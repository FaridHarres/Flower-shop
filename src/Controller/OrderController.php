<?php

namespace App\Controller;


use Stripe\Stripe;
use App\Entity\Order;
use App\Form\OrderType;
use App\Entity\OrderDetails;
use Stripe\Checkout\Session;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// use Symfony\Component\HttpFoundation\Session\Session;

class OrderController extends AbstractController
{

    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/commande', name: 'order')]
    public function index(SessionInterface $sessionInterface, ProductRepository $productRepository, Request $req): Response
    {


        $panier = $sessionInterface->get("panier", []);

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

        if (!$this->getUser()->getAdresses()->getValues()) {

            return $this->redirectToRoute('account_adress_add');
        }

        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);

        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'cart' => $dataPanier
        ]);
    }



    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/commande/recapitulatif', name: 'order_recap', methods: ['POST'])]
    public function add(SessionInterface $sessionInterface, ProductRepository $productRepository, Request $req,  EntityManagerInterface $entitymnager): Response
    {


        $panier = $sessionInterface->get("panier", []);

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
        // dd($dataPanier);

        if (!$this->getUser()->getAdresses()->getValues()) {

            return $this->redirectToRoute('account_adress_add');
        }

        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);

        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $date = new \DateTimeImmutable();
            $carriers = $form->get('carriers')->getData();
            $delivry = $form->get('adresses')->getData();

            $delivry_content = $delivry->getFirstname() . ' ' . $delivry->getLastname();

            $delivry_content .= '<br/>' . $delivry->getPhone();
            if ($delivry->getCompagny()) {
                $delivry_content .= '<br/>' . $delivry->getCompagny();
            }
            $delivry_content .= '<br/>' . $delivry->getAdress();
            $delivry_content .= '<br/>' . $delivry->getCodepostal() . '' . $delivry->getCity();
            $delivry_content .= '<br/>' . $delivry->getCountry();
            $order = new Order();
            $order->setUser($this->getUser());
            $order->setCreatedAt($date);
            $order->setCarrierName($carriers->getName());
            $order->setCarrierprice($carriers->getprice());
            $order->setDelivery($delivry_content);
            $order->setisPaid(0);
            $entitymnager->persist($order);



                foreach ($dataPanier as $product) {

                    //dd($dataPanier);

                    $orderDetails = new OrderDetails();
                    $orderDetails->setMyOrder($order);
                    $orderDetails->setProduct($product['produit']->getName());

                    $orderDetails->setQuantity($product['quantite'])->getQuantity();
                    $orderDetails->setPrice($product['produit']->getprice() / 100);
                    // dd($orderDetails);
                    $orderDetails->setTotal($product['produit']->getPrice() / 100 * $product['quantite']);
                    $entitymnager->persist($orderDetails);

                };
// dd( $product_for_stripe);


            //$entitymnager->flush();


           
           

            return $this->render('order/add.html.twig', [

                'cart' => $dataPanier,
                'carrier' => $carriers,
                'delivery' => $delivry_content,
               
            ]);
        }

        return $this->redirectToRoute('cart');
    }
}
