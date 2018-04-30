<?php
/**
 * Created by PhpStorm.
 * User: Kuroi-Tenshi
 * Date: 26/03/2018
 * Time: 15:00
 */

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Entity\Produits;
use App\Entity\Commande;
use App\Entity\Panier;
use App\Entity\Stock;

class Fixture extends AbstractController
{
    /**
     * @Route("/Fixture", name="fixture")
     */
    public function fixtureController(EntityManagerInterface $em){
        //création d'un utilisateur
        $user = new User();
        $user->setRole("ROLE_USER");
        $user->setPassword($user->generate());
        $user->setAdresse("666 rue des enfer");
        $user->setPseudo("Kuroi");
        $user->setEmail("Kuroi@satan.ep");
        $user->setPostal(66666);
        $user->setVille("Enfer");

        $em->persist($user);
        $em->flush();
    }
}