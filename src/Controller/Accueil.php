<?php
/**
 * Created by PhpStorm.
 * User: Kuroi-Tenshi
 * Date: 08/01/2018
 * Time: 16:35
 */

namespace App\Controller;

use App\Entity\Panier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Accueil extends AbstractController
{
    /**
     * @Route("/Accueil",name="Accueil")
     * @return Response
     * controller de la page d'accueil du site
     */
    public function accueilController(\Doctrine\ORM\EntityManagerInterface $em, SessionInterface $session){
        if($this->getUser()){
            // recupération et changement du nombre de produit dans le stock
            $user = $this->getUser()->getUserName();
            $articles = $em->getRepository(\App\Entity\Panier::class)->findBy(array('idClient' => $user));
            $nb = 0;
            foreach($articles as $article){
                $nb += $article->getQuantiteProduit();
            }
            $session->remove('article');
            $session->set('article', $nb);
        }
        return $this->render('accueil/accueil.html.twig');
    }


    /**
     * @Route("/")
     */
    public function goAccueilController(){
        return $this->redirectToRoute("Accueil");
    }
}