<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Video;
use App\Repository\CategoryRepository;
use App\Repository\VideoRepository;
use App\Utils\CategoryTreeFrontPage;
use App\Utils\EagerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VideoController extends AbstractController
{


    #[Route('categories/{name}/{categoryId}/videos/{page}', name: 'categories.videos', defaults: ['page' => 1], methods: 'GET')]
    public function index(
        string $name,
        int $categoryId,
        int $page,
        CategoryTreeFrontPage $categoryTreeFrontPage,
        EagerService $eagerService,
        EntityManagerInterface $entityManager,
        VideoRepository $videoRepository,
        Request $request
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
        $sortMethod = $request->query->get('sort_by', 'asc');
        $qb = $eagerService->resolveIncludes(Category::class, 'cat', includes: [$nestedCategory, 'parentCategory']);
        $category = $qb->where('cat.id = :categoryId')
            ->setParameter('categoryId', $categoryId)
            ->getQuery()
            ->getSingleResult();

        $videoQb = $entityManager->getRepository(Video::class)->createQueryBuilder('v')->where('v.category = :category')
        ->setParameter('category', $category)->orderBy('v.title', $sortMethod);

        $videos = $videoRepository->paginate($page, $videoQb);

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
        return $this->render('video/index.html.twig', compact('category', 'nameCurrentCategory', 'videos'));
    }

    #[Route('/videos/{id}', name: 'videos.show', requirements: ['id' => '\d+'])]
    public function show($id = null): Response
    {
        return $this->render('video/show.html.twig');
    }

    #[Route('/videos/search/{page?}', name: 'videos.search', defaults: ['page' => 1], methods: 'GET')]
    public function search(VideoRepository $videoRepository, Request $request, $page): Response
    {
        $sortMethod = $request->query->get('sort_by', 'asc');
        $videoQb = $videoRepository->createQueryBuilder('v')->where('v.title like :search')
            ->setParameter('search', '%'.trim($request->query->get('search')).'%')->orderBy('v.title', $sortMethod);

        $videos = $videoRepository->paginate($page, $videoQb);

        return $this->render('video/search-results.html.twig', compact('videos'));
    }

    #[Route('/pricing', name: 'pricing')]
    public function pricing($id = null): Response
    {
        return $this->render('pricing.html.twig');
    }
}
