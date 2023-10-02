<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('admin')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/my_profile.html.twig');
    }

    #[Route('/payment', name: 'payment')]
    public function payment(): Response
    {
        return $this->render('front/payment.html.twig');
    }

    #[Route('/categories', name: 'categories')]
    public function categories(): Response
    {
        return $this->render('admin/categories.html.twig');
    }

    #[Route('/upload-video', name: 'videos.upload')]
    public function upload(): Response
    {
        return $this->render('admin/upload_video.html.twig');
    }

    #[Route('/videos', name: 'admin.videos.index')]
    public function listVideos(): Response
    {
        return $this->render('admin/videos.html.twig');
    }

    #[Route('/edit-category', name: 'categories.edit')]
    public function editCategory(): Response
    {
        return $this->render('admin/edit_category.html.twig');
    }

    #[Route('/users', name: 'users.index')]
    public function listUsers(): Response
    {
        return $this->render('admin/users.html.twig');
    }
}
