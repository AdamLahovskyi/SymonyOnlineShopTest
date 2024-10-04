<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Action\UserAction;
use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => ['order:get']]),
        new GetCollection(security: "is_granted('ROLE_USER')"),
        new Post(normalizationContext: ['groups' => ['order:get']], denormalizationContext: ['groups' => ['order:post']]),
    ]
)]
#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups([
        'order:get',
        'order:post',
    ])]
    #[ORM\ManyToOne(inversedBy: 'orders')]
    private ?User $user = null;

    #[Groups([
        'user:get',
        'order:get',
        'order:post',
    ])]
    #[ORM\Column(type: Types::DECIMAL)]
    private ?float $total_amount = null;

    #[Groups([
        'user:get',
        'order:get',
        'order:post',
    ])]
    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[Groups([
        'order:get',
    ])]
    #[ORM\OneToMany(targetEntity: OrderItems::class, mappedBy: 'order')]
    private Collection $orderItems;

    #[Groups([
        'user:get',
        'order:get',
    ])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    public function __construct()
    {
        $this->orderItems = new ArrayCollection();
        $this->created_at = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getTotalAmount(): ?float
    {
        return $this->total_amount;
    }

    public function setTotalAmount(float $total_amount): self
    {
        $this->total_amount = $total_amount;
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

    /**
     * @return Collection<int, OrderItems>
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    public function addOrderItem(OrderItems $orderItem): self
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems[] = $orderItem;
            $orderItem->setOrder($this);
        }

        return $this;
    }

    public function removeOrderItem(OrderItems $orderItem): self
    {
        if ($this->orderItems->removeElement($orderItem)) {
            // set the owning side to null (unless already changed)
            if ($orderItem->getOrder() === $this) {
                $orderItem->setOrder(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
    }
}
