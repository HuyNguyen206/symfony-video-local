<?php

namespace App\Controller;

use App\Entity\Category;
use App\Utils\EagerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('admin')]
class AdminController extends AbstractController
{
    public function __construct(protected EntityManagerInterface $entityManager)
    {
    }

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
    public function categories(EntityManagerInterface $entityManager, EagerService $eagerService): Response
    {
        $categories = $entityManager->getRepository(Category::class)->findby(['parentCategory' => null]);
        $results = [];
        foreach ($categories as $category) {
            $qb = $eagerService->resolveIncludes(Category::class, 'cat', includes: ['subCategories.subCategories.subCategories.subCategories']);
            $results[] = $qb->where('cat.id = :id')->setParameter('id', $category->getId())->getQuery()->getSingleResult();
        }

        return $this->render('admin/categories.html.twig', compact('results'));
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

    #[Route('/categories/{id}', name: 'categories.edit')]
    public function editCategory(int $id, EagerService $eagerService): Response
    {
        $categories = $this->entityManager->getRepository(Category::class)->findby(['parentCategory' => null]);
        $results = [];
        foreach ($categories as $categoryCheck) {
            $qb = $eagerService->resolveIncludes(Category::class, 'cat', includes: ['subCategories.subCategories.subCategories.subCategories']);
            $results[] = $qb->where('cat.id = :id')->setParameter('id', $categoryCheck->getId())->getQuery()->getSingleResult();
        }

        $category = $eagerService->resolveIncludes(Category::class, 'cat', includes: ['parentCategory'])
            ->where('cat.id = :id')
            ->setParameter('id', $id)
            ->getQuery()->getSingleResult();


//        $category = $this->entityManager->getRepository(Category::class)->createQueryBuilder('cat')->select(['cat.*', 'T_IDENTIFIER(cat.parentCategory)'])
//           ->where('cat.id = :id')
//           ->setParameter('id', $id)
//           ->getQuery()->getSingleResult();
//        dd($category);
        return $this->render('admin/edit_category.html.twig', compact('category', 'results'));
    }

    #[Route('/categories/{id}', name: 'categories.update', methods: 'PUT')]
    public function update(Category $category, EagerService $eagerService, Request $request): Response
    {
        $category->setName($request->request->name);
        $category->parent_category_id = $request->request->parent_category_id;
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        return $this->redirectToRoute('categories.update', ['id' => $category->getId()]);
    }

    #[Route('/categories/{id}', name: 'categories.delete', methods: 'DELETE')]
    public function delete(Category $category, EagerService $eagerService): Response
    {
        $this->entityManager->remove($category);
        $this->entityManager->flush();

        return $this->redirectToRoute('categories');
    }

    #[Route('/users', name: 'users.index')]
    public function listUsers(): Response
    {
        return $this->render('admin/users.html.twig');
    }
}
