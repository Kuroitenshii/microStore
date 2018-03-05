<?php
/**
 * Created by PhpStorm.
 * User: Kuroi-Tenshi
 * Date: 08/01/2018
 * Time: 16:35
 */

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Produits;
use App\Entity\Stock;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Profil extends AbstractController
{
    /**
     * @Route("/Profil/Commande",name="commande-liste")
     * @return Response
     */
    public function commandeController(SessionInterface $session, EntityManagerInterface $em)
    {
        $commande = $em->getRepository(Commande::class)->findBy(array('idClient' => $this->getUser()->getUserName()));
        return $this->render('profil/commande.html.twig', array('commande' => $commande));
    }
}