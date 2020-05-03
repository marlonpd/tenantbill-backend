<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $from_date;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $to_date;

    /**
     * @ORM\Column(type="float")
     */
    private $reading_kwh;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $consumed_kwh;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $rate;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $bill;

    /**
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
        return $this->from_date;
    }

    public function setFromDate(?\DateTimeInterface $from_date): self
    {
        $this->from_date = $from_date;

        return $this;
    }

    public function getToDate(): ?\DateTimeInterface
    {
        return $this->to_date;
    }

    public function setToDate(?\DateTimeInterface $to_date): self
    {
        $this->to_date = $to_date;

        return $this;
    }

    public function getReadingKwh(): ?float
    {
        return $this->reading_kwh;
    }

    public function setReadingKwh(float $reading_kwh): self
    {
        $this->reading_kwh = $reading_kwh;

        return $this;
    }

    public function getConsumedKwh(): ?float
    {
        return $this->consumed_kwh;
    }

    public function setConsumedKwh(?float $consumed_kwh): self
    {
        $this->consumed_kwh = $consumed_kwh;

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
