<?php

namespace App\Traits;

use Doctrine\Persistence\ManagerRegistry;
use Illuminate\Support\Str;
use Knp\Component\Pager\PaginatorInterface;

trait Pagination
{
    public function __construct(ManagerRegistry $registry, protected PaginatorInterface $paginator)
    {
        $entityName = Str::of(class_basename($this))->before('Repository')->toString();

        $entityClass = "App\\Entity\\$entityName";

        parent::__construct($registry, $entityClass);
    }

    public function paginate($page, $query, $limit = 5)
    {
        $query->getQuery();

        return $this->paginator->paginate($query, $page, $limit);
    }
}