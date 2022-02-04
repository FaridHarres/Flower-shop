<?php

namespace App\Controller;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\ApiOperations\Create;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class StripeController extends AbstractController
{

    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/commande/create-session/{reference}', name: 'stripe_create_session')]
    public function index(EntityManagerInterface $entitymnager, $reference, SessionInterface $sessionInterface, ProductRepository $productRepository, OrderRepository $orderRepository)
    {
       
        $order=$orderRepository->findOneByReference($reference);

        // dd($order);

        if(!$order){
            new JsonResponse( ['error'=> 'order']);
        };
        
        //.......................................initialisation du panier.............................................
        $panier = $sessionInterface->get("panier", []);
        //on fabrique les données
        $dataPanier = [];
        $total = 0;
        
        foreach ($panier as $id => $quantite) {
            
            $product = $productRepository->find($id);
            if (!empty($panier[$id])) {
                
                if ($product) {
                    
                    
                    $dataPanier[] = [
                        "produit" => $product,
                        "quantite" => $quantite
                    ];
                    $total += $product->getPrice() * $quantite;
                }
            }
        }
       
        
        
        
        //.............................initialisation de stripe...............................................................................................
        $product_for_stripe =[];
        $YOUR_DOMAIN = 'http://localhost:8000';
        

        foreach ( $order->getOrderDetails()->getValues() as $product) {
       
            $product_object = $productRepository->findOneByName($product->getProduct());
            $product_for_stripe[]=[
                'price_data' => [
                    'currency'=>'EUR',
                    'unit_amount' => $product->getprice()*100 ,
                    'product_data' => [
                        'name' => $product->getProduct(),
                        'images'=>[$YOUR_DOMAIN."/uploads/".$product_object->getillustration()],
                            ],
                        ],
                    
                'quantity' =>$product->getQuantity(),
              

            ];

        
        }
//------------------------initialisation du transporteur-----------------------------------------
        $product_for_stripe[]=[
            'price_data' => [
                'currency'=>'EUR',
                'unit_amount' => $order->getCarrierPrice() ,
                'product_data' => [
                    'name' => $order->getCarrierName(),
                    'images'=>[$YOUR_DOMAIN],
                        ],
                    ],
                
            'quantity' =>1,
          

        ];


         //initialisation de stripe dans le tunel de commande
         Stripe::setApiKey('sk_test_51KP6CXGygG29KtKWetroJJvr4jfOXaHP311A0sDEvSJBlUzjPc9uMWiVlraMgHgERmGdOYgvMZn1P5rcxwK3L5CT00uMDqvqEi');

           
         $checkout_session = Session::Create([
             'customer_email'=>$this->getUser()->getEmail(),
             'payment_method_types'=> ['card'],
           'line_items' => [
             $product_for_stripe
           ],
           'mode' => 'payment',
             'success_url' => $YOUR_DOMAIN . '/commande/merci/{CHECKOUT_SESSION_ID}',
             'cancel_url' => $YOUR_DOMAIN . '/commande/erreur/{CHECKOUT_SESSION_ID}',
         ]);

         $order->setStripeSessionId( $checkout_session->id);
         $entitymnager->flush();

         $response = new JsonResponse( ['id'=> $checkout_session->id]);

         return $response;
        
    }
}
