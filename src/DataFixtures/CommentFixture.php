<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\User;
use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Illuminate\Support\Arr;

class CommentFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $videos = $manager->getRepository(Video::class)->findAll();
        $users = $manager->getRepository(User::class)->findAll();

        $faker = Factory::create();
        foreach ($videos as $name => $video) {
            for ($i = 1; $i <= random_int(1, 5); $i++) {
                $comment = new Comment();
                $comment->setContent($faker->words(5, true));
                $comment->setUser(Arr::random($users));
                $video->addComment($comment);
            }
            $manager->persist($video);
        }

        $manager->flush();
    }


    public function getDependencies()
    {
        return [VideoFixture::class];
    }
}
