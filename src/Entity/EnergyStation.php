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
use function Safe\json_encode;

#[ORM\Entity(repositoryClass: EnergyStationRepository::class)]
#[ApiResource]
#[Vich\Uploadable]
class EnergyStation
{
    use IdentifyTraits;
    use TimestampableEntity;
    use BlameableEntity;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['get_energy_station'])]
    private string $pop;

    #[ORM\Column(type: Types::STRING, length: 5)]
    #[Groups(['get_energy_station'])]
    private string $type;

    #[ORM\Column(type: Types::STRING, length: 100)]
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

    #[ORM\ManyToOne(targetEntity: EvInformation::class, cascade: ['persist'], fetch: 'LAZY')]
    private ?EvInformation $evInformation;

    #[ORM\OneToMany(mappedBy: 'energyStation', targetEntity: EnergyPrice::class, cascade: ['persist', 'remove'], fetch: 'LAZY')]
    private Collection $energyPrices;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    #[Groups(['get_energy_stations', 'get_energy_station'])]
    private ?array $services;

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
        $this->services = [];
        $this->image = new \Vich\UploaderBundle\Entity\File();
        $this->energyPrices = new ArrayCollection();
        $this->evInformation = new EvInformation();
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

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

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getStatusAdmin()
    {
        return null;
    }

    public function setStatusAdmin(?string $status): self
    {
        if (null === $status) {
            return $this;
        }

        $this->status = $status;
        $this->setStatuses($status);

        return $this;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        $this->setStatuses($status);

        return $this;
    }

    public function getStatuses(): ?array
    {
        return $this->statuses;
    }

    public function getStatusesAdmin(): ?array
    {
        return array_reverse($this->statuses);
    }

    public function setStatuses(string $status): self
    {
        $this->statuses[] = $status;

        return $this;
    }

    public function setInitStatuses(array $status): self
    {
        $this->statuses = $status;

        return $this;
    }

    public function getPreviousStatus(): ?string
    {
        if (count($this->statuses) <= 1) {
            return end($this->statuses);
        }

        return $this->statuses[count($this->statuses) - 2];
    }

    public function getClosedAt(): ?\DateTimeImmutable
    {
        return $this->closedAt;
    }

    public function getLastEnergyPricesAdmin()
    {
        $json = [];
        foreach ($this->lastEnergyPrices as $key => $energyPrice) {
            $energyPrice['date'] = (new \DateTime('now', new \DateTimeZone('Europe/Paris')))->setTimestamp($energyPrice['energyPriceDatetimestamp'])->format('Y-m-d h:s:i');
            $json[$key] = $energyPrice;
        }

        return json_encode($json, JSON_PRETTY_PRINT);
    }

    public function getPreviousEnergyPricesAdmin()
    {
        $json = [];
        foreach ($this->previousEnergyPrices as $key => $energyPrice) {
            $energyPrice['date'] = (new \DateTime())->setTimestamp($energyPrice['energyPriceDatetimestamp'])->format('Y-m-d h:s:i');
            $json[$key] = $energyPrice;
        }

        return json_encode($json, JSON_PRETTY_PRINT);
    }

    public function setClosedAt(?\DateTimeImmutable $closedAt): static
    {
        $this->closedAt = $closedAt;

        return $this;
    }

    public function isHasLowPrices(): ?bool
    {
        return $this->hasLowPrices;
    }

    public function setHasLowPrices(bool $hasLowPrices): self
    {
        $this->hasLowPrices = $hasLowPrices;

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

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(?string $hash): static
    {
        $this->hash = $hash;

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

    public function getElementAdmin()
    {
        return json_encode($this->element, JSON_PRETTY_PRINT);
    }

    public function getServicesAdmin()
    {
        return json_encode($this->services, JSON_PRETTY_PRINT);
    }

    /**
     * @return array<mixed>
     */
    public function getLastEnergyPrices(): array
    {
        return $this->lastEnergyPrices;
    }

    public function setLastEnergyPrices(EnergyPrice $energyPrice): self
    {
        $value = 'stable';

        if (array_key_exists($energyPrice->getEnergyType()->getUuid(), $this->lastEnergyPrices) && null !== $this->lastEnergyPrices[$energyPrice->getEnergyType()->getUuid()]) {
            $this->previousEnergyPrices[$energyPrice->getEnergyType()->getUuid()] = $this->lastEnergyPrices[$energyPrice->getEnergyType()->getUuid()];
            $value = $this->getEnergyPriceDifference($energyPrice);
        }

        $this->lastEnergyPrices[$energyPrice->getEnergyType()->getUuid()] = $this->hydrateEnergyPrices($energyPrice, $value);

        return $this;
    }

    public function addLastEnergyPrices(array $energyPrice)
    {
        $this->lastEnergyPrices = $energyPrice;

        return $this;
    }

    private function getEnergyPriceDifference(EnergyPrice $energyPrice)
    {
        if ($this->previousEnergyPrices[$energyPrice->getEnergyType()->getUuid()]['energyPriceValue'] > $energyPrice->getValue()) {
            return 'decreasing';
        }

        if ($this->previousEnergyPrices[$energyPrice->getEnergyType()->getUuid()]['energyPriceValue'] < $energyPrice->getValue()) {
            return 'increasing';
        }

        return 'stable';
    }

    /**
     * @return array<mixed>
     */
    public function getPreviousEnergyPrices()
    {
        return $this->previousEnergyPrices;
    }

    public function setPreviousEnergyPrices(EnergyPrice $energyPrice): self
    {
        $this->previousEnergyPrices[$energyPrice->getEnergyType()->getUuid()] = $this->hydrateEnergyPrices($energyPrice);

        return $this;
    }

    private function hydrateEnergyPrices(EnergyPrice $energyPrice, string $value = 'stable')
    {
        return [
            'energyPriceId' => $energyPrice->getId(),
            'energyPriceDatetimestamp' => $energyPrice->getDateTimestamp(),
            'energyPriceValue' => $energyPrice->getValue(),
            'energyTypeUuid' => $energyPrice->getEnergyType()->getUuid(),
            'energyTypeId' => $energyPrice->getEnergyType()->getId(),
            'energyTypeLabel' => $energyPrice->getEnergyType()->getName(),
            'currency' => $energyPrice->getCurrency()->getName(),
            'energyPriceDifference' => $value,
        ];
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

    public function isHasEnergyStationBrandVerified(): ?bool
    {
        return $this->hasEnergyStationBrandVerified;
    }

    public function setHasEnergyStationBrandVerified(?bool $hasEnergyStationBrandVerified): static
    {
        $this->hasEnergyStationBrandVerified = $hasEnergyStationBrandVerified;

        return $this;
    }

    public function getMaxRetryPositionStack(): ?int
    {
        return $this->maxRetryPositionStack;
    }

    public function addMaxRetryPositionStack(): ?int
    {
        return $this->maxRetryPositionStack++;
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

    public function addMaxRetryTextSearch(): ?int
    {
        return $this->maxRetryTextSearch++;
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

    public function addMaxRetryPlaceDetails(): ?int
    {
        return $this->maxRetryPlaceDetails++;
    }

    public function setMaxRetryPlaceDetails(int $maxRetryPlaceDetails): static
    {
        $this->maxRetryPlaceDetails = $maxRetryPlaceDetails;

        return $this;
    }

    public function getServices(): ?array
    {
        return $this->services;
    }

    public function setServices(?array $services): static
    {
        $this->services = $services;
        return $this;
    }

    public function addService(string $key, string $value): static
    {
        $this->services[$key] = ['key' => $key, 'name' => $value];
        return $this;
    }

    public function initServices(): static
    {
        $this->services = [];
        return $this;
    }

    public function getEvInformation(): ?EvInformation
    {
        return $this->evInformation;
    }

    public function setEvInformation(?EvInformation $evInformation): static
    {
        $this->evInformation = $evInformation;

        return $this;
    }
}
