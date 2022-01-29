<?php

namespace App\Controller;

use App\Classe\Search;
use App\Form\Searchtype;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    #[Route('/nos-produit', name: 'products')]
    public function index(ProductRepository $ProductRepository, request $req): Response
    {
        // tu va me chercher tous les prduit dans la table product
        $products = $ProductRepository->findAll();


        $search = new Search();
        $form = $this->createForm(Searchtype::class, $search);

        $form->handleRequest($req);

        if($form->isSubmitted()&& $form->isValid()){
            // $search= $form->getData();

            $products = $ProductRepository->findwithsearch($search);

        }



        return $this->render('product/index.html.twig', [
            "products" => $products,
            "form"=>  $form->createView(),
        ]);
    }

    #[Route('/produit/{Slug}', name: 'product')] //le slug vien de lentité ^produit
    public function show(ProductRepository $ProductRepository, Request $req): Response
    {
        $slug = $req->get('Slug');

        $product = $ProductRepository->findOneBy(['Slug' => $slug]);

        if (!$product) {
            return $this->redirectToRoute('products');
        }
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }
}
