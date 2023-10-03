<?php

namespace App\Controller;

use App\Entity\Category;
use App\Utils\CategoryTreeFrontPage;
use App\Utils\EagerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VideoController extends AbstractController
{
    #[Route('categories/{name}/{categoryId}/videos', name: 'categories.videos')]
    public function index(
        string $name,
        int $categoryId,
        CategoryTreeFrontPage $categoryTreeFrontPage,
        EagerService $eagerService
    ): Response {
//        $videos = $category->getVideos();
//        $query = $categoryTreeFrontPage->entityManager->createQuery('SELECT c, s FROM App\Entity\Category c JOIN c.subCategories s where c.id = :id');
//        $query->setParameter('id', $id);
//        $subCategories = $query->getResult(); // array of ForumUser objects with the avatar association loaded
//        $categoryTreeFrontPage->entityManager->createQueryBuilder()->setF
//         $categoryTreeFrontPage->buildTree($category);
//
//        $eagerService = new EagerService();
        $maxNestedCategory = 5;
        $nestedCategory = '';
        for($i = 1; $i <=$maxNestedCategory; $i++) {
            $nestedCategory .= $i !== $maxNestedCategory ? 'subCategories.' : 'subCategories';
        }

        $qb = $eagerService->resolveIncludes(Category::class, 'cat', includes: [$nestedCategory, 'parentCategory', 'videos']);
        $category = $qb->where('cat.id = :categoryId')
            ->setParameter('categoryId', $categoryId)
            ->getQuery()
            ->getSingleResult();

//        $s = $categoryTreeFrontPage->entityManager
//            ->getRepository(Category::class)
//            ->createQueryBuilder('cat')
//            ->select('cat', 'subCategory', 'videos')
//            ->where('cat.id = :categoryId')
//            ->setParameter('categoryId', $categoryId)
//            ->leftJoin('cat.subCategories', 'subCategory')
//            ->leftJoin('cat.videos', 'videos')
//            ->getQuery()
//            ->getResult();
//        dd($category);
        $nameCurrentCategory = $category->getName();
        return $this->render('video/index.html.twig', compact('category', 'nameCurrentCategory'));
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
