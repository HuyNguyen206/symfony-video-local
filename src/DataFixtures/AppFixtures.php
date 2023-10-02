<?php

namespace App\DataFixtures;

use Doctrine\Persistence\ObjectManager;

class AppFixtures extends BaseFixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
