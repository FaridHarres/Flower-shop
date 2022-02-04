<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderCancelController extends AbstractController
{
    #[Route('/commande/erreur/{stripeSessionId}', name: 'order_cancel')]
    public function index($stripeSessionId, EntityManagerInterface $entitymnager, OrderRepository $orderRepository): Response
    {
      
    
            $order=$orderRepository->findOneByStripeSessionId($stripeSessionId);
            
    
            //verifier si l'utilisateur est bien moi eviter que "julien prenne la commande dde romain"
            if(!$order || $order->getUser() != $this->getUser()){
                return $this->redirectToRoute('home');
            }
    
            
            
                
            //envoyer un email a notre client pour lui indiquer l'echec de paiement
    
    
    
    
            
        return $this->render('order_cancel/index.html.twig',[
            'order'=>$order
        ] );
    }
}
