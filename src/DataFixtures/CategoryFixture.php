<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;

class CategoryFixture extends BaseFixture
{
    public function load(ObjectManager $manager): void
    {
        $mainCategory = [
            'Graphic Card' => ['AMD', 'Nvidia' => ['RTX 4090', 'RTX 4090 Ti']],
            'Mouse' => ['HP', 'Razor'],
            'Keyboard' => ['Boston', 'China town'],
            'Monitor' => ['LG', 'Samsung'],
        ];

        foreach ($mainCategory as $name => $subCategories) {
            $category1 = new Category();
            $category1->setName($name);
            foreach ($subCategories as $nameSub => $subCategoryName) {
                if (!is_array($subCategoryName)) {
                    $subCategory = new Category();
                    $subCategory->setName($subCategoryName);
                    $category1->addSubCategory($subCategory);
                } else {
                    $category2 = new Category();
                    $category2->setName($nameSub);
                    foreach ($subCategoryName as $grandChild) {
                        $subCategory = new Category();
                        $subCategory->setName($grandChild);
                        $category2->addSubCategory($subCategory);
                    }
                    $category1->addSubCategory($category2);
                    $manager->persist($category2);
                }

            }
            $manager->persist($category1);
        }

        $manager->flush();
    }
}
