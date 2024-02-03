<?php

namespace App\Repository;

use App\Entity\EnergyStation;
use App\Lists\EnergyStationStatusReference;
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

    public function findEnergyStationsById(): array
    {
        $query = $this->createQueryBuilder('s')
            ->indexBy('s', 's.energyStationId')
            ->select('s.energyStationId, s.hash')
            ->getQuery();

        return $query->getArrayResult();
    }

    public function findEnergyStationsByStatus(string $status): array
    {
        $query = $this->createQueryBuilder('s')
            ->select('s')
            ->where('s.status = :status')
            ->setParameter('status', $status)
            ->getQuery();

        return $query->getResult();
    }

    /** @return EnergyStation[] */
    public function findEnergyStationsExceptByStatus(string $status)
    {
        $query = $this->createQueryBuilder('s')
            ->select('s')
            ->where('s.status != :status')
            ->setParameter('status', $status)
            ->getQuery();

        return $query->getResult();
    }

    /** @return EnergyStation[] */
    public function findEnergyStationByIds(array $energyStationIds)
    {
        $query = $this->createQueryBuilder('s')
            ->select('s')
            ->where('s.energyStationId IN (:ids)')
            ->setParameter('ids', $energyStationIds)
            ->getQuery();

        return $query->getResult();
    }

    public function findEnergyStationsByPlaceIdNotNull(): array
    {
        $query = $this->createQueryBuilder('s')
            ->select('s')
            ->innerJoin('s.googlePlace', 'ss')
            ->where('ss.placeId IS NOT NULL')
            ->getQuery();

        return $query->getResult();
    }

    public function findEnergyStationByPlaceIdAndStatus(string $placeId, string $status): array
    {
        $query = $this->createQueryBuilder('s')
            ->select('s')
            ->innerJoin('s.googlePlace', 'ss')
            ->where('ss.placeId = :placeId AND s.status = :status')
            ->setParameters(
                ['status' => $status, 'placeId' => $placeId]
            )
            ->getQuery();

        return $query->getResult();
    }

    public function findEnergyStationsByPlaceId(string $placeId): array
    {
        $query = $this->createQueryBuilder('s')
            ->select('s')
            ->innerJoin('s.googlePlace', 'ss')
            ->where('ss.placeId = :placeId')
            ->setParameters(
                ['placeId' => $placeId]
            )
            ->getQuery();

        return $query->getResult();
    }

    public function findRandomEnergyStation(): ?EnergyStation
    {
        $query = $this->createQueryBuilder('p')
            ->where('p.status IN (:status)')
            ->setParameter('status', [EnergyStationStatusReference::WAITING_VALIDATION, EnergyStationStatusReference::PLACE_ID_ANOMALY])
            ->orderBy('RAND()')
            ->setMaxResults(1)
            ->getQuery();

        return $query->getOneOrNullResult();
    }

    /** @return EnergyStation[] */
    public function getEnergyStationsMap(string $longitude, string $latitude, string $energyTypeUuid, string $radius, ?string $filterCity, ?string $filterDepartment)
    {
        $energyTypeFilter = $this->createEnergyTypeFilter($energyTypeUuid);
        $cityFilter = $this->createEnergyStationsCitiesFilter($filterCity);
        $departmentFilter = $this->createEnergyStationsDepartmentsFilter($filterDepartment);

        $query = "  SELECT 
                    s.id, 
                    ((6371 * acos(cos(radians($latitude)) * cos(radians(a.latitude)) * cos(radians(a.longitude) - radians($longitude)) + sin(radians($latitude)) * sin(radians(a.latitude))))*1000) as distance,
                    
                    JSON_KEYS(s.last_energy_prices) as energy_types,
                    
                    (SELECT GROUP_CONCAT(gs.name SEPARATOR ', ')
                    FROM energy_station_energy_service gss
                    INNER JOIN energy_service gs ON gss.energy_service_id = gs.id
                    AND gss.energy_station_id = s.id) as energy_services
  
                    FROM energy_station s 
                    INNER JOIN address a ON s.address_id = a.id
                    WHERE a.longitude IS NOT NULL AND a.latitude IS NOT NULL $energyTypeFilter $cityFilter $departmentFilter
                    HAVING `distance` < $radius
                    ORDER BY `distance` ASC LIMIT 250;
        ";

        $statement = $this->getEntityManager()->getConnection()->prepare($query);

        $query = $this->createQueryBuilder('s')
            ->select('s')
            ->where('s.id IN (:ids)')
            ->setParameter('ids', $statement->executeQuery()->fetchFirstColumn())
            ->getQuery();

        return $query->getResult();
    }

    private function createEnergyTypeFilter(string $energyTypeUuid)
    {
        $query = " AND (JSON_KEYS(s.last_energy_prices) LIKE '%$energyTypeUuid%')";

        return $query;
    }

    private function createGasServicesFilter($filters)
    {
        $query = '';
        if (array_key_exists('energy_service', $filters ?? []) && '' !== $filters['energy_service']) {
            $energyServices = explode(',', $filters['energy_service']);
            $query = ' AND (';
            foreach ($energyServices as $energyService) {
                $query .= "`energy_services` LIKE '%" . trim($energyService) . "%' OR ";
            }
            $query = mb_substr($query, 0, -4);
            $query .= ')';
        }

        return $query;
    }

    private function createEnergyStationsCitiesFilter(?string $filterCity)
    {
        $query = '';
        if (null === $filterCity) {
            return $query;
        }
        $query = " AND a.postal_code IN ($filterCity)";

        return $query;
    }

    private function createEnergyStationsDepartmentsFilter(?string $filterDepartment)
    {
        $query = '';
        if (null === $filterDepartment) {
            return $query;
        }
        $query = " AND SUBSTRING(a.postal_code, 1, 2) IN ($filterDepartment)";

        return $query;
    }

    /**
     * @return EnergyStation[]
     *
     * @throws QueryException
     */
    public function getEnergyStationGooglePlaceByPlaceId(?EnergyStation $energyStation)
    {
        $query = $this->createQueryBuilder('s')
            ->select('s')
            ->innerJoin('s.googlePlace', 'ss')
            ->where('ss.placeId = :placeId AND ss.placeId IS NOT NULL')
            ->andWhere('s.uuid != :uuid')
            ->setParameters([
                'placeId' => $energyStation?->getGooglePlace()->getPlaceId(),
                'uuid' => $energyStation?->getUuid(),
            ])
            ->getQuery();

        return $query->getResult();
    }
}
