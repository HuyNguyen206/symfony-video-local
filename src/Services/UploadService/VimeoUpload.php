<?php

namespace App\Services\UploadService;

use App\Entity\Video;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class VimeoUpload implements UploadInterface
{
    public function __construct(protected Security $security)
    {
        $this->vimeoToken = $this->security->getUser()->getVimeoApiKey();
    }

    public function upload(UploadedFile $file, Video $video)
    {
        // TODO: Implement upload() method.
    }

    public function delete(string $filename)
    {
        // TODO: Implement delete() method.
    }
}