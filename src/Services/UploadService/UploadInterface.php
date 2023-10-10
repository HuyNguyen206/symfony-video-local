<?php

namespace App\Services\UploadService;

use App\Entity\Video;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface UploadInterface
{
    public function upload(UploadedFile $file, Video $video);
    public function delete(string $filename);
}