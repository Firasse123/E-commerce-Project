<?php
// src/Entity/Order.php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 20, unique: true)]
    private ?string $reference = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $totalPrice = null;

    #[ORM\Column(length: 50)]
    private ?string $status = 'pending';

    #[ORM\Column(type: Types::JSON)]
    private array $orderDetails = [];

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    // Adresse de livraison
    #[ORM\Column(length: 100)]
    private ?string $deliveryFirstName = null;

    #[ORM\Column(length: 100)]
    private ?string $deliveryLastName = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $deliveryAddress = null;

    #[ORM\Column(length: 10)]
    private ?string $deliveryZipCode = null;

    #[ORM\Column(length: 100)]
    private ?string $deliveryCity = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $deliveryPhone = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->reference = $this->generateReference();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;
        return $this;
    }

    public function getTotalPrice(): ?string
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(string $totalPrice): static
    {
        $this->totalPrice = $totalPrice;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getOrderDetails(): array
    {
        return $this->orderDetails;
    }

    public function setOrderDetails(array $orderDetails): static
    {
        $this->orderDetails = $orderDetails;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    // Getters et Setters pour l'adresse de livraison
    public function getDeliveryFirstName(): ?string
    {
        return $this->deliveryFirstName;
    }

    public function setDeliveryFirstName(string $deliveryFirstName): static
    {
        $this->deliveryFirstName = $deliveryFirstName;
        return $this;
    }

    public function getDeliveryLastName(): ?string
    {
        return $this->deliveryLastName;
    }

    public function setDeliveryLastName(string $deliveryLastName): static
    {
        $this->deliveryLastName = $deliveryLastName;
        return $this;
    }

    public function getDeliveryAddress(): ?string
    {
        return $this->deliveryAddress;
    }

    public function setDeliveryAddress(string $deliveryAddress): static
    {
        $this->deliveryAddress = $deliveryAddress;
        return $this;
    }

    public function getDeliveryZipCode(): ?string
    {
        return $this->deliveryZipCode;
    }

    public function setDeliveryZipCode(?string $deliveryZipCode): static
    {
        $this->deliveryZipCode = $deliveryZipCode;
        return $this;
    }

    public function getDeliveryCity(): ?string
    {
        return $this->deliveryCity;
    }

    public function setDeliveryCity(string $deliveryCity): static
    {
        $this->deliveryCity = $deliveryCity;
        return $this;
    }

    public function getDeliveryPhone(): ?string
    {
        return $this->deliveryPhone;
    }

    public function setDeliveryPhone(?string $deliveryPhone): static
    {
        $this->deliveryPhone = $deliveryPhone;
        return $this;
    }

    public function getDeliveryFullName(): string
    {
        return $this->deliveryFirstName . ' ' . $this->deliveryLastName;
    }

    private function generateReference(): string
    {
        return 'MC-' . date('Y') . '-' . strtoupper(substr(uniqid(), -8));
    }

    public function getTotalPriceAsFloat(): float
    {
        return (float) $this->totalPrice;
    }
}