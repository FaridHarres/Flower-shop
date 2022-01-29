<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AccountPasswordController extends AbstractController
{
    // #[Route('/compte/edit-password', name: 'account_password',  methods: ['GET', 'POST'])]
    // public function updatepassword(Request $request, UserPasswordHasherInterface $encodeur, EntityManagerInterface $entitymnager, UserInterface $user): Response
    // {
    //     $user = $this->getUser();
    //     $form= $this->createForm(ChangePasswordType::class, $user);

    //     $form->handleRequest($request);

    //     if($form->isSubmitted()&& $form->isValid()){
            
            
    //         if($encodeur->isPasswordValid($user, $oldpsw)){
                
    //             $oldpsw = $request->get('change_password')['old_password'];
                
    //             $newpsw = $request->get('change_password')['new_password']['first'];
                
    //             $password=  $encodeur->hashPassword($user, $newpsw);

    //             $user->setPassword($password);
               
    //             $entitymnager ->flush();
    //         }
    //     }

    //     return $this->render('account/password.html.twig', [
    //         'form' => $form->createView()
    //     ]
    //     );
    // }

    #[Route('/compte/edit-password', name: 'account_password',  methods: ['GET', 'POST'])]
    public function updatepassword(Request $request, UserPasswordHasherInterface $encodeur, EntityManagerInterface $entitymnager): Response
    {
        $notification = null;
        $user = $this->getUser();
        $form= $this->createForm(ChangePasswordType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted()&& $form->isValid()){
            
            $old_psw = $form->get('old_password')->getData();
            
            
            if($encodeur->isPasswordValid($user, $old_psw)){
                $newpsw= $form->get('new_password')->getData();
                
                $psw = $encodeur->hashPassword($user, $newpsw );

                $this->getUser()->setPassword($psw);
                $entitymnager ->flush();
                $notification = 'votre mot de passe a bien ete mis a jour.';
            }else{
                $notification = 'votre mot de passe est incorrect';
            }
        }

        return $this->render('account/password.html.twig', [
            'form' => $form->createView(),
            'notification' =>$notification
        ]
        );
    }
}
