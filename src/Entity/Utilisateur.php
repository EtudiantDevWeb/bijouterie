<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;


// Une classe Utilisateur ou User doit obligatoirement implémenter la PasswordAuthenticatedUserInterface afin que l'encodage des mots de passe puissent prendre le relais
//Ainsi que la UserInterface. Cette dertnière cnécessite d'implémenter certaines methodes:
//getRoles(), getUserName(), getUserIdentifier(), eraseCredential(), getSalt(), getPassword()
//On ajoute UniqueEntity pour rendre unique notre utilisateur sur son email
//getUserIdentifier() est implementé avec pour retour la valeur de la propriété email car l'utilisateur est identifié grace à son email
#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[UniqueEntity(fields:['email'], message:'Un compte existe déjà à cette adresse email')]
class Utilisateur implements PasswordAuthenticatedUserInterface, UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'Ce champ est obligatoire')]
    private $username;

    //On definit par defaut le reol de nos utilisateurs à ROLE_USER
    #[ORM\Column(type: 'json')]
    private $roles = ['ROLE_USER'];

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'Ce champ est obligatoire')]
    #[Assert\Email(message: 'Email Invalide')]
    private $email;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'Ce champ est obligatoire')]
    private $password;

    #[Assert\EqualTo(propertyPath: 'password', message: 'Les mots de passe ne correspondant pas')]
    public $confirmPassword;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getUserIdentifier()
    {
        return $this->email;
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function __call($name, $arguments)
    {
        // TODO: Implement @method string getUserIdentifier()
    }
}
