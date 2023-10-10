<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Enums\InteractiveType;
use App\Entity\User;
use App\Entity\UserInteractiveVideo;
use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Illuminate\Support\Arr;

class VideoFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $categories = $manager->getRepository(Category::class)->findAll();
        $faker = Factory::create();
        foreach ($categories as $name => $category) {
            for ($i = 1; $i <= random_int(1, 30); $i++) {
                $video = new Video();
                $video->setFilename($faker->words(5, true));
                $video->setOriginFilename($faker->words(5, true));
                $video->setDuration(random_int(5, 50));
                $category->addVideo($video);
            }
            $manager->persist($category);
        }
        $manager->flush();

        $videos = $manager->getRepository(Video::class)->findAll();
        $users = $manager->getRepository(User::class)->findAll();
        foreach ($videos as $video) {
            $interactivedUserIds = [];
            for ($i = 1; $i <= random_int(1, 30); $i++) {
                $randomUser = Arr::random($users);
                if (!in_array($randomUser->getId(), $interactivedUserIds)) {
                    $userInteractiveVideo = new UserInteractiveVideo();
                    $userInteractiveVideo->setVideo($video);
                    $userInteractiveVideo->setUser($randomUser);
                    $userInteractiveVideo->setType(InteractiveType::IS_LIKE->value);
                    $manager->persist($userInteractiveVideo);
                    $interactivedUserIds[] = $randomUser->getId();
                }
            }

            for ($i = 1; $i <= random_int(1, 30); $i++) {
                $randomUser = Arr::random($users);
                if (!in_array($randomUser->getId(), $interactivedUserIds)) {
                    $userInteractiveVideo = new UserInteractiveVideo();
                    $userInteractiveVideo->setVideo($video);
                    $userInteractiveVideo->setUser($randomUser);
                    $userInteractiveVideo->setType(InteractiveType::IS_DISLIKE->value);
                    $manager->persist($userInteractiveVideo);
                    $interactivedUserIds[] = $randomUser->getId();
                }
            }
            $manager->persist($video);
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
