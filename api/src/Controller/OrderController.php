<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OrderController extends AbstractController
{
    #[Route('/api/orders', methods: ['GET'])]
    public function getOrders(EntityManagerInterface $entityManager):Response
    {
        $ordersRepository = $entityManager->getRepository(Order::class);
        $products = $ordersRepository->findAll();

        return $this->json($products, Response::HTTP_OK);

    }

    #[Route('/api/order/{id}', methods: ['GET'])]
    public function getOrder(EntityManagerInterface $entityManager, $id):Response
    {
        $order = $entityManager->getRepository(Order::class)->find($id);

        return $this->json($order, Response::HTTP_OK);

    }

    #[Route('/api/create-order', methods: ['POST'])]
    public function createOrder(EntityManagerInterface $entityManager, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        if (empty($data['user_id']) || empty($data['total_amount']) || empty($data['status']) || empty($data['orderItems']))
        {
            throw new BadRequestException('bad request');
        }
        $user = $entityManager->getRepository(User::class)->find($data['id']);
        if (!$user) {
            return $this->json(['error' => 'User not found.'], Response::HTTP_NOT_FOUND);
        }
        $order = new Order();
        $order
            ->setUser($user)
            ->setTotalAmount($data['total_amount'])
            ->setStatus($data['status'])
            ->setOrderItems($data['orderItems'])
            ->setCreatedAt(new \DateTime());

        $entityManager->persist($order);
        $entityManager->flush();

        return $this->json($order, Response::HTTP_CREATED);

    }

    #[Route('/api/order/{id}', methods: ['PUT'])]
    public function updateProduct(EntityManagerInterface $entityManager, $id, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $order = $entityManager->getRepository(Order::class)->find($id);

        if(!$order) {
            return $this->json(['message' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        if(empty($data['user_id']) || empty($data['total_amount']) || empty($data['status'])){
            return $this->json(null, Response::HTTP_BAD_REQUEST);
        }
        $user = $entityManager->getRepository(User::class)->find($data['user_id']);
        if(!$user) {
            return $this->json(['message' => 'User not found.'], Response::HTTP_NOT_FOUND);
        }
        $order
            ->setTotalAmount($data['total_amount'])
            ->setStatus($data['status'])
            ->setTotalAmount($data['total_amount']);

        $entityManager->persist($order);
        $entityManager->flush();

        return $this->json($order, Response::HTTP_OK);
    }

    #[Route('/order/{id}', methods: ['DELETE'])]
    public function deleteOrder(EntityManagerInterface $entityManager, $id): Response
    {
        $order = $entityManager->getRepository(Order::class)->find($id);

        if (!$order) {
            return $this->json(['message' => 'Order not found'], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($order);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}