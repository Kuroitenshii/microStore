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
use App\Entity\Panier;
use App\Entity\Stock;
use App\Entity\StatusCommande;
use App\Entity\Commande;
use App\Entity\LignesCommande;
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
     * @Route("/Mobile/Ajout/User", name="ajout_user")
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

    /**
     * @Route("/Mobile/Connexion", name="mobile_connexion")
     */
    public function apiConnexion(Request $request, EntityManagerInterface $em){
        //1 on désérialize les données et on obtient un objet de type User
        $serializer = $this->get('serializer');
        $username = $request->get('username');
        $password = $request->get('password');

        //on cherche l'utilisateur dans la base de données
        if($em->getRepository(User::class)->findBy(array('userName' => $username, 'password' => $password))){
            $user = $em->getRepository(User::class)->findOneBy(array('userName' => $username, 'password' => $password));
            $data = json_encode(array("pseudo" => $user->getPseudo(), "id" => $user->getUserName()));
            $response = new Response($data);
            $response->headers->set('Content-Type', 'application/text; charset=utf-8');
            $response->headers->set('Ok', 'oui');
            return $response;
        } else {
            $response = new JsonResponse(json_encode(array("erreur" => "identifiant ou mot de passe invalide !")));
            $response->headers->set('Content-Type', 'application/json');
            $response->headers->set('Non', 'non');
            return $response;
        }
    }

    /**
     * @Route("/Mobile/PanierAdd", name="mobile-panier-add")
     */
    public function apiPanierAdd(Request $request, EntityManagerInterface $em){
        $id = $request->get("id");
        $qte = $request->get("qte");
        $ref = $request->get("ref");
        $ref = $em->getRepository(Produits::class)->findOneBy(array('ref' => $ref));
        $user = $em->getRepository(User::class)->findOneBy(array('userName' => $id));
        $panier = $em->getRepository(Panier::class)->findOneBy(array('idClient' => $id, 'refProduit' => $ref));
        $stock = $em->getRepository(Stock::class)->findOneBy(array('refProduit' => $ref));

        $new = new Panier();
        $new->setIdClient($user);
        $new->setRefProduit($ref);
        //si le panier actuel n'existe pas
        if ($panier == NULL) {
            //definition de la quantité et enregistrement dans la base de données
            if($qte > $stock->getQuantiteStock()){
                return new Response("Stock insuffisant, disponible : ".$stock->getQuantiteStock());
            } else {
                $new->setQuantiteProduit($qte);
                $em->persist($new);
                $em->flush();
                return new Response("Ajout effectué");
            }
        } else {
            //sinon si la quantité du stock est égale a la quantité du panier
            if (($panier->getQuantiteProduit()+$qte) > $stock->getQuantiteStock()) {
                    //sinon renvoie d'une reponse en json pour prévenir d'un stock insuffisant
                    return new Response("Stock insuffisant, disponible : ".($stock->getQuantiteStock()-$panier->getQuantiteProduit()));
            } else {
                //sinon on rajoute 1 a la quantité et on enregistre dans la base
                $qte = $panier->getQuantiteProduit() + $qte;
                $panier->setQuantiteProduit($qte);
                $em->flush();
                return new Response("Ajout effectué");
            }
        }

    }

    /**
     * @Route("/Mobile/Panier/{id}", name="mobile_produit_{id}")
     */
    public function apiAffichePanier(EntityManagerInterface $em, Request $request, $id){
        //vérification du stock avec le nombre d'article dans le panier et suppression des article en trop
        $user = $id;
        $change = 0;
        $articles = $em->getRepository(Panier::class)->findBy(array('idClient' => $user));
        foreach ($articles as $article) {
            $stock = $em->getRepository(Stock::class)->findOneBy(array("refProduit" => $article->getRefProduit()));
            if ($stock->getQuantiteStock() < $article->getQuantiteProduit()) {
                $change += $article->getQuantiteProduit() - $stock->getQuantiteStock();
                $article->setQuantiteProduit($stock->getQuantiteStock());
                $em->flush();
            }
        }

        $panier = $em->getRepository(Panier::class)->findBy(array('idClient' => $id));
        //appel au service de sérialisation
        //l'objet $unProduit sera trasnformé en Json
        $data = "[";
        foreach ($panier as $item){
            if($data == "["){
                $data .= $item->serialize();
            } else {
                $data .= ",".$item->serialize();
            }
        }
        $data .= "]";
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Ok', 'oui');
        return $response;
    }

    /**
     * @Route("/Mobile/Panier/{id}/Change", name="mobile-panier-{id}-change")
     */
    public function apiChangePanier(EntityManagerInterface $em, Request $request, $id){
        $ref = $request->get("ref");
        $qte = $request->get("qte");
        $produit = $em->getRepository(Produits::class)->findOneBy(array("nom" => $ref));
        $panier = $em->getRepository(Panier::class)->findOneBy(array("idClient" => $id, "refProduit" => $produit->getRef()));
        $stock = $em->getRepository(Stock::class)->findOneBy(array('refProduit' => $produit->getRef()));
        if ($qte == 0) {
            // récupération de la connexion et suppresion de l'article du panier
            $conn = $this->getDoctrine()->getConnection();

            $sql = '
        DELETE FROM panier
        WHERE id_client =' . $id . '
        AND ref_produit = ' . $produit->getRef() . '
        ';
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            return new Response("Article retiré du panier");
        } else if ($qte > $stock->getQuantiteStock()) {
            return new Response("Stock insuffisant, disponible : ".$stock->getQuantiteStock());
        } else {
            $panier->setQuantiteProduit($qte);
            $em->flush();
            return new Response("Ajout/Retrait effectué");
        }
    }

    /**
     * @Route("/Mobile/Panier/{id}/Vider", name="mobile-panier-{id}-vider")
     */
    public function apiViderPanier($id){
        $conn = $this->getDoctrine()->getConnection();

        //supréssion du panier de cet utilisateur
        $sql = '
        DELETE FROM panier
        WHERE id_client =' . $id;
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        return new Response("Panier Vider");
    }

    /**
     * @Route("/Mobile/Panier/{id}/Valider", name="mobile-panier-{id}-valider")
     */
    public function apiValiderPanier(EntityManagerInterface $em, $id){
        $articles = $em->getRepository(\App\Entity\Panier::class)->findBy(array('idClient' => $id));

        $i = 0;
        $tot = 0;
        foreach ($articles as $element) {
            $ref = $element->getRefProduit();
            $detail = $em->getRepository(Produits::class)->findOneBy(array('ref' => $ref));
            $prix = $detail->getPrix();
            $total = $prix * $element->getQuantiteProduit();
            $tot += $total; $i++;
        }

        //récupération du prix, de l'utilisateur et de la connection
        $user = $id;
        $conn = $this->getDoctrine()->getConnection();
        $prix = $tot;

        //récupération du panier et du statut "validé"
        $panier = $em->getRepository(\App\Entity\Panier::class)->findBy(array('idClient' => $user));
        $statut = $em->getRepository(StatusCommande::class)->findOneBy(array('idStatut' => 1));

        //création du nouvelle commande avec les information
        $use = $em->getRepository(User::class)->find($user);
        $commande = new Commande();
        $commande->setIdClient($use);
        $commande->setIdStatut($statut);
        $commande->setPrixCommande($prix * 1.2);

        //enregistrement de la commande dans la base
        $em->persist($commande);
        $em->flush();

        //pour chaque article du panier, on créer une ligne dans la table LigneCommande
        foreach ($panier as $article){
            $ligne = new LignesCommande();
            $produit = $em->getRepository(Produits::class)->findOneBy(array('ref' => $article->getRefProduit()));
            $ligne->setIdCommande($commande);
            $ligne->setRefProduit($produit);
            $ligne->setQuantiteCommande($article->getQuantiteProduit());
            $stock = $em->getRepository(Stock::class)->findOneBy(array("refProduit" => $article->getRefProduit()));
            $quantiteActuel = $stock->getQuantiteStock();
            $restant = $quantiteActuel - $article->getQuantiteProduit();
            $stock->setQuantiteStock($restant);
            $em->persist($ligne);
            $em->flush();
        }


        // on supprime ensuite le panier
        $sql = '
        DELETE FROM panier
        WHERE id_client =' . $user;
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        return new Response("Commande Valider");
    }

    /**
     * @Route("/Mobile/Panier/{id}/Total", name="mobile-panier-{id}-total")
     */
    public function apiTotalPanier(EntityManagerInterface $em, $id){
        $articles = $em->getRepository(\App\Entity\Panier::class)->findBy(array('idClient' => $id));

        $i = 0;
        $tot = 0;
        foreach ($articles as $element) {
            $ref = $element->getRefProduit();
            $detail = $em->getRepository(Produits::class)->findOneBy(array('ref' => $ref));
            $prix = $detail->getPrix();
            $total = $prix * $element->getQuantiteProduit();
            $tot += $total; $i++;
        }
        return new Response($tot);
    }

    /**
     * @Route("/Mobile/Commande/{id}", name="mobile_commande-{id}")
     */
    public function apiAfficheCommande(EntityManagerInterface $em, $id){
        $commande = $em->getRepository(Commande::class)->findBy(array("idClient" => $id));
        $data = "[";
        foreach ($commande as $ligne){
            if($data == "["){
                $data .= $ligne->serialize();
            } else {
                $data .= ",".$ligne->serialize();
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
}