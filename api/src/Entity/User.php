<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Action\UserAction;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => ['user:get']]),
        new GetCollection(security: "is_granted('ROLE_ADMIN')"),
        new Post(controller: UserAction::class, normalizationContext: ['groups' => ['user:get']], denormalizationContext: ['groups' => ['user:post']]),
        new Put(security: "is_granted('ROLE_ADMIN')"),
        new Delete(security: "is_granted('ROLE_ADMIN')"),
        new Patch(security: "is_granted('ROLE_ADMIN')")
    ],
    normalizationContext: ['groups' => ['user:get']],
    denormalizationContext: ['groups' => ['user:post']]
)]

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    const ROLE_USER= 'ROLE_USER';
    const ROLE_ADMIN= 'ROLE_ADMIN';
    const ROLE_MANAGER= 'ROLE_MANAGER';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        'user:get',
        'user:post',
        'order:get',
    ])]
    private ?int $id = null;
    #[Groups([
        'user:get',
        'user:post',
        'order:get',
    ])]
    #[ORM\Column(length: 255)]
    private ?string $name = null;
    #[Groups([
        'user:get',
        'user:post',
        'order:get',
    ])]
    #[ORM\Column(length: 255)]
    private ?string $email = null;
    #[Groups([
        'user:get',
        'user:post',
        'order:get',
    ])]
    #[ORM\Column(length: 255)]
    private ?string $password = null;
    #[Groups([
        'user:get',
        'user:post',
        'order:get',
    ])]
    #[ORM\Column(type: Types::JSON)]
    private array $roles = [];
    #[Groups([
        'user:get',
        'user:post',
    ])]
    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'user', cascade: ['persist', 'remove'])]
    private Collection $orders;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name):  self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->setUser($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            if ($order->getUser() === $this) {
                $order->setUser(null);
            }
        }

        return $this;
    }
}
