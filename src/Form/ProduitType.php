<?php

namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class,[
             'required'=>false,
            'label'=>'Nom',
            'attr'=> [
                'placeholder'=>'Saissisez le nom'
                ]

             ])

            ->add('prix', NumberType::class,[
                'required'=>false,
                'label'=>'Prix',
                'attr'=> [
                    'placeholder'=>'Saissisez le prix'
                ]

            ] )
            ->add('photo', FileType::class, [
                'required'=>false,
                'label'=>'Photo',


            ])
            ->add('description', TextareaType::class,[
                'required'=>false,
                'label'=>'Description',
                'attr'=>[
                'placeholder'=>'Saissisez la description'
                    ]


            ])
            ->add('valider', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
