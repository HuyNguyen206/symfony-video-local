<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class VideoFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $categories = $manager->getRepository(Category::class)->findAll();
        $faker = Factory::create();
        foreach ($categories as $name => $category) {
            for ($i = 1; $i <= random_int(1, 5); $i++) {
                $video = new Video();
                $video->setTitle($faker->words(5, true));
                $video->setPath('https://source.unsplash.com/random/300×300');
                $video->setDuration(random_int(5, 50));
                $category->addVideo($video);
            }
            $manager->persist($category);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
       return [
         CategoryFixture::class
       ];
    }
}
