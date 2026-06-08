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
        $path = $request->query('path', '');
        $width = (int) $request->query('w', 800);
        $format = $request->query('f', 'webp');

        if ($path === '' || $width < 1) {
            abort(404);
        }

        return $this->images->serve($path, $width, $format);
    }
}
