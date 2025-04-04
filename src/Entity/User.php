<?php

namespace App\Entity;

use App\Enum\SubscriptionType;
use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 20)]
    private ?string $phone = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $subscriptionType = null;

    #[ORM\OneToOne(targetEntity: Address::class, mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Address $address = null;

    #[ORM\OneToOne(targetEntity: PaymentDetails::class, mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?PaymentDetails $payment = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getSubscriptionType(): ?SubscriptionType
    {
        return $this->subscriptionType
            ? SubscriptionType::from($this->subscriptionType)
            : null;
    }

    public function setSubscriptionType(SubscriptionType $subscrtiptionType): static
    {
        $this->subscriptionType = $subscrtiptionType->value;

        return $this;
    }

    // Getters and setters for relationships...
    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(Address $address): self
    {
        $address->setUser($this);
        $this->address = $address;

        return $this;
    }

    public function getPayment(): ?PaymentDetails
    {
        return $this->payment;
    }

    public function setPayment(?PaymentDetails $payment): self
    {
        $payment->setUser($this);
        $this->payment = $payment;

        return $this;
    }
}
