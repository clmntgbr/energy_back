<?php

namespace App\Repository;

use App\Entity\EnergyStationBrand;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EnergyStationBrand>
 *
 * @method EnergyStationBrand|null find($id, $lockMode = null, $lockVersion = null)
 * @method EnergyStationBrand|null findOneBy(array $criteria, array $orderBy = null)
 * @method EnergyStationBrand[]    findAll()
 * @method EnergyStationBrand[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnergyStationBrandRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EnergyStationBrand::class);
    }

//    /**
//     * @return EnergyStationBrand[] Returns an array of EnergyStationBrand objects
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

//    public function findOneBySomeField($value): ?EnergyStationBrand
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
