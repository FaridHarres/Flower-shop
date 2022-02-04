<?php

namespace App\Controller;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\ApiOperations\Create;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeController extends AbstractController
{
    #[Route('/commande/create-session', name: 'stripe_create_session')]
    public function index(SessionInterface $sessionInterface, ProductRepository $productRepository,)
    {

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


        foreach ($dataPanier as $product) {
        
            $product_for_stripe[]=[
                'price_data' => [
                    'currency'=>'EUR',
                    'unit_amount' => $product['produit']->getprice() ,
                    'product_data' => [
                        'name' => $product['produit']->getName(),
                        'images'=>[$YOUR_DOMAIN."/uploads/".$product['produit']->getillustration()],
                            ],
                        ],
                    
                'quantity' =>($product['quantite']),
              

            ];

        
        }
         //initialisation de stripe dans le tunel de commande
         Stripe::setApiKey('sk_test_51KP6CXGygG29KtKWetroJJvr4jfOXaHP311A0sDEvSJBlUzjPc9uMWiVlraMgHgERmGdOYgvMZn1P5rcxwK3L5CT00uMDqvqEi');

           
         $checkout_session = Session::Create([
             'payment_method_types'=> ['card'],
           'line_items' => [
             $product_for_stripe
           ],
           'mode' => 'payment',
             'success_url' => $YOUR_DOMAIN . '/success.html',
             'cancel_url' => $YOUR_DOMAIN . '/cancel.html',
         ]);

         $response = new JsonResponse( ['id'=> $checkout_session->id]);

         return $response;
        
    }
}
