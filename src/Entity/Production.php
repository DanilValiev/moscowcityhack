<?php

namespace App\Entity;

use App\Repository\ProductionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductionRepository::class)]
class Production
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $Title;

    #[ORM\Column(type: 'string')]
    private $Ogrn;

    #[ORM\Column(type: 'string', nullable: true)]
    private $inn;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $logo;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $kpp;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $url;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $Address;

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: Product::class, orphanRemoval: true)]
    private $products;

    #[ORM\OneToOne(mappedBy: 'production', targetEntity: ContactInfo::class, cascade: ['persist', 'remove'])]
    private $contactInfo;

    #[ORM\ManyToOne(targetEntity: Region::class, cascade: ['persist'], inversedBy: 'production')]
    private $region;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

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

    public function getOgrn(): ?string
    {
        return $this->Ogrn;
    }

    public function setOgrn(string $Ogrn): self
    {
        $this->Ogrn = $Ogrn;

        return $this;
    }

    public function getInn(): ?string
    {
        return $this->inn;
    }

    public function setInn(?string $inn): self
    {
        $this->inn = $inn;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    public function getKpp(): ?int
    {
        return $this->kpp;
    }

    public function setKpp(?int $kpp): self
    {
        $this->kpp = $kpp;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->Address;
    }

    public function setAddress(?string $Address): self
    {
        $this->Address = $Address;

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setCompany($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getCompany() === $this) {
                $product->setCompany(null);
            }
        }

        return $this;
    }

    public function getContactInfo(): ?ContactInfo
    {
        return $this->contactInfo;
    }

    public function setContactInfo(ContactInfo $contactInfo): self
    {
        // set the owning side of the relation if necessary
        if ($contactInfo->getProduction() !== $this) {
            $contactInfo->setProduction($this);
        }

        $this->contactInfo = $contactInfo;

        return $this;
    }

    public function getRegion(): ?Region
    {
        return $this->region;
    }

    public function setRegion(?Region $region): self
    {
        $this->region = $region;

        return $this;
    }
}
