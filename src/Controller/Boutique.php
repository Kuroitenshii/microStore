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
     */
    public function boutiqueChoixController(\Doctrine\ORM\EntityManagerInterface $em, SessionInterface $session)
    {
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
        return $this->render('boutique/choix.html.twig');
    }

    /**
     * @Route("/Boutique/console-de-salon", name="Boutique-salon")
     * @return Response
     */
    public function boutiqueSalonController(\Doctrine\ORM\EntityManagerInterface $em, SessionInterface $session)
    {
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

        return $this->render('boutique/salon.html.twig', array("console" => $console, "imageF" => $imageF, "imageC" => $imageC, "etat" => $etat));
    }

    /**
     * @Route("/Boutique/console-portable", name="Boutique-portable")
     * @return Response
     */
    public function boutiquePortableController(\Doctrine\ORM\EntityManagerInterface $em, SessionInterface $session)
    {
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

        return $this->render('boutique/portable.html.twig', array("console" => $console, "imageF" => $imageF, "imageC" => $imageC, "etat" => $etat));
    }

    /**
     * @Route("/Boutique/Ajout", name="Boutique-ajout")
     */
    public function boutiqueAjoutController(SessionInterface $session, Request $req, \Doctrine\ORM\EntityManagerInterface $em)
    {
        $ref = $em->getRepository(Produits::class)->findOneBy(array('ref' => $req->get("ref")));
        $user = $em->getRepository(User::class)->findOneBy(array('userName' => $this->getUser()->getUserName()));
        $panier = $em->getRepository(Panier::class)->findOneBy(array('idClient' => $user, 'refProduit' => $ref));
        $stock = $em->getRepository(Stock::class)->findOneBy(array('refProduit' => $ref));
        $new = new Panier();
        $new->setIdClient($user);
        $new->setRefProduit($ref);
        if ($panier == NULL) {
            $new->setQuantiteProduit(1);
            $em->persist($new);
            $em->flush();
        } else {
            if ($panier->getQuantiteProduit() == $stock->getQuantiteStock()) {
                if ($req->get('lieu') == "panier") {
                    $session->set('info', 'Stock insuffisant');
                    return new JsonResponse("Stock insuffisant");
                } else {
                    return new JsonResponse("Stock insuffisant");
                }
            } else {

                $qte = $panier->getQuantiteProduit() + 1;
                $panier->setQuantiteProduit($qte);
                $em->flush();

            }
        }
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

        return new JsonResponse("Article ajouté");
    }

    /**
     * @Route("/Boutique/Retrait", name="Boutique-retrait")
     */
    public
    function boutiqueRetraitController(SessionInterface $session, Request $req, \Doctrine\ORM\EntityManagerInterface $em)
    {
        $ref = $req->get("ref");
        $user = $this->getUser()->getUserName();
        $panier = $em->getRepository(Panier::class)->findOneBy(array('idClient' => $user, 'refProduit' => $ref));
        if ($panier->getQuantiteProduit() == 1) {
            $conn = $this->getDoctrine()->getConnection();

            $sql = '
        DELETE FROM panier
        WHERE id_client =' . $user . '
        AND ref_produit = ' . $ref . '
        ';
            $stmt = $conn->prepare($sql);
            $stmt->execute();
        } else {
            $qte = $panier->getQuantiteProduit() - 1;
            $panier->setQuantiteProduit($qte);
            $em->flush();
        }
        $user = $this->getUser()->getUserName();
        $articles = $em->getRepository(\App\Entity\Panier::class)->findBy(array('idClient' => $user));
        $nb = 0;
        foreach ($articles as $article) {
            $nb += $article->getQuantiteProduit();
        }
        $session->remove('article');
        $session->set('article', $nb);

        return new JsonResponse("Article ajouté");
    }

    /**
     * @Route("/Boutique/Detail/{article}", name="Boutique-detail-{article}")
     * @return Response
     */
    public function boutiqueDetailController(\Doctrine\ORM\EntityManagerInterface $em, SessionInterface $session, $article)
    {
        $detail = $em->getRepository(Produits::class)->findOneBy(array("ref" => $article));
        $imageF = base64_encode(stream_get_contents($detail->getImageFabricant()));
        $imageC = base64_encode(stream_get_contents($detail->getImage()));
        $stock = $em->getRepository(Stock::class)->findOneBy(array('refProduit' => $article));
        if ($stock->getQuantiteStock() == 0) {
            $etat = "Rupture de stock";
        } else {
            $etat = $detail->getPrix();
        }
        $stock = $em->getRepository(Stock::class)->findOneBy(array('refProduit' => $article));
        return $this->render('boutique/detail.html.twig', array('detail' => $detail, 'stock' => $stock, "imageF" => $imageF, "imageC" => $imageC, "etat" => $etat));
    }

}