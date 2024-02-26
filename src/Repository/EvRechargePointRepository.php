<?php

namespace App\Repository;

use App\Entity\EvRechargePoint;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EvRechargePoint>
 *
 * @method EvRechargePoint|null find($id, $lockMode = null, $lockVersion = null)
 * @method EvRechargePoint|null findOneBy(array $criteria, array $orderBy = null)
 * @method EvRechargePoint[]    findAll()
 * @method EvRechargePoint[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EvRechargePointRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EvRechargePoint::class);
    }

    //    /**
    //     * @return EvRechargePoint[] Returns an array of EvRechargePoint objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?EvRechargePoint
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
