<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/api/users', methods: ['GET'])]
    public function getUsers(EntityManagerInterface $entityManager): Response
    {
        $userRepository = $entityManager->getRepository(User::class);
        $users = $userRepository->findAll();

        return new JsonResponse($users);

    }
    #[Route('/api/user/{id}', methods: ['GET'])]
    public function getOne(EntityManagerInterface $entityManager, int $id): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        return $this->json($user, Response::HTTP_OK);

    }
    #[Route('/api/create-user', methods: ['POST'])]
    public function createUser(Request $request, EntityManagerInterface $entityManager, UserPasswordHasher $passwordHasher): Response
    {
        $data = json_decode($request->getContent(), true);
        if(empty($data['name'])||empty($data['email'])||empty($data['password'])){
            return $this->json(null, Response::HTTP_BAD_REQUEST);
        }
        $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']]);
        if($existingUser){
            return $this->json(['error'=>'User with this email already exists'], Response::HTTP_BAD_REQUEST);
        }
        $user = new User();
        $user
            ->setName($data['name'])
            ->setEmail($data['email'])
            ->setAddress($data['address'])
            ->setCreatedAt(new \DateTime());

        $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        return $this->json($user, Response::HTTP_CREATED);
    }
    #[Route('api/user/{id}', methods: ['PUT'])]
    public function patchOne(EntityManagerInterface $entityManager,Request $request,UserPasswordHasher $passwordHasher, int $id): Response
    {
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->find($id);
        if(!$user){
            return $this->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }
        $user
            ->setName($request->get('name'))
            ->setEmail($request->get('email'))
            ->setAddress($request->get('address'));
        $hashedPassword = $passwordHasher->hashPassword($user, $request->get('password'));
        $user->setPassword($hashedPassword);

        $entityManager->flush($user);

        return $this->json($user, Response::HTTP_ACCEPTED);

    }

    #[Route('/api/delete-user/{id}', methods: ['DELETE'])]

    public function deleteOne(EntityManagerInterface $entityManager, int $id): Response
    {
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->find($id);
        if(!$user){
            return $this->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);

    }
}