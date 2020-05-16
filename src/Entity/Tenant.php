<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TenantRepository")
 */
class Tenant
{
    /**
     * @ORM\Id()
     * @Groups({"primary"})
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
    * @Groups({"primary"})
     * @ORM\Column(type="string", length=120)
     */
    private $name;

    /**
    * @Groups({"primary"})
     * @ORM\Column(type="string", length=120)
     */
    private $meterNumber;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="tenants")
     * @ORM\JoinColumn(nullable=false)
     */
    private $owner;

    /**
    * @Groups({"primary"})
     * @ORM\Column(type="float", length=120)
     */
    private $meterInitialReading;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\MeterReading", mappedBy="tenant")
     */
    private $meterReadings;

    /**
     * @Groups({"primary"})
     * @ORM\Column(type="datetime")
     */
    private $created;

    public function __construct()
    {
        $this->created = new \DateTime();
        $this->meterReadings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getMeterNumber(): ?string
    {
        return $this->meterNumber;
    }

    public function setMeterNumber(string $meterNumber): self
    {
        $this->meterNumber = $meterNumber;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getMeterInitialReading(): ?float
    {
        return $this->meterInitialReading;
    }

    public function setMeterInitialReading(float $meterInitialReading): self
    {
        $this->meterInitialReading = $meterInitialReading;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }

    /**
     * @return Collection|MeterReading[]
     */
    public function getMeterReadings(): Collection
    {
        return $this->meterReadings;
    }

    public function addMeterReading(MeterReading $meterReading): self
    {
        if (!$this->meterReadings->contains($meterReading)) {
            $this->meterReadings[] = $meterReading;
            $meterReading->setTenant($this);
        }

        return $this;
    }

    public function removeMeterReading(MeterReading $meterReading): self
    {
        if ($this->meterReadings->contains($meterReading)) {
            $this->meterReadings->removeElement($meterReading);
            // set the owning side to null (unless already changed)
            if ($meterReading->getTenant() === $this) {
                $meterReading->setTenant(null);
            }
        }

        return $this;
    }
}
