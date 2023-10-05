<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Support\Arr;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserFixture extends Fixture
{
    protected Generator $faker;

    public function __construct(protected UserPasswordHasherInterface $hasher)
    {
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $user = new User();
            $user->setName($this->faker->name());
            $user->setPassword($this->hasher->hashPassword($user, 'password'));
            $user->setEmail($this->faker->unique()->safeEmail());
            $user->setRoles(Arr::random([['ROLE_ADMIN'], ['ROLE_USER']]));
            $manager->persist($user);
        }
        $manager->flush();
    }
}
