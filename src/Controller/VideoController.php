<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\UserInteractiveVideo;
use App\Entity\Video;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\VideoRepository;
use App\Utils\CategoryTreeFrontPage;
use App\Utils\EagerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class VideoController extends AbstractController
{
    public function __construct(protected EntityManagerInterface $entityManager)
    {

    }

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
            ->addSelect('count(DISTINCT(comments.id)) as commentCount')
            ->setParameter('category', $category)
            ->leftJoin('v.comments', 'comments')
            ->groupBy('v.id')
            ->orderBy('v.title', $sortMethod);

        $videos = $videoRepository->paginate($page, $videoQb);
        $videoIds = collect($videos)->map(function ($video){
           return $video[0]->getId();
        })->toArray();


        $videoInteractions = collect($entityManager->getRepository(UserInteractiveVideo::class)
            ->createQueryBuilder('uiv')
            ->select('v.id',
                'SUM(case when uiv.type = 1 then 1 else 0 end) as likeCount',
                'SUM(case when uiv.type = 0 then 1 else 0 end) as dislikeCount',
                'SUM(case when uiv.user = :userId and uiv.type = 1 then 1 else 0 end) as isLikeVideo',
                'SUM(case when uiv.user = :userId and uiv.type = 0 then 1 else 0 end) as isDislikeVideo',
            )
            ->leftJoin('uiv.video', 'v')
            ->where('uiv.video in (:videoIds)')
            ->setParameter('videoIds', $videoIds)
            ->setParameter('userId', $this->getUser()->getId())
            ->groupBy('v.id')
            ->getQuery()->getResult())->keyBy('id');

        $videosWithCount = collect($videos->getItems())->map(function ($video) use ($videoInteractions){
             $video += $videoInteractions[$video[0]->getId()];
             return $video;
        });
        $videos->setItems($videosWithCount->all());
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
    public function show(Video $video, CommentRepository $commentRepository, EagerService $eagerService): Response
    {
//        $comments = $commentRepository->findBy(['video_id' => $id]);
        $qb = $eagerService->resolveIncludes(Comment::class, 'com', includes: ['user']);

        $comments = $qb->where('com.video = :video')
            ->setParameter('video', $video)
            ->getQuery()
            ->getResult();
//        dd($comments);
        return $this->render('video/show.html.twig', compact('comments', 'video'));
    }

    #[Route('videos/{id}/comments', methods: ['POST'], name: 'videos.comments.store')]
    public function storeComment(Video $video, Request $request, ValidatorInterface $validator)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $comment = new Comment;
        $comment->setContent($request->request->get('content'));
        $comment->setUser($this->getUser());
        $errors = $validator->validate($comment);
        $route = $request->headers->get('referer');
        if (count($errors) > 0) {
            /*
             * Uses a __toString method on the $errors variable which is a
             * ConstraintViolationList object. This gives us a nice string
             * for debugging.
             */
            $this->addFlash('content_error', 'Content can not blank');

            $route = $request->headers->get('referer');

            return $this->redirect($route);
        }

        $video->addComment($comment);
        $this->entityManager->persist($video);
        $this->entityManager->flush();

        return $this->redirect($route);
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

    #[Route('/videos/{id}/react/{isLike}', methods: ['GET'], name: 'video.react')]
    public function interactVideo(Video $video, int $isLike, Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $route = $request->headers->get('referer');
        $userInteractiveVideo = $this->entityManager->getRepository(UserInteractiveVideo::class)->findOneBy(['video' => $video, 'user' => $this->getUser()]);
//        if (!$userInteractiveVideo) {
//            $userInteractiveVideo = new UserInteractiveVideo;
//            $userInteractiveVideo->setVideo($video);
//            $userInteractiveVideo->setUser($this->getUser());
//        }

        if ($userInteractiveVideo->isLiked()) {
            if ($isLike) {
                return $this->redirect($route);
            }
            $userInteractiveVideo->setType($isLike);
        } else {
            if (!$isLike) {
                return $this->redirect($route);
            }
            $userInteractiveVideo->setType($isLike);
        }
        $this->entityManager->persist($userInteractiveVideo);
        $this->entityManager->flush();

        return $this->redirect($route);
    }
}
