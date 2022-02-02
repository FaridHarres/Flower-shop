<?php

namespace App\Controller;

use App\Entity\Adress;
use App\Form\AdressType;
use App\Repository\AdressRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccountAdressController extends AbstractController
{
    #[Route('/compte/adresses', name: 'account_adress')]
    public function index(): Response
    {



        return $this->render('account/address.html.twig');
    }




    #[Route('/compte/ajouter-adresse', name: 'account_adress_add')]
    public function add(Request $req, EntityManagerInterface $entitymnager,  SessionInterface $sessionInterface, ProductRepository $productRepository): Response
    {

        $panier = $sessionInterface->get("panier", []);

        //on fabrique les données
        $dataPanier = [];
        $total = 0;

        $adress = new Adress();
        $form = $this->createForm(AdressType::class, $adress);

        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            //lié ladresse à lutiliseur
            $adress->setUser($this->getUser());
            $entitymnager->persist($adress);
            $entitymnager->flush();

            foreach ($panier as $id => $quantite) {

                $product = $productRepository->find($id);
                if (!empty($panier[$id])) {

                   
                    return $this->redirectToRoute('order');

                }else{
                    return $this->redirectToRoute('account_adress');
                }
            }
            
        };
        return $this->render('account/address_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }







    #[Route('/compte/modifier-adresse/{id}', name: 'account_adress_edit')]
    public function edit(Request $req, AdressRepository $adressRepository, EntityManagerInterface $entitymnager): Response
    {
        $id = $req->get('id');
        $adress = $adressRepository->find($id);

        if (!$adress || $adress->getUser() != $this->getUser()) {

            return $this->redirectToRoute('account_adress');
        }

        $form = $this->createForm(AdressType::class, $adress);

        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            //lié ladresse à lutiliseur

            $entitymnager->flush();
            return $this->redirectToRoute('account_adress');
        };
        return $this->render('account/address_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/compte/supprimer-adresse/{id}', name: 'account_adress_delete')]
    public function delete(Request $req,  AdressRepository $adressRepository, EntityManagerInterface $entitymnager): Response
    {
        $id = $req->get('id');
        $adress = $adressRepository->find($id);

        if ($adress && $adress->getUser() == $this->getUser()) {
            $entitymnager->remove($adress);
            $entitymnager->flush();
            
            return $this->redirectToRoute('account_adress');
        }

     

        
    }
}
