<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Entity\Traits\IdentifyTraits;
use App\Repository\EnergyStationRepository;
use App\Service\Uuid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: EnergyStationRepository::class)]
#[ApiResource]
class EnergyStation
{
    use IdentifyTraits;
    use TimestampableEntity;
    use BlameableEntity;

    #[ORM\Column(type: Types::STRING, length: 10)]
    #[Groups(['get_energy_station'])]
    private string $pop;

    #[ORM\Column(type: Types::STRING, length: 20)]
    #[Groups(['get_energy_stations', 'get_energy_station'])]
    private string $energyStationId;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['get_energy_stations', 'get_energy_station'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $statuses = [];

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['get_energy_stations', 'get_energy_station'])]
    private ?string $status;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    private bool $hasEnergyStationBrandVerified = false;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[Groups(['get_energy_station'])]
    private ?\DateTimeImmutable $closedAt = null;

    #[ORM\OneToOne(targetEntity: Address::class, cascade: ['persist', 'remove'])]
    #[Groups(['get_energy_stations', 'get_energy_station'])]
    #[ORM\JoinColumn(nullable: false)]
    private Address $address;

    #[ORM\ManyToOne(targetEntity: GooglePlace::class, cascade: ['persist', 'remove'])]
    #[Groups(['get_energy_stations', 'get_energy_station'])]
    #[ORM\JoinColumn(nullable: false)]
    private GooglePlace $googlePlace;

    #[ORM\Column(type: Types::JSON)]
    private array $element = [];

    #[Vich\UploadableField(mapping: 'energy_stations_image', fileNameProperty: 'image.name', size: 'image.size', mimeType: 'image.mimeType', originalName: 'image.originalName', dimensions: 'image.dimensions')]
    private ?File $imageFile = null;

    #[ORM\Embedded(class: 'Vich\UploaderBundle\Entity\File')]
    private EmbeddedFile $image;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $hash;

    #[ORM\ManyToOne(targetEntity: EnergyStationBrand::class, cascade: ['persist'], fetch: 'LAZY')]
    #[Groups(['get_energy_station'])]
    private EnergyStationBrand $energyStationBrand;

    #[ORM\OneToMany(mappedBy: 'energyStation', targetEntity: EnergyPrice::class, cascade: ['persist', 'remove'], fetch: 'LAZY')]
    private Collection $energyPrices;

    #[ORM\ManyToMany(targetEntity: EnergyService::class, inversedBy: 'energyStations', cascade: ['persist'])]
    #[Groups(['get_energy_stations', 'get_energy_station'])]
    private Collection $energyServices;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $lastEnergyPrices;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $previousEnergyPrices;

    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    private int $maxRetryPositionStack;

    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    private int $maxRetryTextSearch;

    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    private int $maxRetryPlaceDetails;

    #[Groups(['get_energy_stations'])]
    private bool $hasLowPrices = false;

    public function __construct()
    {
        $this->maxRetryPlaceDetails = 0;
        $this->maxRetryPositionStack = 0;
        $this->maxRetryTextSearch = 0;
        $this->statuses = [];
        $this->uuid = Uuid::v4();
        $this->lastEnergyPrices = [];
        $this->previousEnergyPrices = [];
        $this->image = new \Vich\UploaderBundle\Entity\File();
        $this->energyPrices = new ArrayCollection();
        $this->energyServices = new ArrayCollection();
    }

    public function __toString()
    {
        return (string)$this->energyStationId;
    }

    #[Groups(['get_energy_stations', 'get_energy_station'])]
    public function getImagePath(): string
    {
        return sprintf('/images/energy_stations/%s', $this->getImage()->getName());
    }

    #[Groups(['get_energy_stations', 'get_energy_station'])]
    public function getLastPrices(): array
    {
        return array_combine(array_slice([0, 1, 2, 3, 4, 5], 0, count($this->lastEnergyPrices)), $this->lastEnergyPrices);
    }

    #[Groups(['get_energy_station'])]
    public function getPreviousPrices(): array
    {
        return array_combine(array_slice([0, 1, 2, 3, 4, 5], 0, count($this->previousEnergyPrices)), $this->previousEnergyPrices);
    }

    public function getImage(): EmbeddedFile
    {
        return $this->image;
    }

    public function setImage(EmbeddedFile $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(File $imageFile = null): self
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            $this->updatedAt = new \DateTime();
        }

        return $this;
    }

    public function getPop(): ?string
    {
        return $this->pop;
    }

    public function setPop(string $pop): static
    {
        $this->pop = $pop;

        return $this;
    }

    public function getEnergyStationId(): ?string
    {
        return $this->energyStationId;
    }

    public function setEnergyStationId(string $energyStationId): static
    {
        $this->energyStationId = $energyStationId;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getStatuses(): ?array
    {
        return $this->statuses;
    }

    public function setStatuses(?array $statuses): static
    {
        $this->statuses = $statuses;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function isHasEnergyStationBrandVerified(): ?bool
    {
        return $this->hasEnergyStationBrandVerified;
    }

    public function setHasEnergyStationBrandVerified(?bool $hasEnergyStationBrandVerified): static
    {
        $this->hasEnergyStationBrandVerified = $hasEnergyStationBrandVerified;

        return $this;
    }

    public function getClosedAt(): ?\DateTimeImmutable
    {
        return $this->closedAt;
    }

    public function setClosedAt(?\DateTimeImmutable $closedAt): static
    {
        $this->closedAt = $closedAt;

        return $this;
    }

    public function getElement(): array
    {
        return $this->element;
    }

    public function setElement(array $element): static
    {
        $this->element = $element;

        return $this;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(?string $hash): static
    {
        $this->hash = $hash;

        return $this;
    }

    public function getLastEnergyPrices(): ?array
    {
        return $this->lastEnergyPrices;
    }

    public function setLastEnergyPrices(?array $lastEnergyPrices): static
    {
        $this->lastEnergyPrices = $lastEnergyPrices;

        return $this;
    }

    public function getPreviousEnergyPrices(): ?array
    {
        return $this->previousEnergyPrices;
    }

    public function setPreviousEnergyPrices(?array $previousEnergyPrices): static
    {
        $this->previousEnergyPrices = $previousEnergyPrices;

        return $this;
    }

    public function getMaxRetryPositionStack(): ?int
    {
        return $this->maxRetryPositionStack;
    }

    public function setMaxRetryPositionStack(int $maxRetryPositionStack): static
    {
        $this->maxRetryPositionStack = $maxRetryPositionStack;

        return $this;
    }

    public function getMaxRetryTextSearch(): ?int
    {
        return $this->maxRetryTextSearch;
    }

    public function setMaxRetryTextSearch(int $maxRetryTextSearch): static
    {
        $this->maxRetryTextSearch = $maxRetryTextSearch;

        return $this;
    }

    public function getMaxRetryPlaceDetails(): ?int
    {
        return $this->maxRetryPlaceDetails;
    }

    public function setMaxRetryPlaceDetails(int $maxRetryPlaceDetails): static
    {
        $this->maxRetryPlaceDetails = $maxRetryPlaceDetails;

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(Address $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getGooglePlace(): ?GooglePlace
    {
        return $this->googlePlace;
    }

    public function setGooglePlace(?GooglePlace $googlePlace): static
    {
        $this->googlePlace = $googlePlace;

        return $this;
    }

    public function getEnergyStationBrand(): ?EnergyStationBrand
    {
        return $this->energyStationBrand;
    }

    public function setEnergyStationBrand(?EnergyStationBrand $energyStationBrand): static
    {
        $this->energyStationBrand = $energyStationBrand;

        return $this;
    }

    /**
     * @return Collection<int, EnergyPrice>
     */
    public function getEnergyPrices(): Collection
    {
        return $this->energyPrices;
    }

    public function addEnergyPrice(EnergyPrice $energyPrice): static
    {
        if (!$this->energyPrices->contains($energyPrice)) {
            $this->energyPrices->add($energyPrice);
            $energyPrice->setEnergyStation($this);
        }

        return $this;
    }

    public function removeEnergyPrice(EnergyPrice $energyPrice): static
    {
        if ($this->energyPrices->removeElement($energyPrice)) {
            // set the owning side to null (unless already changed)
            if ($energyPrice->getEnergyStation() === $this) {
                $energyPrice->setEnergyStation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, EnergyService>
     */
    public function getEnergyServices(): Collection
    {
        return $this->energyServices;
    }

    public function addEnergyService(EnergyService $energyService): static
    {
        if (!$this->energyServices->contains($energyService)) {
            $this->energyServices->add($energyService);
        }

        return $this;
    }

    public function removeEnergyService(EnergyService $energyService): static
    {
        $this->energyServices->removeElement($energyService);

        return $this;
    }
}
