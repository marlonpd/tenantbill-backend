<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MeterReadingRepository")
 */
class MeterReading
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Tenant", inversedBy="meterReadings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tenant;

    /**
     * @Groups({"primary"})
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fromDate;

    /**
     * @Groups({"primary"})
     * @ORM\Column(type="float")
     */
    private $previousReadingKwh;

    /**
     * @Groups({"primary"})
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $toDate;

    /**
     * @Groups({"primary"})
     * @ORM\Column(type="float")
     */
    private $presentReadingKwh;

    /**
     * @Groups({"primary"})
     * @ORM\Column(type="float", nullable=true)
     */
    private $consumedKwh;

    /**
     * @Groups({"primary"})
     * @ORM\Column(type="float", nullable=true)
     */
    private $rate;

    /**
     * @Groups({"primary"})
     * @ORM\Column(type="float", nullable=true)
     */
    private $bill;

    /**
     * @Groups({"primary"})
     * @ORM\Column(type="datetime")
     */
    private $created;

    public function __construct()
    {
        $this->created = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTenant(): ?Tenant
    {
        return $this->tenant;
    }

    public function setTenant(?Tenant $tenant): self
    {
        $this->tenant = $tenant;

        return $this;
    }

    public function getFromDate(): ?\DateTimeInterface
    {
        return $this->fromDate;
    }

    public function setFromDate(?\DateTimeInterface $fromDate): self
    {
        $this->fromDate = $fromDate;

        return $this;
    }

    public function getPreviousReadingKwh(): ?float
    {
        return $this->previousReadingKwh;
    }

    public function setPreviousReadingKwh(float $previousReadingKwh): self
    {
        $this->previousReadingKwh = $previousReadingKwh;

        return $this;
    }

    public function getToDate(): ?\DateTimeInterface
    {
        return $this->toDate;
    }

    public function setToDate(?\DateTimeInterface $toDate): self
    {
        $this->toDate = $toDate;

        return $this;
    }

    public function getPresentReadingKwh(): ?float
    {
        return $this->presentReadingKwh;
    }

    public function setPresentReadingKwh(float $presentReadingKwh): self
    {
        $this->presentReadingKwh = $presentReadingKwh;

        return $this;
    }

    public function getConsumedKwh(): ?float
    {
        return $this->consumedKwh;
    }

    public function setConsumedKwh(?float $consumedKwh): self
    {
        $this->consumedKwh = $consumedKwh;

        return $this;
    }

    public function getRate(): ?float
    {
        return $this->rate;
    }

    public function setRate(?float $rate): self
    {
        $this->rate = $rate;

        return $this;
    }

    public function getBill(): ?float
    {
        return $this->bill;
    }

    public function setBill(?float $bill): self
    {
        $this->bill = $bill;

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
}
