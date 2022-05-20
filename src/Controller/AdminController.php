<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
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

        $form=$this->createForm(ProduitType::class, $produit);
        // ici on instancie un objet Form qui va controller automatiquement la correspondance des champs de formulaire demandés dans ProduitType avec les propriétés de notre entité Produit
        // La méthode createForm() attend 2 arguments, le 1er le nom du formulaire (le type) à utiliser , en second l'entité correspondante à ce formulaire





        // on renvoie à notre template la vue du formulaire chargée dans une variable 'form' grace à la méthode createView()
        return $this->render('admin/ajoutProduit.html.twig', [
            'form'=>$form->createView()
        ]);
    }







}
