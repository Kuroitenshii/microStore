<?php
/**
 * Created by PhpStorm.
 * User: Kuroi-Tenshi
 * Date: 22/02/2018
 * Time: 16:35
 */

namespace App\Controller;

use App\Entity\Produits;
use App\Entity\Stock;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class Boutique extends AbstractController
{
    /**
     * @Route("/Boutique",name="Boutique-Choix")
     * @return Response
     */
    public function boutiqueChoixController()
    {
        return $this->render('boutique/choix.html.twig');
    }

    /**
     * @Route("/Boutique/console-de-salon", name="Boutique-salon")
     * @return Response
     */
    public function boutiqueSalonController(\Doctrine\ORM\EntityManagerInterface $em, SessionInterface $session)
    {
        $console = $em->getRepository(Produits::class)->findBy(array("idCategorie" => 1));
        foreach ($console as $key => $salon){
            $imageF[$key] = base64_encode(stream_get_contents($salon->getImageFabricant()));
            $imageC[$key] = base64_encode(stream_get_contents($salon->getImage()));
            $stock = $em->getRepository(Stock::class)->findBy(array('refProduit' => $salon->getRef()));
            if ($stock[0]->getQuantiteStock() == 0){
                $etat[$key] = "Rupture de stock";
            } else {
                $etat[$key] = $salon->getPrix();
            }
        }

        return $this->render('boutique/salon.html.twig',array("console" => $console, "imageF" => $imageF, "imageC" => $imageC, "etat" => $etat));
    }

    /**
     * @Route("/Boutique/console-portable", name="Boutique-portable")
     * @return Response
     */
    public function boutiquePortableController(\Doctrine\ORM\EntityManagerInterface $em, SessionInterface $session)
    {
        $console = $em->getRepository(Produits::class)->findBy(array("idCategorie" => 2));
        foreach ($console as $key => $portable){
            $imageF[$key] = base64_encode(stream_get_contents($portable->getImageFabricant()));
            $imageC[$key] = base64_encode(stream_get_contents($portable->getImage()));
            $stock = $em->getRepository(Stock::class)->findBy(array('refProduit' => $portable->getRef()));
            if ($stock[0]->getQuantiteStock() == 0){
                $etat[$key] = "Rupture de stock";
            } else {
                $etat[$key] = $portable->getPrix();
            }
        }

        return $this->render('boutique/portable.html.twig',array("console" => $console, "imageF" => $imageF, "imageC" => $imageC,  "etat" => $etat));
    }

    /**
     * @Route("/Boutique/Ajout", name="Boutique-ajout")
     */
    public function boutiqueAjoutController(SessionInterface $session, Request $req)
    {
        //$session->clear();
        $ref = $req->get("ref");
        if($session->has("panier_".$ref)){
            $qte = $session->get("panier_".$ref);
            $qte ++;
            $session->remove("panier_".$ref);
            $session->set("panier_".$ref, $qte);
        } else {
            $qte = 1;
            $session->set("panier_".$ref, $qte);
        }
    }
}