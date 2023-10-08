<?php

namespace App\DataFixtures;

use App\Entity\Enums\SubscriptionType;
use App\Entity\Subscription;
use App\Entity\User;
use Carbon\Carbon;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SubscriptionFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $users = $manager->getRepository(User::class)->findAll();
        foreach ($users as $user) {
            $subscription = new Subscription();
            $subscription->setPlan(SubscriptionType::randomType());
            $subscription->setPaymentStatus('paid');
            $subscription->setFreePlanUsed(false);
            $subscription->setValidTo(Carbon::now()->addDays(random_int(1, 10)));
            $subscription->setUser($user);
            $manager->persist($subscription);
        }
        $manager->flush();
    }
}
