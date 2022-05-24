<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Matiere;
use App\Entity\Produit;
use App\Form\CategorieType;
use App\Form\MatiereType;
use App\Form\ProduitType;
use App\Repository\CategorieRepository;
use App\Repository\MatiereRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{

    #[Route('ajoutProduit', name: 'ajoutProduit')]
    public function ajoutProduit(Request $request, EntityManagerInterface $manager): Response
    {
        // Fonction ayant pour but d'afficher le formulaire puis de traiter les données de ce formulaire.
        // Les formulaire symfony transitent en méthode post, il nous faut donc
        // appeler Request $request (de HttpFoundation) étant la classe traitant les données des superglobales ($_POST, $_GET, $_cookie ..)
        // Afin de communiquer avec la BDD pour insérer notre produit il faudra systématiquement appeler EntityManagerInterface $manager (de l'ORM doctrine)

        $produit = new Produit();
        // ici on instancie un nouvel objet Produit vide, que l'on va charger avec les informations réceptionnées du formulaire grace à Request
        dump($produit);
        $form = $this->createForm(ProduitType::class, $produit, ['ajout' => true]);
        // ici on instancie un objet Form qui va controller automatiquement la correspondance des champs de formulaire demandés dans ProduitType avec les propriétés de notre entité Produit
        // La méthode createForm() attend 2 arguments, le 1er le nom du formulaire (le type) à utiliser , en second l'entité correspondante à ce formulaire

        $form->handleRequest($request);//  on utilise la méthode handlerequest() de notre objet $form afin de traiter les données

        dump($produit);// ici l'objet produit est rempli des données du formulaire

        if ($form->isSubmitted() && $form->isValid()) {// si le formulaire a été soumis via notre boutton submit et que les controles de contraintes effectué sur notre entité et notre formulaire sont correct

            $image = $form->get('photo')->getData();
            //dd($image);

            $nomImage = date('YmdHis') . '_' . $image->getClientOriginalName();
            // on renomme le fichier photo pour s'assurer de son unicité

            $image->move($this->getParameter('images_directory'), $nomImage);
            // on utilise la méthode move() équivalente à la fonction copy() en php.
            // afin d'uploader le fichier temporaire dans l'app

            $produit->setPhoto('upload/' . $nomImage);

            $manager->persist($produit);// ici on prépare la requête
            $manager->flush(); // on execute

            $this->addFlash('success', 'Produit ajouté avec success');

            return $this->redirectToRoute('listeProduit');


        }

        // on renvoie à notre template la vue du formulaire chargée dans une variable 'form' grace à la méthode createView()
        return $this->render('admin/ajoutProduit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('listeProduit', name: 'listeProduit')]
    public function listeProduit(ProduitRepository $produitRepository): Response
    {
        $produits = $produitRepository->findAll();


        return $this->render('admin/listeProduit.html.twig', [
            'produits' => $produits

        ]);
    }

    #[Route('modificationProduit/{id}', name: 'modificationProduit')]
    public function modificationProduit(Produit $produit, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(ProduitType::class, $produit, ['modification' => true]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $image = $form->get('modifPhoto')->getData();

            if ($image): // si on sélectionné une photo en modification

                $nomImage = date('YmdHis') . '_' . $image->getClientOriginalName();

                $image->move($this->getParameter('images_directory'), $nomImage);
                $photo = str_replace('upload/', '', $produit->getPhoto());
                unlink($this->getParameter('images_directory') . '/' . $photo);

                $produit->setPhoto('upload/' . $nomImage);
            endif;

            $manager->persist($produit);// ici on prépare la requête
            $manager->flush(); // on execute

            $this->addFlash('success', 'Produit modifié avec success');

            return $this->redirectToRoute('listeProduit');


        }


        return $this->render('admin/modificationProduit.html.twig', [
            'form' => $form->createView(),
            'produit' => $produit
        ]);
    }


    #[Route('suppressionProduit/{id}', name: 'suppressionProduit')]
    public function suppressionProduit(Produit $produit, EntityManagerInterface $em): Response
    {
        $em->remove($produit);  // prepare la requête de suppression
        $em->flush(); // on execute
        $this->addFlash('success', 'Produit supprimé');

        return $this->redirectToRoute('listeProduit');

    }


    #[Route('ajoutCategorie', name: 'ajoutCategorie')]
    #[Route('modificationCategorie/{id}', name: 'modificationCategorie')]
    public function gestionCategorie(CategorieRepository $categorieRepository, Request $request, EntityManagerInterface $manager, $id = null): Response
    {

        // $categories = $categorieRepository->findAll();
        $categories = $categorieRepository->findBY([], ['nom' => 'ASC']);
        if (!$id):
            $categorie = new Categorie();
        else:
            $categorie = $categorieRepository->find($id);
        endif;

        $form = $this->createForm(CategorieType::class, $categorie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager->persist($categorie);
            $manager->flush();
            if (!$id):
                $this->addFlash('success', 'Catégorie ajoutée');
            else:
                $this->addFlash('success', 'Catégorie modifiée');
            endif;

            return $this->redirectToRoute('ajoutCategorie');

        }


        return $this->render('admin/gestionCategorie.html.twig', [
            'form' => $form->createView(),
            'categories' => $categories

        ]);
    }


    #[Route('suppressionCategorie/{id}', name: 'suppressionCategorie')]
    public function suppressionCategorie(Categorie $categorie, EntityManagerInterface $em, CategorieRepository $categorieRepository, $id): Response
    {
        $categorie = $categorieRepository->find($id);
        $categorieRepository->remove($categorie);  // prepare la requête de suppression
        $em->flush(); // on execute
        $this->addFlash('success', 'Categorie supprimé');

        return $this->redirectToRoute('ajoutCategorie');

    }

    #[Route('ajoutMatiere', name: 'ajoutMatiere')]
    #[Route('modificationMatiere/{id}', name: 'modificationMatiere')]
    public function gestionMatiere(MatiereRepository $repository, EntityManagerInterface $manager, Request $request, $id = null): Response
    {

        $matieres = $repository->findAll();

        if ($id):
            $matiere = $repository->find($id);
        else:
            $matiere = new Matiere();
        endif;

        $form = $this->createForm(MatiereType::class, $matiere);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager->persist($matiere);
            $manager->flush();

            if ($id):
                $this->addFlash('success', 'Matière modifiée');
            else:
                $this->addFlash('success', 'Matière ajoutée');
            endif;

            return $this->redirectToRoute('ajoutMatiere');
        }


        return $this->render('admin/gestionMatiere.html.twig', [
            'form' => $form->createView(),
            'matieres' => $matieres
        ]);
    }

    #[Route('suppressionMatiere/{id}', name: 'suppressionMatiere')]
    public function suppressionMatiere(Matiere $matiere, EntityManagerInterface $manager): Response
    {
        $manager->remove($matiere);
        $manager->flush();
        $this->addFlash('success', 'Matière supprimée');


        return $this->redirectToRoute('ajoutMatiere');
    }


}
