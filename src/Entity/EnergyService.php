<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Entity\Traits\IdentifyTraits;
use App\Entity\Traits\NameTraits;
use App\Repository\EnergyServiceRepository;
use App\Service\Uuid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: EnergyServiceRepository::class)]
#[ApiResource]
class EnergyService
{
    use IdentifyTraits;
    use NameTraits;
    use TimestampableEntity;
    use BlameableEntity;

    #[ORM\ManyToMany(targetEntity: EnergyStation::class, mappedBy: 'energyServices', fetch: 'EXTRA_LAZY')]
    private Collection $energyStations;

    public function __construct()
    {
        $this->uuid = Uuid::v4();
        $this->energyStations = new ArrayCollection();
    }

    /**
     * @return Collection<int, EnergyStation>
     */
    public function getEnergyStations(): Collection
    {
        return $this->energyStations;
    }

    public function addEnergyStation(EnergyStation $energyStation): static
    {
        if (!$this->energyStations->contains($energyStation)) {
            $this->energyStations->add($energyStation);
        }

        return $this;
    }

    public function removeEnergyStation(EnergyStation $energyStation): static
    {
        $this->energyStations->removeElement($energyStation);

        return $this;
    }
}
