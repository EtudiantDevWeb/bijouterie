<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\InscriptionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{

    #[Route('inscription', name: 'inscription')]
    public function inscription(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {
        $utilisateur= new Utilisateur();

        $form=$this->createForm(InscriptionType::class, $utilisateur);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            // on hache le mot de passe récupéré en brut du formulaire et étant déjà chargé dans notre objet Utilisateur
            // on utilise la méthode hasPassword qui nécessite de faire implémenter notre classe Utilisateur de la PasswordAuthenticatedUserInterface afin que le controle de l'algorithm à utiliser puisse s'effectuer
            $mdp=$hasher->hashPassword($utilisateur, $utilisateur->getPassword());
            // on réaffecte le mot de passe haché
            $utilisateur->setPassword($mdp);
            $manager->persist($utilisateur);
            $manager->flush();

            $this->addFlash('success', 'Féliciation, vous êtes bien inscrit. Connectez-vous à présent');

            return $this->redirectToRoute('login');

        }


        return $this->render('security/inscription.html.twig', [
            'form'=>$form->createView()
        ]);
    }

    #[Route('login', name: 'login')]
    public function login(): Response
    {
        return $this->render('security/login.html.twig', [

        ]);
    }

    #[Route('logout', name: 'logout')]
    public function logout()
    {

    }


}
