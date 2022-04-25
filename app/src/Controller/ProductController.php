<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]
class ProductController extends AbstractController
{
    public function __construct(
        private ProductRepository   $productRepo,
        private SerializerInterface $serializer,
        private ManagerRegistry     $doctrine
    )
    {
        $this->_em = $this->doctrine->getManager();
    }

    #[Route('/products', name: 'products.getAll', methods: 'GET')]
    public function getAllProducts(): ?JsonResponse
    {
        $products = $this->productRepo->findALl();
        if ($products == null) return new JsonResponse("Sorry, no product matches this id", 404);
        return new JsonResponse($this->displayProducts($products), 200);
    }

    #[Route('/products/{productId}', name: 'products.getInfos', methods: 'GET')]
    public function getProductInfos(Request $req): ?JsonResponse
    {
        $id = $req->attributes->get('productId');
        $product = $this->productRepo->find($id);
        if ($product === null) return new JsonResponse("Sorry, no product matches this id", 404);
        return new JsonResponse($this->displayProduct($product), 200);
    }

    #[Route('/products', name: 'product.add', methods: 'POST')]
    public function addProduct(Request $req): JsonResponse
    {
        $data = json_decode($req->getContent());

        $product = new Product();
        $product->setName($data->name);
        $product->setDescription($data->description);
        $product->setPhoto($data->photo);
        $product->setPrice($data->price);

        $this->_em->persist($product);
        $this->_em->flush();

        return new JsonResponse($this->displayProduct($product), 201);
    }

    #[Route('/products/{productId}', name: 'product.update', methods: 'PATCH')]
    public function updateProduct(Request $req): JsonResponse
    {
        $data = json_decode($req->getContent());
        $id = $req->attributes->get('productId');

        $product = $this->productRepo->find($id);
        if ($product === null) return new JsonResponse("Sorry, no product matches this id", 404);

        if (isset($data->name)) {
            $product->setName($data->name);
        }
        if (isset($data->description)) {
            $product->setDescription($data->description);
        }
        if (isset($data->photo)) {
            $product->setPhoto($data->photo);
        }
        if (isset($data->price)) {
            $product->setPrice($data->price);
        }

        $this->_em->flush();

        return new JsonResponse($this->displayProduct($product));
    }

    #[Route('/products/{productId}', name: 'product.delete', methods: 'DELETE')]
    public function deleteProduct(Request $req): JsonResponse
    {
        $id = $req->attributes->get('productId');
        $product = $this->productRepo->find($id);
        if ($product === null) return new JsonResponse("Sorry, no product matches this id", 404);
        $successMessage = "The product named ".$product->getName()." with the id ".$product->getId()." was deleted successfully";

        $this->_em->remove($product);
        $this->_em->flush();


        return new JsonResponse($successMessage, 200);
    }

    public function displayProduct($passedProduct)
    {
        return array(
            'id' => $passedProduct->getId(),
            'name' => $passedProduct->getName(),
            'description' => $passedProduct->getDescription(),
            'photo' => $passedProduct->getPhoto(),
            'price' => $passedProduct->getPrice()
        );
    }

    public function displayProducts($passedProducts): array
    {
        $returnArray = array();
        foreach ($passedProducts as $product) {
            $tempArray = array(
                'id' => $product->getId(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'photo' => $product->getPhoto(),
                'price' => $product->getPrice());
            array_push($returnArray, $tempArray);
        }
        return $returnArray;
    }
}
