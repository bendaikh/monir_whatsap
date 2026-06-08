<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ImageOptimizationService
{
    private const CACHE_DIR = 'cache/optimized';

    private const MAX_WIDTH = 1920;

    private const JPEG_QUALITY = 82;

    private const WEBP_QUALITY = 82;

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
        if ($ext === 'gif' || $ext === 'svg') {
            return $imageUrl;
        }

        $width = max(100, min($width, self::MAX_WIDTH));

        return route('image.optimized', [
            'width' => $width,
            'path' => $path,
        ]).($format !== 'webp' ? '?f='.$format : '');
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

    public function serve(string $path, int $width, string $format = 'webp'): BinaryFileResponse
    {
        $path = ltrim(str_replace(['..', '\\'], '', $path), '/');
        $sourcePath = storage_path('app/public/'.$path);

        if (! is_file($sourcePath)) {
            abort(404);
        }

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (in_array($ext, ['gif', 'svg'], true)) {
            return $this->fileResponse($sourcePath, $ext);
        }

        $outputExt = $this->resolveOutputFormat($ext, $format);
        $cacheKey = self::CACHE_DIR.'/'.md5($path.'|'.$width.'|'.$outputExt).'.'.$outputExt;
        $cachePath = storage_path('app/public/'.$cacheKey);

        if (is_file($cachePath) && filemtime($cachePath) >= filemtime($sourcePath)) {
            return $this->fileResponse($cachePath, $outputExt);
        }

        File::ensureDirectoryExists(dirname($cachePath));

        $image = $this->loadImage($sourcePath, $ext);
        if ($image === null) {
            return $this->fileResponse($sourcePath, $ext);
        }

        $resized = $this->resize($image, $width);
        imagedestroy($image);

        $saved = $this->saveImage($resized, $cachePath, $outputExt);
        imagedestroy($resized);

        if (! $saved) {
            return $this->fileResponse($sourcePath, $ext);
        }

        return $this->fileResponse($cachePath, $outputExt);
    }

    public function optimizeUploadedFile(string $storedPath): string
    {
        $sourcePath = storage_path('app/public/'.$storedPath);
        if (! is_file($sourcePath)) {
            return $storedPath;
        }

        $ext = strtolower(pathinfo($storedPath, PATHINFO_EXTENSION));
        if (in_array($ext, ['gif', 'svg'], true)) {
            return $storedPath;
        }

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

        return $storedPath;
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

    private function loadImage(string $path, string $ext): ?\GdImage
    {
        return match ($ext) {
            'jpg', 'jpeg' => @imagecreatefromjpeg($path) ?: null,
            'png' => @imagecreatefrompng($path) ?: null,
            'webp' => function_exists('imagecreatefromwebp') ? (@imagecreatefromwebp($path) ?: null) : null,
            default => null,
        };
    }

    private function dimensions(\GdImage $image): array
    {
        return [imagesx($image), imagesy($image)];
    }

    private function resize(\GdImage $image, int $maxWidth): \GdImage
    {
        [$width, $height] = $this->dimensions($image);
        $newWidth = min($width, $maxWidth);
        $newHeight = (int) round($height * ($newWidth / $width));

        $canvas = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        imagecopyresampled($canvas, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        return $canvas;
    }

    private function saveImage(\GdImage $image, string $path, string $format): bool
    {
        return match ($format) {
            'png' => imagepng($image, $path, 6),
            'webp' => function_exists('imagewebp') ? imagewebp($image, $path, self::WEBP_QUALITY) : false,
            default => imagejpeg($image, $path, self::JPEG_QUALITY),
        };
    }

    private function fileResponse(string $path, string $format): BinaryFileResponse
    {
        $mime = match ($format) {
            'png' => 'image/png',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'webp' => 'image/webp',
            default => 'image/jpeg',
        };

        return response()->file($path, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=31536000, immutable',
            'Vary' => 'Accept',
        ]);
    }
}
