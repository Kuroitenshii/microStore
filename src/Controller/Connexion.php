<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Connexion extends AbstractController {
    /**
     * @Route("/Connexion",name="Connexion")
     * affichage de l'ecran de conenxion
     */
    public function connexionController( AuthenticationUtils $auth){
        //récupération de l'erreur eventuelle et du dernier username utilisé
        $erreur = $auth->getLastAuthenticationError();
        $lastUserName = $auth->getLastUsername();


        //affichage du render
        return $this->render('connexion/connexion.html.twig', array('last_username' => $lastUserName, 'error'=>$erreur));
    }
}