<?php

namespace App\Http\Controllers;

use App\Services\ImageOptimizationService;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function __construct(private ImageOptimizationService $images) {}

    public function show(Request $request, int $width, string $path)
    {
        $format = $request->query('f', 'webp');

        return $this->images->serve($path, $width, $format);
    }
}
