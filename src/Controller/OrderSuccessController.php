<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderSuccessController extends AbstractController
{   
    
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/commande/merci/{stripeSessionId}', name: 'order_validate')]
    public function index($stripeSessionId, EntityManagerInterface $entitymnager, OrderRepository $orderRepository, SessionInterface $sessionInterface): Response
    {

        $order=$orderRepository->findOneByStripeSessionId($stripeSessionId);
        

        //verifier si l'utilisateur est bien moi eviter que "julien prenne la commande dde romain"
        if(!$order || $order->getUser() != $this->getUser()){
            return $this->redirectToRoute('home');
        }

        
        
        
        //modifier le status ispaid en mettant le status 1

        if(!$order->getIsPaid()){
            $sessionInterface->remove("panier", []);
            $order->setIsPaid(1);
            
            $entitymnager->flush();





        }


        //afficher les infos de la commande de l'utilisateur 
        return $this->render('order_success/index.html.twig',[
            'order'=>$order
        ]);
    }
}
