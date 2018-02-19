<?php
/**
 * Created by PhpStorm.
 * User: Kuroi-Tenshi
 * Date: 08/01/2018
 * Time: 16:35
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class Accueil extends AbstractController
{
    /**
     * @Route("/Accueil",name="Accueil")
     * @return Response
     */
    public function accueilController(){
        return $this->render('accueil/accueil.html.twig');
    }
}