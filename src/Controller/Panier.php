<?php
/**
 * Created by PhpStorm.
 * User: Kuroi-Tenshi
 * Date: 08/01/2018
 * Time: 16:35
 */

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\LignesCommande;
use App\Entity\Produits;
use App\Entity\StatusCommande;
use App\Entity\Stock;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class Panier extends AbstractController
{
    /**
     * @Route("/Panier",name="Panier")
     * @return Response
     */
    public function panierController(SessionInterface $session, EntityManagerInterface $em)
    {
            if($session->has("info")){
                $info2 = $session->get("info");
                $session->remove("info");
            } else {
                $info2 = "";
            }
            $user = $this->getUser()->getUserName();
            $info = "";
            $change = 0;
            $articles = $em->getRepository(\App\Entity\Panier::class)->findBy(array('idClient' => $user));
            foreach ($articles as $article) {
                $stock = $em->getRepository(Stock::class)->findOneBy(array("refProduit" => $article->getRefProduit()));
                if ($stock->getQuantiteStock() < $article->getQuantiteProduit()) {
                    $change += $article->getQuantiteProduit() - $stock->getQuantiteStock();
                    $article->setQuantiteProduit($stock->getQuantiteStock());
                    $em->flush();
                }
            }
            if ($change != 0) {
                $info = $change . "élément(s) retiré (stock insuffisant)";
            }


            $articles = $em->getRepository(\App\Entity\Panier::class)->findBy(array('idClient' => $user));
            $nb = 0;
            foreach ($articles as $article) {
                $nb += $article->getQuantiteProduit();
            }
            $session->remove('article');
            $session->set('article', $nb);

            $panier = array();
            $i = 0;
            $tot = 0;
            foreach ($articles as $element) {
                $ref = $element->getRefProduit();
                $detail = $em->getRepository(Produits::class)->findBy(array('ref' => $ref))[0];
                $prix = $detail->getPrix();
                $total = $prix * $element->getQuantiteProduit();
                $tot += $total;
                $panier[$i] = array("ref" => $detail->getRef(), "nom" => $detail->getNom(), "quantite" => $element->getQuantiteProduit(), "prix" => $prix, "total" => $total);
                $i++;
            }
        return $this->render('panier/panier.html.twig', array('panier' => $panier, 'tot' => $tot, 'info' => $info, 'info2' => $info2));
    }

    /**
     * @Route("/Panier/Vider", name="Panier-vider")
     */
    public
    function panierViderController(SessionInterface $session, Request $req, \Doctrine\ORM\EntityManagerInterface $em)
    {
        $user = $this->getUser()->getUserName();
        $conn = $this->getDoctrine()->getConnection();

        $sql = '
        DELETE FROM panier
        WHERE id_client =' . $user;
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $articles = $em->getRepository(\App\Entity\Panier::class)->findBy(array('idClient' => $user));
        $nb = 0;
        foreach ($articles as $article) {
            $nb += $article->getQuantiteProduit();
        }
        $session->remove('article');
        $session->set('article', $nb);

        return new JsonResponse("");
    }

    /**
     * @Route("/Panier/Valider", name="Panier-valider")
     */
    public
    function panierValiderController(SessionInterface $session, Request $req, \Doctrine\ORM\EntityManagerInterface $em)
    {
        $user = $this->getUser()->getUserName();
        $conn = $this->getDoctrine()->getConnection();
        $prix = $req->get('prix');

        $panier = $em->getRepository(\App\Entity\Panier::class)->findBy(array('idClient' => $user));
        $statut = $em->getRepository(StatusCommande::class)->findOneBy(array('idStatut' => 1));
        $commande = new Commande();
        $commande->setIdClient($user);
        $commande->setIdStatut($statut);
        $commande->setPrixCommande($prix * 1.2);
        $em->persist($commande);
        $em->flush();
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



        $sql = '
        DELETE FROM panier
        WHERE id_client =' . $user;
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $articles = $em->getRepository(\App\Entity\Panier::class)->findBy(array('idClient' => $user));
        $nb = 0;
        foreach ($articles as $article) {
            $nb += $article->getQuantiteProduit();
        }
        $session->remove('article');
        $session->set('article', $nb);

        return new JsonResponse("");
    }
}