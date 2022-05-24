<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Matiere;
use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Config\Security\ProviderConfig\EntityConfig;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        if($options['ajout']==true):




        $builder



            ->add('matieres', EntityType::class,[
                'required'=>false,
                'label'=>'Matieres',
                'class'=>Matiere::class,
                'choice_label'=>'nom',
                'multiple'=>'true',
                'attr'=>[
                    'class'=>'select2',
                    'date-placeholder'=>'Saisissez une ou des matieres'
                ]
            ])


            ->add('categorie', EntityType::class,[
                'required'=>false,
                'label'=>'Categorie',
                'class'=>Categorie::class,
                'choice_label'=>'nom'
            ])
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
        elseif ($options['modification']==true)://Si on est en modification, on demande l'envoi de ce formulaire

            $builder

                ->add('categorie', EntityType::class,[
                    'required'=>false,
                    'label'=>'Categorie',
                    'class'=>Categorie::class,
                    'choice_label'=>'nom',
                    'placeholder' =>'Saisissez une categorie',

                ])

               ->add('matieres', EntityType::class,[
                   'required'=>false,
                  'label'=>'Matieres',
                  'class'=>Matiere::class,
                   'choice_label'=>'nom',
                   'multiple'=>'true',
                   'attr'=>[
                       'class'=>'select2',
                       'date-placeholder'=>'Saisissez une ou des matieres'
                   ]
                ])


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
                ->add('modifPhoto', FileType::class, [
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

            endif;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
            'ajout'=>false,
            'modification'=>false
        ]);
    }
}
