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

    #[ORM\Column(type: 'text')]
    private $Title;

    #[ORM\Column(type: 'string', nullable: true)]
    private $Ogrn;

    #[ORM\Column(type: 'string', nullable: true)]
    private $inn;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $logo;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $kpp;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $url;

    #[ORM\Column(type: 'text', nullable: true)]
    private $Address;

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: Product::class, orphanRemoval: true)]
    private $products;

    #[ORM\OneToOne(mappedBy: 'production', targetEntity: ContactInfo::class, cascade: ['persist', 'remove'])]
    private $contactInfo;

    #[ORM\ManyToOne(targetEntity: Region::class, cascade: ['persist'], inversedBy: 'production')]
    private $region;

    #[ORM\ManyToMany(targetEntity: Industrial::class, inversedBy: 'productions')]
    private $industries;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $manual;

    #[ORM\Column(type: 'json', nullable: true)]
    private $support = [];

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $companyRegData;

    #[ORM\ManyToOne(targetEntity: Odkv::class, inversedBy: 'productions')]
    private $odvkPrimary;

    #[ORM\ManyToMany(targetEntity: Odkv::class)]
    private $odkvSecondary;

    #[ORM\Column(type: 'json', nullable: true)]
    private $finReport = [];

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $staturoryCapital;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->industries = new ArrayCollection();
        $this->odkvSecondary = new ArrayCollection();
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

    /**
     * @return Collection<int, Industrial>
     */
    public function getIndustries(): Collection
    {
        return $this->industries;
    }

    public function addIndustry(Industrial $industry): self
    {
        if (!$this->industries->contains($industry)) {
            $this->industries[] = $industry;
        }

        return $this;
    }

    public function removeIndustry(Industrial $industry): self
    {
        $this->industries->removeElement($industry);

        return $this;
    }

    public function isManual(): ?bool
    {
        return $this->manual;
    }

    public function setManual(?bool $manual): self
    {
        $this->manual = $manual;

        return $this;
    }

    public function getSupport(): ?array
    {
        return $this->support;
    }

    public function setSupport(?array $support): self
    {
        $this->support = $support;

        return $this;
    }

    public function getCompanyRegData(): ?string
    {
        return $this->companyRegData;
    }

    public function setCompanyRegData(?string $companyRegData): self
    {
        $this->companyRegData = $companyRegData;

        return $this;
    }

    public function getOdvkPrimary(): ?Odkv
    {
        return $this->odvkPrimary;
    }

    public function setOdvkPrimary(?Odkv $odvkPrimary): self
    {
        $this->odvkPrimary = $odvkPrimary;

        return $this;
    }

    /**
     * @return Collection<int, Odkv>
     */
    public function getOdkvSecondary(): Collection
    {
        return $this->odkvSecondary;
    }

    public function addOdkvSecondary(Odkv $odkvSecondary): self
    {
        if (!$this->odkvSecondary->contains($odkvSecondary)) {
            $this->odkvSecondary[] = $odkvSecondary;
        }

        return $this;
    }

    public function removeOdkvSecondary(Odkv $odkvSecondary): self
    {
        $this->odkvSecondary->removeElement($odkvSecondary);

        return $this;
    }

    public function getFinReport(): ?array
    {
        return $this->finReport;
    }

    public function setFinReport(?array $finReport): self
    {
        $this->finReport = $finReport;

        return $this;
    }

    public function getStaturoryCapital(): ?string
    {
        return $this->staturoryCapital;
    }

    public function setStaturoryCapital(?string $staturoryCapital): self
    {
        $this->staturoryCapital = $staturoryCapital;

        return $this;
    }
}
