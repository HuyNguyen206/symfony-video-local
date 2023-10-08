<?php

namespace App\Controller;

use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubscriptionController extends AbstractController
{
    public function __construct(public EntityManagerInterface $entityManager)
    {
    }

    #[Route('/subscriptions/store', name: 'subscription.store')]
    public function store(): Response
    {
        return $this->render('subscription/index.html.twig', [
            'controller_name' => 'SubscriptionController',
        ]);
    }

    #[Route('/subscriptions/cancel', name: 'subscription.cancel')]
    public function cancel(Request $request): Response
    {
        $subscription = $this->getUser()->getSubscription();
        $subscription->setValidTo(Carbon::now());
        $subscription->setPaymentStatus(null);
        $subscription->setPlan('cancel');
        $this->entityManager->persist($subscription);
        $this->entityManager->flush();
        return $this->redirectToRoute('admin');
    }
}
