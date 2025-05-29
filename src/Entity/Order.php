<?php
// src/Entity/Order.php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name:'`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'order')]
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

#[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $deliveryZipCode = null;

    #[ORM\Column(length: 100)]
    private ?string $deliveryCity = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $deliveryPhone = null;

    // Adresse de facturation
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $billingFirstName = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $billingLastName = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $billingAddress = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $billingZipCode = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $billingCity = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $billingPhone = null;

    // Téléphone global (optionnel, si tu préfères un champ générique)
    #[ORM\Column(length: 20, nullable: true)]
    private ?string $phone = null;

    // Notes / commentaires
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;
    private Collection $orderItems;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->reference = $this->generateReference();
        $this->orderItems = new ArrayCollection();

    }

    // Getters & setters basiques (id, user, reference, totalPrice, status, orderDetails, dates)

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
public function getTotal(): float
{
    return $this->getTotalPrice();
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

    // Livraison

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

public function setDeliveryZipCode(?string $deliveryZipCode): self
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
        return trim($this->deliveryFirstName . ' ' . $this->deliveryLastName);
    }

    // Facturation

    public function getBillingFirstName(): ?string
    {
        return $this->billingFirstName;
    }

    public function setBillingFirstName(?string $billingFirstName): static
    {
        $this->billingFirstName = $billingFirstName;
        return $this;
    }

    public function getBillingLastName(): ?string
    {
        return $this->billingLastName;
    }

    public function setBillingLastName(?string $billingLastName): static
    {
        $this->billingLastName = $billingLastName;
        return $this;
    }

    public function getBillingAddress(): ?string
    {
        return $this->billingAddress;
    }

    public function setBillingAddress(?string $billingAddress): static
    {
        $this->billingAddress = $billingAddress;
        return $this;
    }

    public function getBillingZipCode(): ?string
    {
        return $this->billingZipCode;
    }

    public function setBillingZipCode(?string $billingZipCode): static
    {
        $this->billingZipCode = $billingZipCode;
        return $this;
    }

    public function getBillingCity(): ?string
    {
        return $this->billingCity;
    }

    public function setBillingCity(?string $billingCity): static
    {
        $this->billingCity = $billingCity;
        return $this;
    }

    public function getBillingPhone(): ?string
    {
        return $this->billingPhone;
    }

    public function setBillingPhone(?string $billingPhone): static
    {
        $this->billingPhone = $billingPhone;
        return $this;
    }

    public function getBillingFullName(): string
    {
        return trim($this->billingFirstName . ' ' . $this->billingLastName);
    }

    // Téléphone global (optionnel)

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;
        return $this;
    }

    // src/Entity/Order.php

private ?string $deliveryPostalCode = null;

public function getDeliveryPostalCode(): ?string
{
    return $this->deliveryPostalCode;
}

public function setDeliveryPostalCode(string $deliveryPostalCode): self
{
    $this->deliveryPostalCode = $deliveryPostalCode;

    return $this;
}

    // Notes

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;
        return $this;
    }

    // Utilitaires

    private function generateReference(): string
    {
        return 'MC-' . date('Y') . '-' . strtoupper(substr(uniqid(), -8));
    }

    public function getTotalPriceAsFloat(): float
    {
        return (float) $this->totalPrice;
    }


    
    

    /**
     * @return Collection|OrderItem[]
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

}
