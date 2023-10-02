<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
    public function __construct(protected EntityManagerInterface $entityManager)
    {
    }

    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('front/index.html.twig');
    }

    public function getCategories()
    {
//        $qb = $this->entityManager->getRepository(Category::class)->createQueryBuilder('cat');
//
//        $categories = $qb->where($qb->expr()->isNull('cat.parentCategory'))->getQuery()->getResult();

        $categories = $this->entityManager->getRepository(Category::class)->findBy(['parentCategory' => null],
            ['name' => 'asc']);

        return $this->render('front/includes/_main_categories.html.twig', compact('categories'));
    }

}
