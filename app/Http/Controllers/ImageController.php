<?php

namespace App\Http\Controllers;

use App\Services\ImageService;

class ImageController extends Controller
{
    protected $imageService;

    public function __construct(
        ImageService $imageService
    ) {
        $this->imageService = $imageService;
    }

    public function get(string $filename)
    {
        return $this->imageService->get($filename);
    }
}
