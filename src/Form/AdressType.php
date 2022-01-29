<?php

namespace App\Form;

use App\Entity\Adress;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label'=> 'Quel nom souhaitez vous donner à votre adresse',
                'required'=>false,
                'attr'=>[
                    'placeholder'=> 'Nommez votre adresse'
                ]
            ])
            ->add('firstname', TextType::class, [
                'label'=> 'votre prenom',
                'attr'=>[
                    'placeholder'=> 'Entrez votre prenom'
                ]
            ])
            ->add('lastname', TextType::class, [
                'label'=> 'Votre nom',
                'attr'=>[
                    'placeholder'=> 'Entrez votre nom'
                ]
            ])
            ->add('compagny', TextType::class, [
                'label'=> 'Votre societe',
                'required'=>false,
                'attr'=>[
                    'placeholder'=> '(facultatif) Entrez votre nom de societe'
                ]
            ])
            ->add('adress', TextType::class, [
                'label'=> 'Votre adresse',
                'attr'=>[
                    'placeholder'=> '15 rue des champs-elysées...'
                ]
            ])
            ->add('codepostal', TextType::class, [
                'label'=> 'Votre code postal',
                'attr'=>[
                    'placeholder'=> 'entrez votre Votre code postal'
                ]
            ])
            ->add('city', TextType::class, [
                'label'=> 'Votre ville',
                'attr'=>[
                    'placeholder'=> 'Entrez votre ville'
                ]
            ])
            ->add('country', CountryType::class, [
                'label'=> 'Votre pays ',
                'attr'=>[
                    'placeholder'=> 'votre Pays',
                    'class'=>'d-block p-2'
                ]
            ])
            ->add('phone', TelType::class, [
                'label'=> 'Votre telephone',
                'attr'=>[
                    'placeholder'=> 'Entrez votre Numero de telephone'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label'=> 'Valider',
                'attr'=>[
                    'class'=> 'btn-block btn-success'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Adress::class,
        ]);
    }
}
