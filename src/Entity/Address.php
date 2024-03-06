<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\ApiResource\Controller\GetAddressCities;
use App\ApiResource\Controller\GetAddressDepartments;
use App\Entity\Traits\IdentifyTraits;
use App\Repository\AddressRepository;
use App\Service\Uuid;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AddressRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(
            name: 'address_cities',
            uriTemplate: '/address/cities',
            controller: GetAddressCities::class,
            read: false,
            paginationEnabled: false,
            normalizationContext: ['skip_null_values' => false, 'groups' => ['get_address_cities', 'common']],
        ),
        new GetCollection(
            name: 'address_departments',
            uriTemplate: '/address/departments',
            controller: GetAddressDepartments::class,
            read: false,
            paginationEnabled: false,
            normalizationContext: ['skip_null_values' => false, 'groups' => ['get_address_departments', 'common']],
        )
    ]
)]
class Address
{
    use IdentifyTraits;
    use TimestampableEntity;
    use BlameableEntity;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['get_energy_stations_map'])]
    private ?string $vicinity = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['get_energy_stations', 'get_energy_stations_map'])]
    private ?string $street;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['get_energy_stations', 'get_energy_stations_map'])]
    private ?string $number = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['get_energy_stations', 'get_addresses', 'get_energy_stations_map'])]
    private ?string $city;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['get_addresses', 'get_energy_stations_map'])]
    private ?string $region = null;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    #[Groups(['get_energy_stations', 'get_energy_stations_map'])]
    private ?string $postalCode;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    #[Groups(['get_energy_stations_map'])]
    private ?string $country;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    #[Groups(['get_energy_stations', 'get_energy_stations_map'])]
    private ?string $longitude;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    #[Groups(['get_energy_stations', 'get_energy_stations_map'])]
    private ?string $latitude;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $positionStackApiResult;

    public function __construct()
    {
        $this->uuid = Uuid::v4();
    }

    public function __toString()
    {
        return $this->vicinity ?? $this->street;
    }

    public function getVicinity(): ?string
    {
        return $this->vicinity;
    }

    public function setVicinity(?string $vicinity): static
    {
        $this->vicinity = $vicinity;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): static
    {
        $this->street = $street;

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(?string $region): static
    {
        $this->region = $region;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): static
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getPositionStackApiResult(): array
    {
        return $this->positionStackApiResult ?? [];
    }

    public function getPositionStackApiResultAdmin(): string
    {
        return json_encode($this->positionStackApiResult, JSON_PRETTY_PRINT);
    }

    public function setPositionStackApiResult(?array $positionStackApiResult): self
    {
        $this->positionStackApiResult = $positionStackApiResult;

        return $this;
    }
}
