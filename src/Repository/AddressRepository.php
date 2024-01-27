<?php

namespace App\Repository;

use App\Entity\Address;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Address>
 *
 * @method Address|null find($id, $lockMode = null, $lockVersion = null)
 * @method Address|null findOneBy(array $criteria, array $orderBy = null)
 * @method Address[]    findAll()
 * @method Address[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AddressRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Address::class);
    }

    /**
     * @return float|int|mixed|string [] Returns an array
     */
    public function getPostalCodes()
    {
        $query = $this->createQueryBuilder('a')
            ->select('a.postalCode as code')
            ->orderBy('a.postalCode', 'ASC')
            ->groupBy('a.postalCode')
            ->getQuery();

        return $query->getArrayResult();
    }

    /**
     * @return float|int|mixed|string [] Returns an array
     */
    public function getCities()
    {
        $query = $this->createQueryBuilder('a')
            ->select('LOWER(MAX(a.city)) as name, a.postalCode as code')
            ->orderBy('LOWER(MAX(a.city))', 'ASC')
            ->groupBy('a.postalCode')
            ->getQuery();

        return $query->getArrayResult();
    }
}
