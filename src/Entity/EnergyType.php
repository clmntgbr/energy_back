<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\Traits\IdentifyTraits;
use App\Entity\Traits\NameTraits;
use App\Repository\EnergyTypeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: EnergyTypeRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['skip_null_values' => false, 'groups' => ['get_energy_types', 'common']],
        ),
        new Get(
            normalizationContext: ['skip_null_values' => false, 'groups' => ['get_energy_type', 'common']],
        )
    ]
)]
#[Vich\Uploadable]
class EnergyType
{
    use IdentifyTraits;
    use NameTraits;
    use TimestampableEntity;
    use BlameableEntity;

    #[ORM\Column(type: Types::STRING, length: 5)]
    #[Groups(['get_energy_types', 'get_energy_type'])]
    private string $type;

    #[ORM\Column(type: Types::STRING, length: 5, nullable:true)]
    #[Groups(['get_energy_types', 'get_energy_type'])]
    private ?string $code;

    #[Vich\UploadableField(mapping: 'energy_types_image', fileNameProperty: 'image.name', size: 'image.size', mimeType: 'image.mimeType', originalName: 'image.originalName', dimensions: 'image.dimensions')]
    private ?File $imageFile = null;

    #[ORM\Embedded(class: 'Vich\UploaderBundle\Entity\File')]
    private EmbeddedFile $image;

    public function __construct()
    {
        $this->image = new EmbeddedFile();
    }

    #[Groups(['get_energy_types', 'get_energy_type'])]
    public function getImagePath(): string
    {
        return sprintf('/images/energy_types/%s', $this->getImage()->getName());
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): static
    {
        $this->code = $code;

        return $this;
    }
}
