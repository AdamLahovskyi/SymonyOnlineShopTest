<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    #[Route('/api/products',methods: ['GET'])]
    public function getProducts(EntityManagerInterface $entityManager): Response
    {
        $productsRepository = $entityManager->getRepository(Product::class);
        $products = $productsRepository->findAll();

        return $this->json($products, Response::HTTP_OK);

    }

    #[Route('/api/product/{id}', methods: ['GET'])]
    public function getProduct(EntityManagerInterface $entityManager, $id): Response
    {
        $product = $entityManager->getRepository(Product::class)->find($id);

        return $this->json($product, Response::HTTP_OK);

    }

    #[Route('/api/create-product', methods: ['POST'])]
    public function createProduct(EntityManagerInterface $entityManager, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        if(empty($data['name'])||empty($data['price'])||empty($data['description'])||empty($data['stock'])||empty($data['orderItems'])){
            return $this->json(null, Response::HTTP_BAD_REQUEST);
        }
        $existingProduct = $entityManager->getRepository(Product::class)->findOneBy(['name' => $data['name']]);
        if($existingProduct){
            return $this->json(['error'=>'Product with this email already exists'], Response::HTTP_BAD_REQUEST);
        }
        $product = new Product();
        $product
            ->setName($data['name'])
            ->setPrice($data['price'])
            ->setDescription($data['description'])
            ->setStock($data['stock'])
            ->setCreatedAt(new \DateTime())
            ->setOrderItems($data['orderItems']);

        $entityManager->persist($product);
        $entityManager->flush();

        return $this->json($product, Response::HTTP_CREATED);
    }

    #[Route('/api/product/{id}', methods: ['PUT'])]
    public function patchProduct(EntityManagerInterface $entityManager, $id, Product $product): Response
    {
        $productRepository = $entityManager->getRepository(Product::class);
        $product = $productRepository->find($id);

        if(!$product) {
            return $this->json(['message' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        $product
            ->setName($product->getName())
            ->setPrice($product->getPrice())
            ->setDescription($product->getDescription())
            ->setStock($product->getStock());

        $entityManager->flush($product);

        return $this->json($product, Response::HTTP_ACCEPTED);
    }

    #[Route('/api/product/{id}', methods: ['DELETE'])]
    public function deleteProduct(EntityManagerInterface $entityManager, $id): Response
    {
        $productRepository = $entityManager->getRepository(Product::class);
        $product = $productRepository->find($id);
        if(!$product) {
            return $this->json(['message' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }
        $entityManager->remove($product);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);

    }

}