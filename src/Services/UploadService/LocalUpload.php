<?php

namespace App\Services\UploadService;

use App\Entity\Category;
use App\Entity\Video;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class LocalUpload implements UploadInterface
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected SluggerInterface $slugger,
//        protected ParameterBagInterface $parameterBag,
        protected string $storeDirectory,
        protected Filesystem $filesystem
    )
    {
    }

    public function upload(UploadedFile $videoFile, Video $video)
    {
        $originalFilename = pathinfo($videoFile->getClientOriginalName(), PATHINFO_FILENAME);
        $originalFilename = preg_replace('/[^A-Za-z0-9]+/','',$originalFilename);
        // this is needed to safely include the file name as part of the URL
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid('_', true).'.'.$videoFile->guessExtension();

        // Move the file to the directory where brochures are stored
        try {
            $videoFile->move(
//                $this->parameterBag->get('videos_directory'),
                $this->storeDirectory,
                $newFilename
            );
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        // updates the 'brochureFilename' property to store the PDF file name
        // instead of its contents
        $video->setFilename($newFilename);
//        $video->setCategory($this->entityManager->getRepository(Category::class)->findOneBy([]));
        $video->setOriginFilename($originalFilename);
        $this->entityManager->persist($video);
        $this->entityManager->flush();
    }

    public function delete(string $filePath)
    {
//        try {
            $this->filesystem->remove($filePath);
//        }catch (IOExceptionInterface $exception) {
//            $logger = new Logger();
//            $logger->error($exception->getMessage());
//        }
        return true;
    }
}