<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Enums\SubscriptionPrice;
use App\Repository\VideoRepository;
use App\Utils\EagerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('admin')]
class AdminController extends AbstractController
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected ValidatorInterface $validator
    ) {
    }

    #[Route('/', name: 'admin')]
    public function index(SessionInterface $session): Response
    {
        $subscription = $this->getUser()->getSubscription();
        return $this->render('admin/my_profile.html.twig', compact('subscription'));
    }

    #[Route("/payment/process", name: 'payment.process')]
    public function process()
    {
        $subscription = $this->getUser()->getSubscription();
        $subscription->setPaymentStatus('paid');
        $this->entityManager->persist($subscription);
        $this->entityManager->flush();

        return $this->redirectToRoute('admin');
    }

    #[Route('/payment/{plan}', name: 'payment')]
    public function payment(string $plan, SessionInterface $session, Request $request): Response
    {
//        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        if ($request->isMethod('GET')) {
            $session->set('plan', $plan);
            $session->set('price', SubscriptionPrice::fromName($plan)->value);
        }

        return $this->render('front/payment.html.twig');
    }

    #[Route('/categories', name: 'categories', methods: 'GET')]
    public function categories(EntityManagerInterface $entityManager, EagerService $eagerService): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $categories = $entityManager->getRepository(Category::class)->findby(['parentCategory' => null]);
        $results = [];
        foreach ($categories as $category) {
            $qb = $eagerService->resolveIncludes(Category::class, 'cat',
                includes: ['subCategories.subCategories.subCategories.subCategories']);
            $results[] = $qb->where('cat.id = :id')->setParameter('id',
                $category->getId())->getQuery()->getSingleResult();
        }

        return $this->render('admin/categories.html.twig', compact('results'));
    }

    #[Route('/upload-video', name: 'videos.upload')]
    public function upload(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('admin/upload_video.html.twig');
    }

    #[Route('/videos/{page}', name: 'admin.videos.index')]
    public function listVideos(VideoRepository $videoRepository, int $page = 1): Response
    {
        $videoQb = $videoRepository->createQueryBuilder('v')->orderBy('v.title', 'asc');

        $videos = $videoRepository->paginate($page, $videoQb);

        return $this->render('admin/videos.html.twig', compact('videos'));
    }

    #[Route('/categories/{id}', name: 'categories.edit', methods: 'GET')]
    public function editCategory(int $id, EagerService $eagerService): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $categories = $this->entityManager->getRepository(Category::class)->findby(['parentCategory' => null]);
        $results = [];
        foreach ($categories as $categoryCheck) {
            $qb = $eagerService->resolveIncludes(Category::class, 'cat',
                includes: ['subCategories.subCategories.subCategories.subCategories']);
            $results[] = $qb->where('cat.id = :id')->setParameter('id',
                $categoryCheck->getId())->getQuery()->getSingleResult();
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
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $category->setName($request->request->get('name'));
        $category->setParentCategory($this->entityManager->getRepository(Category::class)->find($request->request->get('parent_category_id')));
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        return $this->redirectToRoute('categories.update', ['id' => $category->getId()]);
    }

    #[Route('/categories', name: 'categories.store', methods: 'POST')]
    public function store(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $category = new Category();
        $category->setName($request->request->get('name'));
        $parentId = $request->request->get('parent_category_id');
        if ($parentId) {
            $category->setParentCategory($this->entityManager->getRepository(Category::class)->find($request->request->get('parent_category_id')));
        }
        $errors = $this->validator->validate($category);
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        return $this->redirectToRoute('categories');
    }

    #[Route('/categories/{id}', name: 'categories.delete', methods: 'DELETE')]
    public function delete(Category $category, EagerService $eagerService): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $this->entityManager->remove($category);
        $this->entityManager->flush();

        return $this->redirectToRoute('categories');
    }

    #[Route('/users', name: 'users.index')]
    public function listUsers(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('admin/users.html.twig');
    }
}
