<?php
/**
 * Created by PhpStorm.
 * User: Kuroi-Tenshi
 * Date: 08/01/2018
 * Time: 16:35
 */

namespace App\Controller;

use App\Entity\Produits;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Panier extends AbstractController
{
    /**
     * @Route("/Panier",name="Panier")
     * @return Response
     */
    public function accueilController(SessionInterface $session, EntityManagerInterface $em){
        if($this->getUser() == NULL){
            $panier = array();
            $panierUser = $session->all();
            $i = 0;
            foreach ($panierUser as $element){
                $ref = array_keys($panierUser)[$i];
                $ref= preg_replace('/\D/', '', $ref);
                $detail = $em->getRepository(Produits::class)->findBy(array('ref' => $ref))[0];
                $prix = $detail->getPrix();
                $total = $prix * $element;
                $panier[$ref] = array("ref" => $ref, "nom" => $detail->getNom(), "quantite" => $element, "prix" => $prix, "total" => $total);
                $i ++;
            }
        } else {
            $panier = ""
;        }
        return $this->render('panier/panier.html.twig', array('panier' => $panier));
    }
}