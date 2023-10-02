<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    #[Route('/login', name: 'show-login')]
    public function login(): Response
    {
        return $this->render('auth/login.html.twig',);
    }

    #[Route('/register', name: 'show-register')]
    public function register(): Response
    {
        return $this->render('auth/register.html.twig',);
    }
}
