<?php

namespace App\Repository;

use App\Entity\EnergyPrice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EnergyPrice>
 *
 * @method EnergyPrice|null find($id, $lockMode = null, $lockVersion = null)
 * @method EnergyPrice|null findOneBy(array $criteria, array $orderBy = null)
 * @method EnergyPrice[]    findAll()
 * @method EnergyPrice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnergyPriceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EnergyPrice::class);
    }

//    /**
//     * @return EnergyPrice[] Returns an array of EnergyPrice objects
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

//    public function findOneBySomeField($value): ?EnergyPrice
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
