<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccountOrderController extends AbstractController
{
    #[Route('/compte/mes-commandes', name: 'account_order')]
    public function index(EntityManagerInterface $entitymnager, OrderRepository $orderRepository ): Response
    {
        $orders = $orderRepository->findSuccessOrders($this->getUser());


        return $this->render('account/order.html.twig',[
            'orders'=>$orders
        ]);
    }

    #[Route('/compte/mes-commandes/{reference}', name: 'account_order_show')]
    public function show($reference,EntityManagerInterface $entitymnager, OrderRepository $orderRepository ): Response
    {
        $order = $orderRepository->findOneByReference($reference);

        if(!$order || $order->getUser() != $this->getUser()){
            return $this->redirectToRoute('account_order');
        }

        return $this->render('account/order_show.html.twig',[
            'order'=>$order
        ]);
    }
}
