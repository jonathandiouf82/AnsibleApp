<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]
class OrderController extends AbstractController
{
    public function __construct(
        private OrderRepository   $orderRepo,
        private SerializerInterface $serializer,
        private ManagerRegistry     $doctrine
    )
    {
        $this->_em = $this->doctrine->getManager();
    }

    #[Route('/orders', name: 'order.getAll', methods: 'GET')]
    public function getAllOrders(): ?JsonResponse
    {
        $orders = $this->orderRepo->findBy(['owner'=>$this->getUser()->getId()]);
        if ($orders == null) return new JsonResponse("You don't have any orders yet.", 404);
        return new JsonResponse($this->displayOrders($orders));
    }

    #[Route('/orders/{orderId}', name: 'order.getInfos', methods: 'GET')]
    public function getProductInfos(Request $req): ?JsonResponse
    {
        $id = $req->attributes->get('orderId');
        $order = $this->orderRepo->find($id);
        if ($order === null) return new JsonResponse("Sorry, no order matches this id", 404);
        if($order->getOwner()->getId()===$this->getUser()->getId()) return new JsonResponse($this->displayOrder($order));
        return new JsonResponse("You cannot access this order, it is not yours.", 403);
    }

    public function displayOrder($passedOrder)
    {
        return array(
            'id' => $passedOrder->getId(),
            'totalPrice' => $passedOrder->getTotalPrice(),
            'creationDate' => $passedOrder->getCreationDate(),
            'products' => $passedOrder->getProducts());
    }

    public function displayOrders($passedOrders): array
    {
        $returnArray = array();
        foreach ($passedOrders as $order) {
            $tempArray = array(
                'id' => $order->getId(),
                'totalPrice' => $order->getTotalPrice(),
                'creationDate' => $order->getCreationDate(),
                'products' => $order->getProducts());
            array_push($returnArray, $tempArray);
        }
        return $returnArray;
    }
}
