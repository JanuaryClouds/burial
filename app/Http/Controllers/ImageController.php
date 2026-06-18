<?php

namespace App\Http\Controllers;

use App\Services\ImageService;

class ImageController extends Controller
{
    public function __construct(
        protected ImageService $imageService
    ) {}

    public function get(string $filename)
    {
        return $this->imageService->get($filename);
    }
}
