<?php

namespace App\Repository;

use App\Entity\EvInformation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EvInformation>
 *
 * @method EvInformation|null find($id, $lockMode = null, $lockVersion = null)
 * @method EvInformation|null findOneBy(array $criteria, array $orderBy = null)
 * @method EvInformation[]    findAll()
 * @method EvInformation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EvInformationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EvInformation::class);
    }

//    /**
//     * @return EvInformation[] Returns an array of EvInformation objects
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

//    public function findOneBySomeField($value): ?EvInformation
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
