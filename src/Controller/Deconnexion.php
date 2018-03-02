<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Deconnexion extends AbstractController {
    /**
     *@Route("/deconnexion",name="deconnexion")
     */
    public function deconnexion(){
        return $this->redirectToRoute("Accueil");
    }
}