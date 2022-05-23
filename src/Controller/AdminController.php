<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Produit;
use App\Form\CategorieType;
use App\Form\ProduitType;
use App\Repository\CategorieRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{

    #[Route('ajoutProduit', name: 'ajoutProduit')]
    public function ajoutProduit(Request $request,EntityManagerInterface $manager): Response
    {
        // Fonction ayant pour but d'afficher le formulaire puis de traiter les données de ce formulaire.
        // Les formulaire symfony transitent en méthode post, il nous faut donc
        // appeler Request $request (de HttpFoundation) étant la classe traitant les données des superglobales ($_POST, $_GET, $_cookie ..)
        // Afin de communiquer avec la BDD pour insérer notre produit il faudra systématiquement appeler EntityManagerInterface $manager (de l'ORM doctrine)

        $produit= new Produit();
        // ici on instancie un nouvel objet Produit vide, que l'on va charger avec les informations réceptionnées du formulaire grace à Request
        dump($produit);
        $form=$this->createForm(ProduitType::class, $produit, ['ajout'=>true]);
        // ici on instancie un objet Form qui va controller automatiquement la correspondance des champs de formulaire demandés dans ProduitType avec les propriétés de notre entité Produit
        // La méthode createForm() attend 2 arguments, le 1er le nom du formulaire (le type) à utiliser , en second l'entité correspondante à ce formulaire

        $form->handleRequest($request);//  on utilise la méthode handlerequest() de notre objet $form afin de traiter les données

        dump($produit);// ici l'objet produit est rempli des données du formulaire

        if ($form->isSubmitted() && $form->isValid()){// si le formulaire a été soumis via notre boutton submit et que les controles de contraintes effectué sur notre entité et notre formulaire sont correct

            $image=$form->get('photo')->getData();
            //dd($image);

            $nomImage=date('YmdHis').'_'.$image->getClientOriginalName();
            // on renomme le fichier photo pour s'assurer de son unicité

            $image->move($this->getParameter('images_directory'), $nomImage);
            // on utilise la méthode move() équivalente à la fonction copy() en php.
            // afin d'uploader le fichier temporaire dans l'app

            $produit->setPhoto('upload/'.$nomImage);

            $manager->persist($produit);// ici on prépare la requête
            $manager->flush(); // on execute

            return $this->redirectToRoute('listeProduit');


        }

        // on renvoie à notre template la vue du formulaire chargée dans une variable 'form' grace à la méthode createView()
        return $this->render('admin/ajoutProduit.html.twig', [
            'form'=>$form->createView()
        ]);
    }

        #[Route('listeProduit', name: 'listeProduit')]
            public function listeProduit(ProduitRepository $produitRepository): Response
            {

            $produits=$produitRepository->findAll();

                return $this->render('admin/listeProduit.html.twig', [
                    'produits'=>$produits


                   
                ]);
            }
        

                #[Route('modificationProduit/{id}', name: 'modification')]
                    public function modificationProduit(Produit $produit,Request $request, EntityManagerInterface $manager): Response
                    {


                        $form=$this->createForm(ProduitType::class, $produit, ['modification'=>true]);


                        $form->handleRequest($request);


                        if ($form->isSubmitted() && $form->isValid()){

                            $image=$form->get('modifPhoto')->getData();

                            if($image): //Si on a sélectionné une photo en modification


                            $nomImage=date('YmdHis').'_'.$image->getClientOriginalName();


                            $image->move($this->getParameter('images_directory'), $nomImage);
                                $photo=str_replace('upload/','', $produit->getPhoto());
                                unlink($this->getParameter('images_directory').'/'.$photo);


                                $produit->setPhoto('upload/'.$nomImage);
                            endif;

                            $manager->persist($produit);// ici on prépare la requête
                            $manager->flush(); // on execute

                            $this->addFlash('success', 'Produit modifié avec success');


                            return $this->redirectToRoute('listeProduit');


                        }

                        return $this->render('admin/modificationProduit.html.twig', [
                            'form'=>$form->createView(),
                            'produit'=>$produit
                        ]);


                                            }

                        #[Route('supressionProduit/{id}', name: 'supressionProduit')]
                            public function supressionProduit(Produit $produit,EntityManagerInterface $manager): Response
                            {
                                $manager->remove($produit); //prepare la requete de suppression
                                $manager->flush(); //on execute
                                $this->addFlash('succes', 'Produit supprimé');

                                return $this->redirectToRoute('listeProduit');

                            }

                            #[Route('ajoutCategorie', name: 'ajoutCategorie')]
                            #[Route('modificationCategorie/{id}', name: 'modificationCategorie')]

                                public function gestionCategorie(CategorieRepository $categorieRepository, Request $request, EntityManagerInterface $manager, $id=null): Response
                                {
                                    $categories=$categorieRepository->findAll();
                                    if (!$id):

                                    $categorie=new Categorie();
                                    else:
                                    $categorie=$categorieRepository->find($id);
                                    endif;

                                    $form=$this->createForm(CategorieType::class, $categorie);

                                    $form->handleRequest($request);

                                    if($form->isSubmitted() && $form->isValid()){

                                        $manager->persist($categorie);
                                        $manager->flush();
                                        if (!$id):
                                        $this->addFlash('succes', 'Categorie ajoutée');
                                        else:
                                            $this->addFlash('succes', 'Categorie ajoutée');
                                        endif;

                                        return $this->redirectToRoute('ajoutCategorie');
                                    }


                                    return $this->render('admin/gestionCategorie.html.twig', [
                                        'form'=>$form->createView(),
                                        'categories'=>$categories

                                    ]);
                                }


    #[Route('supressionCategorie/{id}', name: 'supressionCategorie')]
    public function supressionCategorie(Categorie $categorie,EntityManagerInterface $manager): Response
    {
        $manager->remove($categorie); //prepare la requete de suppression
        $manager->flush(); //on execute
        $this->addFlash('succes', 'Categorie supprimé');

        return $this->redirectToRoute('ajoutCategorie');

    }












}
