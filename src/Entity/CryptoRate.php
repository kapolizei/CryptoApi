<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\CryptoRateRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CryptoRateRepository::class)]
#[ApiResource]
class CryptoRate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10)]
    private ?string $currencyPair = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $rate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $timestamp = null;

    #[ORM\Column(length: 10)]
    private ?string $quoteCurrency = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getCurrencyPair(): ?string
    {
        return $this->currencyPair;
    }

    public function setCurrencyPair(string $currencyPair): static
    {
        $this->currencyPair = $currencyPair;

        return $this;
    }

    public function getRate(): ?string
    {
        return $this->rate;
    }

    public function setRate(string $rate): static
    {
        $this->rate = $rate;

        return $this;
    }

    public function getTimestamp(): ?\DateTimeInterface
    {
        return $this->timestamp;
    }

    public function setTimestamp(\DateTimeInterface $timestamp): static
    {
        $this->timestamp = $timestamp;

        return $this;
    }



    public function getQuoteCurrency(): ?string
    {
        return $this->quoteCurrency;
    }

    public function setQuoteCurrency(string $quoteCurrency): static
    {
        $this->quoteCurrency = $quoteCurrency;

        return $this;
    }

    public function exportToArray(): array
    {
        return [
            'id' => $this->id,
            'pair' => $this->currencyPair,
            'price' => $this->rate,
            'quoteCurrency' => $this->quoteCurrency,
            'time' => $this->timestamp,
        ];
    }

}
