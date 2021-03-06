<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'text')]
    private $Title;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $availableCount;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $cost;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $photo;

    #[ORM\ManyToOne(targetEntity: Production::class, inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private $company;

    #[ORM\Column(type: 'datetime_immutable')]
    private $created_at;

    #[ORM\ManyToOne(targetEntity: Categories::class, inversedBy: 'products')]
    private $category;

    #[ORM\Column(type: 'integer')]
    private $externalId;

    #[ORM\Column(type: 'text', nullable: true)]
    private $description;

    #[ORM\Column(type: 'text', nullable: true)]
    private $gost;

    #[ORM\ManyToOne(targetEntity: Industrial::class, inversedBy: 'products')]
    private $industry;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $odkp2;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $tnved;

    #[ORM\Column(type: 'json', nullable: true)]
    private $characteristics = [];

    #[ORM\Column(type: 'integer', nullable: true)]
    private $okeiId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->Title;
    }

    public function setTitle(string $Title): self
    {
        $this->Title = $Title;

        return $this;
    }

    public function getAvailableCount(): ?int
    {
        return $this->availableCount;
    }

    public function setAvailableCount(?int $availableCount): self
    {
        $this->availableCount = $availableCount;

        return $this;
    }

    public function getCost(): ?int
    {
        return $this->cost;
    }

    public function setCost(?int $cost): self
    {
        $this->cost = $cost;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getCompany(): ?Production
    {
        return $this->company;
    }

    public function setCompany(?Production $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getCategory(): ?Categories
    {
        return $this->category;
    }

    public function setCategory(?Categories $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getExternalId(): ?int
    {
        return $this->externalId;
    }

    public function setExternalId(int $externalId): self
    {
        $this->externalId = $externalId;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getGost(): ?string
    {
        return $this->gost;
    }

    public function setGost(?string $gost): self
    {
        $this->gost = $gost;

        return $this;
    }

    public function getIndustry(): ?Industrial
    {
        return $this->industry;
    }

    public function setIndustry(?Industrial $industry): self
    {
        $this->industry = $industry;

        return $this;
    }

    public function getOdkp2(): ?string
    {
        return $this->odkp2;
    }

    public function setOdkp2(?string $odkp2): self
    {
        $this->odkp2 = $odkp2;

        return $this;
    }

    public function getTnved(): ?string
    {
        return $this->tnved;
    }

    public function setTnved(?string $tnved): self
    {
        $this->tnved = $tnved;

        return $this;
    }

    public function getCharacteristics(): ?array
    {
        return $this->characteristics;
    }

    public function setCharacteristics(?array $characteristics): self
    {
        $this->characteristics = $characteristics;

        return $this;
    }

    public function getOkeiId(): ?int
    {
        return $this->okeiId;
    }

    public function setOkeiId(?int $okeiId): self
    {
        $this->okeiId = $okeiId;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getTitle();
    }
}
