<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VideoController extends AbstractController
{
    #[Route('categories/{name}/{id}/videos', name: 'categories.videos')]
    public function index(string $name, Category $category): Response
    {
        $videos = $category->getVideos();

        return $this->render('video/index.html.twig', compact('videos'));
    }

    #[Route('/videos/{?id}', name: 'videos.show')]
    public function show($id = null): Response
    {
        return $this->render('video/show.html.twig');
    }

    #[Route('/videos/search', name: 'videos.search')]
    public function search($id = null): Response
    {
        return $this->render('video/search-results.html.twig');
    }

    #[Route('/pricing', name: 'pricing')]
    public function pricing($id = null): Response
    {
        return $this->render('pricing.html.twig');
    }
}
