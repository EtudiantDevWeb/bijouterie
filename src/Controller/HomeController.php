<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use App\Service\Panier\PanierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(ProduitRepository $produitRepository): Response
    {// on injecte en dépendance ProduitRepository $produitRepository
        //afin de pouvoir utiliser les méthode s'y trouvant
        // Les repository doivent systématiquement être appelé lorsque l'on souhaite récupérer des données provenant de la BDD (Toutes les requêtes de SELECT
        //)

        $produits = $produitRepository->findAll();// equivalence de SELECT * FROM produit;

        return $this->render('home/home.html.twig', [
            'produits' => $produits

        ]);
    }


    #[Route('ajoutPanier/{id}/{param}', name: 'ajoutPanier')]
    public function ajoutPanier(PanierService $panierService, $id, $param): Response
    {
        $panierService->ajout($id);
        $this->addFlash('success', 'Ajouté au panier !!');

        //dd($panierService->panierDetail());
        if ($param == 'home'):
            return $this->redirectToRoute('home');
        else:
            return $this->redirectToRoute('panier');
        endif;
    }

    #[Route('retraitPanier/{id}', name: 'retraitPanier')]
    public function retraitPanier(PanierService $panierService, $id): Response
    {
        $panierService->retrait($id);
        return $this->redirectToRoute('panier');

    }

    #[Route('supprimerPanier/{id}', name: 'supprimerPanier')]
    public function supprimerPanier(PanierService $panierService, $id): Response
    {
        $panierService->supprimer($id);
        return $this->redirectToRoute('panier');

    }

    #[Route('viderPanier', name: 'viderPanier')]
    public function viderPanier(SessionInterface $session): Response
    {
        $session->remove(panier);
        return $this->redirectToRoute('home');

    }


    #[Route('panier', name: 'panier')]
    public function panier(PanierService $panierService): Response
    {

        $panier = $panierService->panierDetail();
        $total = $panierService->total();
        return $this->render('home/panier.html.twig', [

            'panier' => $panier,
            'total' => $total

        ]);
    }


}
