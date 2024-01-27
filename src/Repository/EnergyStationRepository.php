<?php

namespace App\Repository;

use App\Entity\EnergyStation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EnergyStation>
 *
 * @method EnergyStation|null find($id, $lockMode = null, $lockVersion = null)
 * @method EnergyStation|null findOneBy(array $criteria, array $orderBy = null)
 * @method EnergyStation[]    findAll()
 * @method EnergyStation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnergyStationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EnergyStation::class);
    }

//    /**
//     * @return EnergyStation[] Returns an array of EnergyStation objects
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

//    public function findOneBySomeField($value): ?EnergyStation
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
