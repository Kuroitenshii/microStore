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
use App\Entity\Panier;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;
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
     * affichage de la vue pour choisir la categorie
     */
    public function boutiqueChoixController(\Doctrine\ORM\EntityManagerInterface $em, SessionInterface $session)
    {
        // recupération et changement du nombre de produit dans le stock
        if ($this->getUser()) {
            $user = $this->getUser()->getUserName();
            $articles = $em->getRepository(\App\Entity\Panier::class)->findBy(array('idClient' => $user));
            $nb = 0;
            foreach ($articles as $article) {
                $nb += $article->getQuantiteProduit();
            }
            $session->remove('article');
            $session->set('article', $nb);
        }
        //affichage de la vue
        return $this->render('boutique/choix.html.twig');
    }

    /**
     * @Route("/Boutique/console-de-salon", name="Boutique-salon")
     * @return Response
     * affichage des article de la categorie "console de salon"
     */
    public function boutiqueSalonController(\Doctrine\ORM\EntityManagerInterface $em, SessionInterface $session)
    {
        // recupération et changement du nombre de produit dans le stock
        if ($this->getUser()) {
            $user = $this->getUser()->getUserName();
            $articles = $em->getRepository(\App\Entity\Panier::class)->findBy(array('idClient' => $user));
            $nb = 0;
            foreach ($articles as $article) {
                $nb += $article->getQuantiteProduit();
            }
            $session->remove('article');
            $session->set('article', $nb);
        }

        //récupération des article de la categorie ainsi que de leur image et leur stock
        $console = $em->getRepository(Produits::class)->findBy(array("idCategorie" => 1));
        foreach ($console as $key => $salon) {
            $imageF[$key] = base64_encode(stream_get_contents($salon->getImageFabricant()));
            $imageC[$key] = base64_encode(stream_get_contents($salon->getImage()));
            $stock = $em->getRepository(Stock::class)->findBy(array('refProduit' => $salon->getRef()));
            if ($stock[0]->getQuantiteStock() == 0) {
                $etat[$key] = "Rupture de stock";
            } else {
                $etat[$key] = $salon->getPrix();
            }
        }

        //affichage de la vue contenant les article
        return $this->render('boutique/salon.html.twig', array("console" => $console, "imageF" => $imageF, "imageC" => $imageC, "etat" => $etat));
    }

    /**
     * @Route("/Boutique/console-portable", name="Boutique-portable")
     * @return Response
     * affichage des articles de la categorie "console portable"
     */
    public function boutiquePortableController(\Doctrine\ORM\EntityManagerInterface $em, SessionInterface $session)
    {
        // recupération et changement du nombre de produit dans le stock
        if ($this->getUser()) {
            $user = $this->getUser()->getUserName();
            $articles = $em->getRepository(\App\Entity\Panier::class)->findBy(array('idClient' => $user));
            $nb = 0;
            foreach ($articles as $article) {
                $nb += $article->getQuantiteProduit();
            }
            $session->remove('article');
            $session->set('article', $nb);
        }

        //récupération des article de la categorie et de leur images et stock respectif
        $console = $em->getRepository(Produits::class)->findBy(array("idCategorie" => 2));
        foreach ($console as $key => $portable) {
            $imageF[$key] = base64_encode(stream_get_contents($portable->getImageFabricant()));
            $imageC[$key] = base64_encode(stream_get_contents($portable->getImage()));
            $stock = $em->getRepository(Stock::class)->findBy(array('refProduit' => $portable->getRef()));
            if ($stock[0]->getQuantiteStock() == 0) {
                $etat[$key] = "Rupture de stock";
            } else {
                $etat[$key] = $portable->getPrix();
            }
        }

        //affichage de la vue contenant les données
        return $this->render('boutique/portable.html.twig', array("console" => $console, "imageF" => $imageF, "imageC" => $imageC, "etat" => $etat));
    }

    /**
     * @Route("/Boutique/Ajout", name="Boutique-ajout")
     * ajout d'un article au panier
     */
    public function boutiqueAjoutController(SessionInterface $session, Request $req, \Doctrine\ORM\EntityManagerInterface $em)
    {
        //récupération du produit, de l'utilisateur, du stock, et du panier
        $ref = $em->getRepository(Produits::class)->findOneBy(array('ref' => $req->get("ref")));
        $user = $em->getRepository(User::class)->findOneBy(array('userName' => $this->getUser()->getUserName()));
        $panier = $em->getRepository(Panier::class)->findOneBy(array('idClient' => $user, 'refProduit' => $ref));
        $stock = $em->getRepository(Stock::class)->findOneBy(array('refProduit' => $ref));

        //création d'un nouveau panier avec l'id client et la ref produit
        $new = new Panier();
        $new->setIdClient($user);
        $new->setRefProduit($ref);

        //si le panier actuel n'existe pas
        if ($panier == NULL) {
            //definition de la quantité sur 1 et enregistrement dans la base de données
            $new->setQuantiteProduit(1);
            $em->persist($new);
            $em->flush();
        } else {
            //sinon si la quantité du stock est égale a la quantité du panier
            if ($panier->getQuantiteProduit() == $stock->getQuantiteStock()) {
                //si le demande vien de la page panier
                if ($req->get('lieu') == "panier") {
                    //paramettrage du message d'erreur
                    $session->set('info', 'Stock insuffisant');
                    return new JsonResponse(array("ref" => $req->get("ref"), "res" => "Stock insuffisant"));
                } else {
                    //sinon renvoie d'une reponse en json pour prévenir d'un stock insuffisant
                    return new JsonResponse(array("ref" => $req->get("ref"), "res" => "Stock insuffisant"));
                }
            } else {
                //sinon on rajoute 1 a la quantité et on enregistre dans la base
                $qte = $panier->getQuantiteProduit() + 1;
                $panier->setQuantiteProduit($qte);
                $em->flush();

            }
        }

        // recupération et changement du nombre de produit dans le stock
        $user = $this->getUser()->getUserName();
        $articles = $em->getRepository(\App\Entity\Panier::class)->findBy(array('idClient' => $user));
        $nb = 0;
        foreach ($articles as $article) {
            $nb += $article->getQuantiteProduit();
        }

        if ($req->get('lieu') == "panier") {
            $session->set('info', 'Article ajouté');
        } else {
            $session->set('info', "");
        }

        $session->remove('article');
        $session->set('article', $nb);

        //retour de la reponse avec la référence et la reponse positive
        return new JsonResponse(array("ref" => $req->get("ref"), "res" => "Article ajouté"));
    }

    /**
     * @Route("/Boutique/Retrait", name="Boutique-retrait")
     * retrait d'article du stock
     */
    public
    function boutiqueRetraitController(SessionInterface $session, Request $req, \Doctrine\ORM\EntityManagerInterface $em)
    {
        //récupération de la réference, de l'utilisateur et du panier
        $ref = $req->get("ref");
        $user = $this->getUser()->getUserName();
        $panier = $em->getRepository(Panier::class)->findOneBy(array('idClient' => $user, 'refProduit' => $ref));

        //si la quantité actuelle est egale a 1
        if ($panier->getQuantiteProduit() == 1) {
            // récupération de la connexion et suppresion de l'article du panier
            $conn = $this->getDoctrine()->getConnection();

            $sql = '
        DELETE FROM panier
        WHERE id_client =' . $user . '
        AND ref_produit = ' . $ref . '
        ';
            $stmt = $conn->prepare($sql);
            $stmt->execute();
        } else {
            //sinon retirer 1 a la quantité et enregistrer dans la base
            $qte = $panier->getQuantiteProduit() - 1;
            $panier->setQuantiteProduit($qte);
            $em->flush();
        }

        // recupération et changement du nombre de produit dans le stock
        $user = $this->getUser()->getUserName();
        $articles = $em->getRepository(\App\Entity\Panier::class)->findBy(array('idClient' => $user));
        $nb = 0;
        foreach ($articles as $article) {
            $nb += $article->getQuantiteProduit();
        }
        $session->remove('article');
        $session->set('article', $nb);

        //retour de la reponse positive en json
        return new JsonResponse("Article ajouté");
    }

    /**
     * @Route("/Boutique/Detail/{article}", name="Boutique-detail-{article}")
     * @return Response
     * afficher le detail d'un article
     */
    public function boutiqueDetailController(\Doctrine\ORM\EntityManagerInterface $em, SessionInterface $session, $article)
    {
        //récupération des detail de l'article
        $detail = $em->getRepository(Produits::class)->findOneBy(array("ref" => $article));

        //récupération des images et du stock
        $imageF = base64_encode(stream_get_contents($detail->getImageFabricant()));
        $imageC = base64_encode(stream_get_contents($detail->getImage()));
        $stock = $em->getRepository(Stock::class)->findOneBy(array('refProduit' => $article));

        //si le stock est egale a 0, afficher une rupture de stock, sinon afficher le prix
        if ($stock->getQuantiteStock() == 0) {
            $etat = "Rupture de stock";
        } else {
            $etat = $detail->getPrix();
        }

        //afficher les detial du produit sur la vue twig
        return $this->render('boutique/detail.html.twig', array('detail' => $detail, 'stock' => $stock, "imageF" => $imageF, "imageC" => $imageC, "etat" => $etat));
    }

}