<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Connexion extends AbstractController {
    /**
     * @Route("/Connexion",name="Connexion")
     */
    public function connexionController( AuthenticationUtils $auth){
        $erreur = $auth->getLastAuthenticationError();
        $lastUserName = $auth->getLastUsername();

        return $this->render('connexion/connexion.html.twig', array('last_username' => $lastUserName, 'error'=>$erreur));
    }
}