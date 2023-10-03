<?php

namespace App\Utils\AbstractClass;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class CategoryTreeAbstract
{
    public $categoriesFromDB;
    public $tree;
    public static $entityRepository;

    public function __construct(public EntityManagerInterface $entityManager, private UrlGeneratorInterface $generator)
    {
        $this->categoriesFromDB = $this->getCagegories();
    }

    abstract public function getCategoryList();

    private function getCagegories()
    {
        if (self::$entityRepository) {
            return self::$entityRepository;
        }

        $categories = $this->entityManager->getRepository(Category::class)->findAll();

        return self::$entityRepository = $categories;
    }

    public function buildTree(Category &$parent) {
        $childrens = $parent->getSubCategories();
        if (count($childrens)) {
            $parent->childrens = $childrens;
            foreach ($parent->childrens as &$child) {
                $this->buildTree($child);
            }
        }
    }
}