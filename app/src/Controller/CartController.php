<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;
use App\Repository\CartRepository;
use App\Entity\Order;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;

#[Route('/api/carts')]
class CartController extends AbstractController
{
    public function __construct(
        private ProductRepository $productRepo,
        private CartRepository $cartRepo,
        private ManagerRegistry $doctrine
    ) {
        $this->_em = $this->doctrine->getManager();
    }

    #[Route('/{productId}', methods: 'POST', name: 'cart.addProduct')]
    public function addProductToCart(int $productId): JsonResponse
    {
        $productToAdd = $this->productRepo->find($productId);
        if ($productToAdd) {
            $cart = $this->getUser()->getCart();
            $cart->addProductList($productToAdd);
            $this->_em->flush();
            return new JsonResponse("Product added!");
        } else {
            return new JsonResponse("error: product not found", 404);
        }
    }

    #[Route('/{productId}', methods: 'PATCH', name: 'cart.removeProduct')]
    public function removeProductToCart(int $productId): JsonResponse
    {
        $productToAdd = $this->productRepo->find($productId);
        if ($productToAdd) {
            $cart = $this->getUser()->getCart();
            $cart->removeProductList($productToAdd);
            $this->_em->flush();
            return new JsonResponse("Product removed!");
        } else {
            return new JsonResponse("error: product not found", 404);
        }
    }

    #[Route(methods: 'GET', name: 'cart.getState')]
    public function getState(): JsonResponse
    {
        $cart = $this->getUser()->getCart();
        return new JsonResponse($this->displayCart($cart));
    }

    #[Route('/validate/{cartId}', methods: 'POST', name: 'cart.validate')]
    public function validate(): JsonResponse
    {
        $cart = $this->getUser()->getCart();
        if ($cart->getProductList() == []) {
            return new JsonResponse("Impossible to validate an empty cart!", 406);
        }
        $order = new Order();
        $order->setTotalPrice($cart->getTotalPrice());
        $order->setOwner($cart->getOwner());
        foreach($cart->getProductList() as $p) {
            $order->addProduct($p->getId());
        }        
        $order->setCreationDate(new \DateTime());
        $cart->reset();
        $this->_em->persist($order);
        $this->_em->flush();
        return new JsonResponse("Cart validated : see order n°".$order->getId());
    }
    
    public function displayCart($cart) {
        $res = array(
            'id' => $cart->getId(),
            'totalPrice' => $cart->getTotalPrice(),
            'owner' => $cart->getOwner(),
            'productList' => $cart->getProductList()
        );
        return $res;
    }
}
