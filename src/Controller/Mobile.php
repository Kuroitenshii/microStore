<?php
/**
 * Created by PhpStorm.
 * User: Kuroi-Tenshi
 * Date: 13/03/2018
 * Time: 16:25
 */

namespace App\Controller;

use App\Entity\Produits;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Mobile extends AbstractController
{
    /**
     * @Route("/Mobile/Produit/{id}", name="mobile_produit_{id}")
     */
    public function apiAfficheUnProduit(Produits $unproduit = null){
        if($unproduit === null){
            $response = new Response($unproduit);
            $response->headers->set('Ok', 'non');
            $response->setStatutsCode(404);
            return $response;
        }
        //appel au service de sérialisation
        //l'objet $unProduit sera trasnformé en Json
        $data = $unproduit->serialize();
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Ok', 'oui');
        return $response;
    }

    /**
     * @Route("/Mobile/Produit", name="mobile_produit")
     */
    public function apiAfficheProduit(EntityManagerInterface $em){
        $produits = $em->getRepository(Produits::class)->findAll();
        $data = "[";
        foreach ($produits as $produit){
            if($data == "["){
                $data .= $produit->serialize();
            } else {
                $data .= ",".$produit->serialize();
            }
        }
        $data .= "]";
        //appel au service de sérialisation
        //l'objet $unProduit sera trasnformé en Json

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Ok', 'oui');
        return $response;
    }

    /**
     * @Route("/Mobile/Categorie/{id}", name="mobile_produit-cat-{id}")
     */
    public function apiAfficheCatProduit(EntityManagerInterface $em, $id){
        $produits = $em->getRepository(Produits::class)->findBy(array("idCategorie" => $id));
        $data = "[";
        foreach ($produits as $produit){
            if($data == "["){
                $data .= $produit->serialize();
            } else {
                $data .= ",".$produit->serialize();
            }
        }
        $data .= "]";
        //appel au service de sérialisation
        //l'objet $unProduit sera trasnformé en Json

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Ok', 'oui');
        return $response;
    }

    /**
     * @Route("/Mobile/Ajout/User", name="ajour_user")
     */
    public function apiAjoutUser(Request $request, EntityManagerInterface $em){
        //1 on désérialize les données et on obtient un objet de type User
        $serializer = $this->get('serializer');
        $unUser = $serializer->deserialize($request->getContent(), User::class, 'json');

        //2 - on ajoute le User a la BDD
        $em->persist($unUser);
        $em->flush();

        //3 on construit une reponse retournée
        $response = new Response("L'ajout est réalisé !");
        $response->headers->set('Content-Type', 'application/text');
        $response->headers->set('Ok', 'oui');
        return $response;
    }

}