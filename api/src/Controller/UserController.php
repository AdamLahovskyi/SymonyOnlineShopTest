<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

class UserController
{
    public function __construct(private SerializerInterface $serializer, private EntityManagerInterface $entityManager)
    {

    }

    #[IsGranted(User::ROLE_ADMIN)]
    #[Route('/api/create-user', name: 'create_user', methods: ['POST'])]
    public function createUser(Request $request): JsonResponse
    {
        $userData = $request->getContent();

        /** @var User $user */
        $user = $this->serializer->deserialize($userData, User::class, 'json');
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse($userData, Response::HTTP_CREATED);
    }
}
