<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class ImageOptimizationService
{
    private const MAX_WIDTH = 1920;

    private const JPEG_QUALITY = 82;

    private const WEBP_QUALITY = 82;

    public function isAvailable(): bool
    {
        return extension_loaded('gd');
    }

    public function url(?string $imageUrl, int $width = 800, string $format = 'webp'): ?string
    {
        if ($imageUrl === null || $imageUrl === '') {
            return null;
        }

        if (str_contains($imageUrl, 'via.placeholder.com')) {
            return $imageUrl;
        }

        $path = $this->extractStoragePath($imageUrl);
        if ($path === null) {
            return $imageUrl;
        }

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (in_array($ext, ['gif', 'svg'], true)) {
            return $imageUrl;
        }

        if (! $this->isAvailable()) {
            return $this->originalUrl($path);
        }

        $width = max(100, min($width, self::MAX_WIDTH));

        $query = [
            'w' => $width,
            'path' => $path,
        ];

        if ($format !== 'webp') {
            $query['f'] = $format;
        }

        return route('image.optimized', $query);
    }

    public function srcset(?string $imageUrl, array $widths = [400, 640, 800, 1200]): ?string
    {
        if ($imageUrl === null || $imageUrl === '') {
            return null;
        }

        $parts = [];
        foreach ($widths as $width) {
            $url = $this->url($imageUrl, $width);
            if ($url) {
                $parts[] = "{$url} {$width}w";
            }
        }

        return $parts ? implode(', ', $parts) : null;
    }

    public function serve(string $path, int $width, string $format = 'webp'): Response
    {
        $path = ltrim(str_replace(['..', '\\'], '', $path), '/');
        $sourcePath = storage_path('app/public/'.$path);

        if (! is_file($sourcePath) || ! is_readable($sourcePath)) {
            abort(404);
        }

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (in_array($ext, ['gif', 'svg'], true)) {
            return $this->fileResponse($sourcePath, $ext);
        }

        if (! $this->isAvailable()) {
            return $this->fileResponse($sourcePath, $ext);
        }

        try {
            $width = max(1, min($width, self::MAX_WIDTH));
            $outputExt = $this->resolveOutputFormat($ext, $format);
            $cachePath = $this->buildCachePath($path, $width, $outputExt);

            if ($cachePath && is_file($cachePath) && filemtime($cachePath) >= filemtime($sourcePath)) {
                return $this->fileResponse($cachePath, $outputExt);
            }

            $imageInfo = @getimagesize($sourcePath);
            if ($imageInfo && $imageInfo[0] > 0 && $imageInfo[0] <= $width && $this->sameFormat($ext, $outputExt)) {
                return $this->fileResponse($sourcePath, $ext);
            }

            $image = $this->loadImage($sourcePath, $ext);
            if ($image === null) {
                return $this->fileResponse($sourcePath, $ext);
            }

            $resized = $this->resize($image, $width);
            imagedestroy($image);

            if ($cachePath) {
                $saved = $this->saveImage($resized, $cachePath, $outputExt);
                imagedestroy($resized);

                if ($saved && is_file($cachePath)) {
                    return $this->fileResponse($cachePath, $outputExt);
                }
            } else {
                $saved = $this->streamImage($resized, $outputExt);
                imagedestroy($resized);

                if ($saved instanceof Response) {
                    return $saved;
                }
            }

            return $this->fileResponse($sourcePath, $ext);
        } catch (\Throwable $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) {
                throw $e;
            }

            Log::warning('Image optimization failed, serving original.', [
                'path' => $path,
                'width' => $width,
                'format' => $format,
                'error' => $e->getMessage(),
            ]);

            return $this->fileResponse($sourcePath, $ext);
        }
    }

    public function optimizeUploadedFile(string $storedPath): string
    {
        if (! $this->isAvailable()) {
            return $storedPath;
        }

        $sourcePath = storage_path('app/public/'.$storedPath);
        if (! is_file($sourcePath)) {
            return $storedPath;
        }

        $ext = strtolower(pathinfo($storedPath, PATHINFO_EXTENSION));
        if (in_array($ext, ['gif', 'svg'], true)) {
            return $storedPath;
        }

        try {
            $image = $this->loadImage($sourcePath, $ext);
            if ($image === null) {
                return $storedPath;
            }

            [$width] = $this->dimensions($image);
            if ($width <= self::MAX_WIDTH) {
                imagedestroy($image);

                return $storedPath;
            }

            $resized = $this->resize($image, self::MAX_WIDTH);
            imagedestroy($image);

            $outputExt = $ext === 'png' ? 'png' : 'jpg';
            $this->saveImage($resized, $sourcePath, $outputExt);
            imagedestroy($resized);
        } catch (\Throwable $e) {
            Log::warning('Upload image optimization failed.', [
                'path' => $storedPath,
                'error' => $e->getMessage(),
            ]);
        }

        return $storedPath;
    }

    private function originalUrl(string $path): string
    {
        return '/storage/'.ltrim($path, '/');
    }

    private function buildCachePath(string $path, int $width, string $outputExt): ?string
    {
        $cacheDir = storage_path('framework/cache/image-optimizer');

        if (! is_dir($cacheDir) && ! @mkdir($cacheDir, 0755, true) && ! is_dir($cacheDir)) {
            return null;
        }

        if (! is_writable($cacheDir)) {
            return null;
        }

        $cacheKey = md5($path.'|'.$width.'|'.$outputExt).'.'.$outputExt;

        return $cacheDir.'/'.$cacheKey;
    }

    private function extractStoragePath(string $imageUrl): ?string
    {
        $path = trim($imageUrl);

        if (preg_match('#/storage/(.+)$#i', $path, $matches)) {
            return $matches[1];
        }

        if (str_starts_with($path, 'storage/')) {
            return substr($path, strlen('storage/'));
        }

        if (! preg_match('#^https?://#i', $path) && ! str_starts_with($path, '/')) {
            return ltrim($path, '/');
        }

        return null;
    }

    private function resolveOutputFormat(string $sourceExt, string $requestedFormat): string
    {
        if ($requestedFormat === 'jpg' || $requestedFormat === 'jpeg') {
            return 'jpg';
        }

        if ($requestedFormat === 'png') {
            return 'png';
        }

        if ($requestedFormat === 'webp' && function_exists('imagewebp')) {
            return 'webp';
        }

        return in_array($sourceExt, ['png', 'webp'], true) ? $sourceExt : 'jpg';
    }

    private function sameFormat(string $sourceExt, string $outputExt): bool
    {
        if ($sourceExt === $outputExt) {
            return true;
        }

        return in_array($sourceExt, ['jpg', 'jpeg'], true) && $outputExt === 'jpg';
    }

    private function loadImage(string $path, string $ext): ?\GdImage
    {
        $image = match ($ext) {
            'jpg', 'jpeg' => @imagecreatefromjpeg($path),
            'png' => @imagecreatefrompng($path),
            'webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
            default => false,
        };

        return $image instanceof \GdImage ? $image : null;
    }

    private function dimensions(\GdImage $image): array
    {
        return [imagesx($image), imagesy($image)];
    }

    private function resize(\GdImage $image, int $maxWidth): \GdImage
    {
        [$width, $height] = $this->dimensions($image);

        if ($width < 1 || $height < 1) {
            throw new \RuntimeException('Invalid image dimensions.');
        }

        $newWidth = min($width, $maxWidth);
        $newHeight = max(1, (int) round($height * ($newWidth / $width)));

        $canvas = imagecreatetruecolor($newWidth, $newHeight);
        if ($canvas === false) {
            throw new \RuntimeException('Failed to create image canvas.');
        }

        if ($newWidth < $width) {
            imagealphablending($canvas, false);
            imagesavealpha($canvas, true);
        }

        imagecopyresampled($canvas, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        return $canvas;
    }

    private function saveImage(\GdImage $image, string $path, string $format): bool
    {
        $dir = dirname($path);
        if (! is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        return match ($format) {
            'png' => @imagepng($image, $path, 6),
            'webp' => function_exists('imagewebp') ? @imagewebp($image, $path, self::WEBP_QUALITY) : false,
            default => @imagejpeg($image, $path, self::JPEG_QUALITY),
        };
    }

    private function streamImage(\GdImage $image, string $format): ?Response
    {
        ob_start();

        $written = match ($format) {
            'png' => @imagepng($image, null, 6),
            'webp' => function_exists('imagewebp') ? @imagewebp($image, null, self::WEBP_QUALITY) : false,
            default => @imagejpeg($image, null, self::JPEG_QUALITY),
        };

        $contents = ob_get_clean();

        if (! $written || $contents === false || $contents === '') {
            return null;
        }

        $mime = match ($format) {
            'png' => 'image/png',
            'webp' => 'image/webp',
            default => 'image/jpeg',
        };

        return response($contents, 200, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=31536000, immutable',
            'Vary' => 'Accept',
        ]);
    }

    private function fileResponse(string $path, string $format): Response
    {
        if (! is_file($path) || ! is_readable($path)) {
            Log::error('Image file not found or not readable for response.', ['path' => $path]);
            abort(404);
        }

        $mime = match ($format) {
            'png' => 'image/png',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'webp' => 'image/webp',
            'jpeg', 'jpg' => 'image/jpeg',
            default => 'image/jpeg',
        };

        try {
            return response()->file($path, [
                'Content-Type' => $mime,
                'Cache-Control' => 'public, max-age=31536000, immutable',
                'Vary' => 'Accept',
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to create file response.', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            abort(500);
        }
    }
}
