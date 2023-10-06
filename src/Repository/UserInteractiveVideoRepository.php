<?php

namespace App\Repository;

use App\Entity\UserInteractiveVideo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserInteractiveVideo>
 *
 * @method UserInteractiveVideo|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserInteractiveVideo|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserInteractiveVideo[]    findAll()
 * @method UserInteractiveVideo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserInteractiveVideoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserInteractiveVideo::class);
    }

//    /**
//     * @return UserInteractiveVideo[] Returns an array of UserInteractiveVideo objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?UserInteractiveVideo
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
