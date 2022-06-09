<?php

namespace App\Entity;

use App\Repository\ContactInfoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContactInfoRepository::class)]
class ContactInfo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $directorFio;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $contactFio;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $contactEmail;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $contactPhone;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $contactFax;

    #[ORM\OneToOne(inversedBy: 'contactInfo', targetEntity: Production::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $production;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $Site;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDirectorFio(): ?string
    {
        return $this->directorFio;
    }

    public function setDirectorFio(?string $directorFio): self
    {
        $this->directorFio = $directorFio;

        return $this;
    }

    public function getContactFio(): ?string
    {
        return $this->contactFio;
    }

    public function setContactFio(?string $contactFio): self
    {
        $this->contactFio = $contactFio;

        return $this;
    }

    public function getContactEmail(): ?string
    {
        return $this->contactEmail;
    }

    public function setContactEmail(?string $contactEmail): self
    {
        $this->contactEmail = $contactEmail;

        return $this;
    }

    public function getContactPhone(): ?string
    {
        return $this->contactPhone;
    }

    public function setContactPhone(?string $contactPhone): self
    {
        $this->contactPhone = $contactPhone;

        return $this;
    }

    public function getContactFax(): ?string
    {
        return $this->contactFax;
    }

    public function setContactFax(?string $contactFax): self
    {
        $this->contactFax = $contactFax;

        return $this;
    }

    public function getProduction(): ?Production
    {
        return $this->production;
    }

    public function setProduction(Production $production): self
    {
        $this->production = $production;

        return $this;
    }

    public function getSite(): ?string
    {
        return $this->Site;
    }

    public function setSite(?string $Site): self
    {
        $this->Site = $Site;

        return $this;
    }
}
