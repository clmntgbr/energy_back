<?php

namespace App\Repository;

use App\Entity\EnergyService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EnergyService>
 *
 * @method EnergyService|null find($id, $lockMode = null, $lockVersion = null)
 * @method EnergyService|null findOneBy(array $criteria, array $orderBy = null)
 * @method EnergyService[]    findAll()
 * @method EnergyService[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnergyServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EnergyService::class);
    }

//    /**
//     * @return EnergyService[] Returns an array of EnergyService objects
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

//    public function findOneBySomeField($value): ?EnergyService
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
