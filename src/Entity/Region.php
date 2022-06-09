<?php

namespace App\Entity;

use App\Repository\RegionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RegionRepository::class)]
class Region
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $code;

    #[ORM\Column(type: 'string', length: 255)]
    private $title;

    #[ORM\Column(type: 'string', length: 255)]
    private $alphacode;

    #[ORM\OneToMany(mappedBy: 'region', targetEntity: Production::class)]
    private $production;

    public function __construct()
    {
        $this->production = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?int
    {
        return $this->code;
    }

    public function setCode(int $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getAlphacode(): ?string
    {
        return $this->alphacode;
    }

    public function setAlphacode(string $alphacode): self
    {
        $this->alphacode = $alphacode;

        return $this;
    }

    /**
     * @return Collection<int, Production>
     */
    public function getProduction(): Collection
    {
        return $this->production;
    }

    public function addProduction(Production $production): self
    {
        if (!$this->production->contains($production)) {
            $this->production[] = $production;
            $production->setRegion($this);
        }

        return $this;
    }

    public function removeProduction(Production $production): self
    {
        if ($this->production->removeElement($production)) {
            // set the owning side to null (unless already changed)
            if ($production->getRegion() === $this) {
                $production->setRegion(null);
            }
        }

        return $this;
    }
}
