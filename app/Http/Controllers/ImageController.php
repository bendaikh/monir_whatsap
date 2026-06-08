<?php

namespace App\Http\Controllers;

use App\Services\ImageOptimizationService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ImageController extends Controller
{
    public function __construct(private ImageOptimizationService $images) {}

    public function show(Request $request): Response
    {
        try {
            $path = $request->query('path', '');
            $width = (int) $request->query('w', 800);
            $format = $request->query('f', 'webp');

            if ($path === '' || $width < 1) {
                abort(404);
            }

            return $this->images->serve($path, $width, $format);
        } catch (\Throwable $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) {
                throw $e;
            }

            \Illuminate\Support\Facades\Log::error('ImageController error: ' . $e->getMessage(), [
                'exception' => $e,
                'url' => $request->fullUrl()
            ]);
            
            // Fallback to original if possible, otherwise 404
            try {
                return $this->images->serve($request->query('path', ''), 0); // 0 width means original
            } catch (\Throwable $e2) {
                abort(404);
            }
        }
    }
}
