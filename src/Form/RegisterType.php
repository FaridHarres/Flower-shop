<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' =>'Prenom',
                'attr'=>[
                    'placeholder'=>'veuillez saisir votre prenom']
                ] )
            ->add('lastname', TextType::class, [
                'label' =>'Nom',
                'attr'=>[
                    'placeholder'=>'veuillez saisir votre nom']
                ] )
            ->add('email', EmailType::class,[
                'label' =>'Email',
                'attr'=>[
                    'placeholder'=>'veuillez saisir votre Email']
                ] )
            ->add('password',RepeatedType::class,[
                    'type'=> PasswordType::class,
                    'invalid_message'=>'veuillez saisir deux mot de passe identique',
                    'label' =>'Mot de passe',
                    'required'=> true,
                    'first_options'=>[
                        'label'=>'mot de passe',
                        'attr'=>[
                            'placeholder'=>'veuillez saisir votre mot de passe']
                    ],
                    'second_options'=>['label'=>' confirmer votre mot de passe,',
                    'attr'=>[
                        'placeholder'=>'veuillez saisir votre mot de passe']
                
                    ]
            ])
         
            
            ->add('soumettre', SubmitType::class);//envoi le formulaire a la meme route

        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
