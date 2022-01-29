<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterController extends AbstractController
{
    #[Route('/inscription', name: 'register')]
    public function formregister(Request $request, EntityManagerInterface $entitymnager, UserPasswordHasherInterface $encodeur ): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $user = $form->getData();
            $psw = $encodeur->hashPassword($user, $user->getPassword());
            $user->setPassword($psw);
            $entitymnager ->persist($user);
            $entitymnager ->flush();

        }

        return $this->render('register/index.html.twig',[
            'form'=>$form->createView()
        ]);
    }


}
