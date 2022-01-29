<?php

namespace App\Form;

use App\Classe\Search;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class Searchtype extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('string', TextType::class,[
                'label'=>false,
                'required'=>false,
                'attr'=>[
                    'placeholder'=>'votre recherche...',
                    'class'=>'form-control-sm'
                ]
                ])
            ->add('categories', EntityType::class,[
                'label'=>false,
                'required'=>false,
                'class'=> Category::class, //avec la classe categori car on   a mis un entity type
                'multiple'=>true,
                'expanded'=> true //vue en checkbox

            ])
            ->add('submit', SubmitType::class, [
                'label' => 'FILTRER',
                'attr'=>[
                    'class'=>'btn-block btn-success',
                ]]);
                

            
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Search::class,
            'method'=> 'GET',
            'csrf_protection'=>false,


        ]);
    }


    // avoir un tableau de lurl
    public function getBlockPrefix() 
    {
        return '';
    }


}