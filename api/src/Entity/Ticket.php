<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\TicketRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TicketRepository::class)]
#[ApiResource(
    operations:[
        new Get(normalizationContext: ['groups' => ['ticket:get']]),
        new GetCollection(normalizationContext: ['groups' => ['ticket:get']]),
        new Post(normalizationContext: ['groups' => ['ticket:get']], denormalizationContext: ['groups' => ['ticket:post']]),
        new Put(security: "is_granted('ROLE_ADMIN')"),
        new Patch(security: "is_granted('ROLE_ADMIN')"),
        new Delete(security: "is_granted('ROLE_ADMIN')")
    ],
    normalizationContext: ['groups' => ['ticket:get']],
    denormalizationContext: ['groups' => ['ticket:post']]
)]
class Ticket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?float $price = null;
    #[Groups([
        'user:get',
        'ticket:get',
        'order:get',
    ])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $purchase_date = null;
    #[Groups([
        'user:get',
        'ticket:get',
        'order:get',
    ])]
    #[ORM\Column(length: 255)]
    private ?string $status = null;
    #[Groups([
        'ticket:get',
        'ticket:post',
    ])]
    #[ORM\OneToOne(mappedBy: 'ticket', cascade: ['persist', 'remove'])]
    private ?Order $order = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getPurchaseDate(): ?\DateTimeInterface
    {
        return $this->purchase_date;
    }

    public function setPurchaseDate(\DateTimeInterface $purchase_date): self
    {
        $this->purchase_date = $purchase_date;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): self
    {
        // set the owning side of the relation if necessary
        if ($order->getTicket() !== $this) {
            $order->setTicket($this);
        }

        $this->order = $order;

        return $this;
    }
}
