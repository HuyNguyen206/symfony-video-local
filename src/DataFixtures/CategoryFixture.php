<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;

class CategoryFixture extends BaseFixture
{
    public function load(ObjectManager $manager): void
    {
        $mainCategory = [
            'Graphic Card' => ['AMD', 'Nvidia'],
            'Mouse' => ['HP', 'Razor'],
            'Keyboard' => ['Boston', 'China town'],
            'Monitor' => ['LG', 'Samsung'],
        ];

        foreach ($mainCategory as $name => $subCategories) {
            $category1 = new Category();
            $category1->setName($name);
            foreach ($subCategories as $subCategoryName) {
                $subCategory = new Category();
                $subCategory->setName($subCategoryName);
                $category1->addSubCategory($subCategory);
            }
            $manager->persist($category1);
        }

        $manager->flush();
    }
}
