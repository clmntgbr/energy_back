<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Entity\Traits\IdentifyTraits;
use App\Repository\EvInformationRepository;
use App\Service\Uuid;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: EvInformationRepository::class)]
#[ApiResource(
    operations: []
)]
class EvInformation
{
    use IdentifyTraits;
    use TimestampableEntity;
    use BlameableEntity;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['get_energy_stations_map'])]
    private ?string $numberRechargePoint;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['get_energy_stations_map'])]
    private ?string $maximumPower;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['get_energy_stations_map'])]
    private ?DateTime $dateCreated;

    #[ORM\OneToMany(targetEntity: EvRechargePoint::class, mappedBy: 'evinformation')]
    #[Groups(['get_energy_stations_map'])]
    private Collection $evRechargePoints;

    public function __construct()
    {
        $this->uuid = Uuid::v4();
        $this->evRechargePoints = new ArrayCollection();
    }

    public function getNumberRechargePoint(): ?string
    {
        return $this->numberRechargePoint;
    }

    public function setNumberRechargePoint(?string $numberRechargePoint): static
    {
        $this->numberRechargePoint = $numberRechargePoint;

        return $this;
    }

    public function getMaximumPower(): ?string
    {
        return $this->maximumPower;
    }

    public function setMaximumPower(?string $maximumPower): static
    {
        $this->maximumPower = $maximumPower;

        return $this;
    }

    public function getDateCreated(): ?\DateTime
    {
        return $this->dateCreated;
    }

    public function setDateCreated(?\DateTime $dateCreated): static
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * @return Collection<int, EvRechargePoint>
     */
    public function getEvRechargePoints(): Collection
    {
        return $this->evRechargePoints;
    }

    public function addEvRechargePoint(EvRechargePoint $evRechargePoint): static
    {
        if (!$this->evRechargePoints->contains($evRechargePoint)) {
            $this->evRechargePoints->add($evRechargePoint);
            $evRechargePoint->setEvinformation($this);
        }

        return $this;
    }

    public function removeEvRechargePoint(EvRechargePoint $evRechargePoint): static
    {
        if ($this->evRechargePoints->removeElement($evRechargePoint)) {
            // set the owning side to null (unless already changed)
            if ($evRechargePoint->getEvinformation() === $this) {
                $evRechargePoint->setEvinformation(null);
            }
        }

        return $this;
    }
}
