<?php

namespace App\Utils;

use Doctrine\ORM\EntityManagerInterface;

class EagerService
{
    public function __construct(protected EntityManagerInterface $em)
    {
     ;
    }

    public function resolveIncludes($class, $alias, $qb = null, $includes = [])
    {
        foreach($includes as $firstIndex => $include) {
            if (strpos($include, '.') !== false) {
                $relations = explode('.', $include);
            } else {
                $relations = [$include];
            }
            // Parse includes into an array
            // The next relation is owned by the previous one, so we keep track of the previous relation
            $previousRelation = $alias;
            $qb = $qb ?? $this->em->getRepository($class)->createQueryBuilder($previousRelation);

            foreach ($relations as $index => $relation) {
                $relationAlias = "{$relation}_{$firstIndex}_{$index}";
                // Add inner joins to the query builder referencing the new relation
                $qb->addSelect($relationAlias);
                $qb->leftJoin("{$previousRelation}.{$relation}", $relationAlias);
                $previousRelation = $relationAlias;
            }
        }

        // Return query builder or the result of the query
        return $qb;
    }
}