<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(ProduitRepository$produitRepository): Response
    {//on injecte en dépendance ProduitRepository $produitRepository afin de pouvoir utiliser les methodes s'y trouvant
        //Les repositories doivent systematiquement etre appellées lorsque l'on souhaite récupérer des données provennant d'une BDD (toutes les requetes de SELECT)

        $produits=$produitRepository->findAll();



        return $this->render('home/home.html.twig', [
            'produits' => $produits,
        ]);
    }
    
    

        
}
