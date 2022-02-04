<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


//premiere solution
class CartController extends AbstractController
{


    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/mon-panier', name: 'cart')]
    public function index(SessionInterface $sessionInterface, ProductRepository $productRepository): Response
    {

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

        return $this->render('cart/index.html.twig', compact("dataPanier", "total"));
    }






    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/error', name: 'error')]
    public function error(): Response
    {

        return $this->render('product/erreur.html.twig');
    }







    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/cart/add/{id}', name: 'add_to_cart')]
    public function add(Request $req, SessionInterface $sessionInterface, ProductRepository $productRepository): Response //il est aller chercher la class Cart qui la refiler à cart pour pouvoir utiliser les propriété de la class 
    {

        // on recupere le panier actuel
        $id = $req->get('id');
        $panier = $sessionInterface->get("panier", []);
        $product = $productRepository->find($id);

        if (!empty($panier[$id])) {

            if ($product) {

                $panier[$id]++;
            } else{
                return $this->redirectToRoute('error');
            }

            //on sauvegarde dans la session
            $sessionInterface->set("panier", $panier);
            return $this->redirectToRoute('cart');
        } else {
            $panier[$id] = 1;
        }

        //on sauvegarde dans la session
        $sessionInterface->set("panier", $panier);
        return $this->redirectToRoute('products');

    }



    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/cart/remove/{id}', name: 'remove_my_cart')]
    public function remove(Request $req, SessionInterface $sessionInterface): Response //il est aller chercher la class Cart qui la refiler à cart pour pouvoir utiliser les propriété de la class 
    {

        // on recupere le panier actuel
        $id = $req->get('id');
        $panier = $sessionInterface->get("panier", []);

        if (!empty($panier[$id])) {
            if ($panier[$id] > 1) {
                $panier[$id]--;
            } else {
                unset($panier[$id]);
            }
        }

        //on sauvegarde dans la session
        $sessionInterface->set("panier", $panier);

        return $this->redirectToRoute('cart');
    }


    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/cart/delete/{id}', name: 'delete_cart')]
    public function delete(Request $req, SessionInterface $sessionInterface): Response //il est aller chercher la class Cart qui la refiler à cart pour pouvoir utiliser les propriété de la class 
    {

        // on recupere le panier actuel
        $id = $req->get('id');
        $panier = $sessionInterface->get("panier", []);

        if (!empty($panier[$id])) {

            unset($panier[$id]);
        }

        //on sauvegarde dans la session
        $sessionInterface->set("panier", $panier);

        return $this->redirectToRoute('cart');
    }


    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/cart/delete}', name: 'delete_all')]
    public function deleteAll(SessionInterface $sessionInterface): Response //il est aller chercher la class Cart qui la refiler à cart pour pouvoir utiliser les propriété de la class 
    {

        // on recupere le panier actuel

        $sessionInterface->remove("panier", []);




        return $this->redirectToRoute('cart');
    }




}
