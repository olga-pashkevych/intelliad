<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RateRepository")
 */
class Rate
{
    /** @ORM\Id()
     *  @ORM\Column(type="custom_datetime")
     */
    private $date;

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="App\Entity\Currency", inversedBy="rates")
     * @ORM\JoinColumn(nullable=false)
     */
    private $currency;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=6)
     */
    private $rate;

    public function __construct(\DateTimeInterface $date, Currency $currency)
    {
        $this->date = $date;
        $this->currency = $currency;
    }

    public function getCurrency(): ?Currency
    {
        return $this->currency;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function getRate()
    {
        return $this->rate;
    }

    public function setRate($rate): self
    {
        $this->rate = $rate;

        return $this;
    }
}
