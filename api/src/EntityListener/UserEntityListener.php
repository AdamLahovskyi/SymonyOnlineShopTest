<?php

namespace App\EntityListener;

use App\Entity\User;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserEntityListener
{

    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {}

    /**
     * @param User $user
     * @param PrePersistEventArgs $eventArgs
     * @return void
     */
    public function prePersist(User $user, PrePersistEventArgs $eventArgs)
    {
        $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
    }

}
