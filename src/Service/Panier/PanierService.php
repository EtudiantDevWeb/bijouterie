<?php

namespace App\Service\Panier;

use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PanierService
{

    public $session;
    public $produitRepository;

    public function __construct(SessionInterface $session, ProduitRepository $produitRepository)
    {
        $this->session = $session;
        $this->produitRepository = $produitRepository;

    }


    public function ajout(int $id)
    {
        // on déclare un panier en session qui va charger un tableau
        // avec les id des produits en indice et en valeur la quantité
        $panier = $this->session->get('panier', []);

        // si on a présence d'une quantité à l'indice de l'id passé en argument alors on incrémente la quantité
        if (!empty($panier[$id])):
            $panier[$id]++;

        // sinon on initialise la quantité à 1
        else:
            $panier[$id] = 1;
        endif;

        $this->session->set('panier', $panier);

    }

    public function retrait(int $id)
    {
        $panier = $this->session->get('panier', []);

        if ($panier[$id] == 1):
            $this->session->remove($panier[$id]);
        else:
            $panier[$id]--;
        endif;

        $this->session->set('panier', $panier);

    }

    public function supprimer(int $id)
    {
        $panier = $this->session->get('panier', []);
        unset($panier[$id]);
        $this->session->set('panier', $panier);
    }

    public function panierDetail()
    {
        $panier = $this->session->get('panier', []);
        $panierDetail = [];

        foreach ($panier as $id => $quantite):

            $panierDetail[] = [
                'produit' => $this->produitRepository->find($id),
                'quantite' => $quantite
            ];

        endforeach;

        return $panierDetail;

    }

    public function total()
    {

        $panier = $this->panierDetail();
        $total = 0;
        foreach ($panier as $item):
            $total+=$item['produit']->getPrix()*$item['quantite'];

        endforeach;

        return $total;


    }


}