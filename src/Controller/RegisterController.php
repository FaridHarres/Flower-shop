<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\User;
use App\Form\RegisterType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterController extends AbstractController
{
    #[Route('/inscription', name: 'register')]
    public function formregister(Request $request, EntityManagerInterface $entitymnager, UserPasswordHasherInterface $encodeur, UserRepository $userRepository): Response
    {

        $notif = null;

        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $user = $form->getData();

            //verifier si lutilisateur est present en database
            $search_mail=$userRepository->findOneByEmail($user->getEmail());

                if(!$search_mail){

                    $psw = $encodeur->hashPassword($user, $user->getPassword());
                    $user->setPassword($psw);
                    $entitymnager->persist($user);
                    $entitymnager->flush();


                    
                    // l'utilisateur doit recevoir un mail de confirmation 
                    $email = new Mail();
                    $content="Bonjour". $user->getFirstName() ."<br/> Bienvenue sur le premier site de revente de fleures non vendu par les fleuristes" ;
                    $email ->send($user->getEmail(), $user->getFirstName(), 'bienvenue sur Green Mind', $content );

                     
                    $notif = 'Bonjour, nous vous confiormons votre inscription, vous pouvez des a present vous rendre sur votre espace client';

                }else{

                    $notif = 'le mail que vous avez renseigné existe deja' ;

                }





        }

        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
            'notif'=>$notif,
        ]);
    }
}
