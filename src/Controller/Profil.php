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
use Dompdf\Dompdf;
use Dompdf\Options;

class Profil extends AbstractController
{
    /**
     * @Route("/Profil/Commande",name="commande-liste")
     * @return Response
     */
    public function commandeController(SessionInterface $session, EntityManagerInterface $em)
    {
        $commande = $em->getRepository(Commande::class)->findBy(array('idClient' => $this->getUser()->getUserName()));
        return $this->render('profil/commande.html.twig', array('commandes' => $commande));
    }

    /**
     * @Route("/Profil/Commande/{id}",name="commande-liste-{id}")
     * @return Response
     */
    public function detailCommandeController(SessionInterface $session, EntityManagerInterface $em, $id)
    {
        $commande = $em->getRepository(Commande::class)->findOneBy(array("idCommande" => $id));
        $lignes = $em->getRepository(LignesCommande::class)->findBy(array("idCommande" => $id));
        $user = $this->getUser()->getUserName();
        $nb = 0;
        foreach ($lignes as $article) {
            $nb += $article->getQuantiteCommande();
        }
        return $this->render('profil/detail-commande.html.twig', array('commande' => $lignes, "prix" => $commande->getPrixCommande(), 'numero' => $commande->getIdCommande(), 'nb' => $nb, 'etat' => $commande->getIdStatut()));
    }

    /**
     * @Route("/Profil/Commande/annuler/{id}",name="commande-liste-annuler-{id}")
     */
    public function annulerCommandeController(SessionInterface $session, EntityManagerInterface $em, $id)
    {
        $commande = $em->getRepository(LignesCommande::class)->findBy(array("idCommande" => $id));
        foreach ($commande as $ligne){
            $stock = $em->getRepository(Stock::class)->findOneBy(array("refProduit" => $ligne->getRefProduit()));
            $stockactuel = $stock->getQuantiteStock();
            $stock->setQuantiteStock($stockactuel + $ligne->getQuantiteCommande());
            $em->flush();
        }

        $conn = $this->getDoctrine()->getConnection();
        $sql = '
        DELETE FROM commande
        WHERE id_commande =' . $id ;
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        return $this->render('message/annulation_reussi.html.twig', array('id' => $id));
    }

    /**
     * @Route("/Profil/Commande/valider/{id}",name="commande-liste-valider-{id}")
     */
    public function validerCommandeController(SessionInterface $session, EntityManagerInterface $em, $id)
    {
        $commande = $em->getRepository(Commande::class)->findOneBy(array("idCommande" => $id));
        $statut = $em->getRepository(StatusCommande::class)->findOneBy(array("idStatut" => 2));
        $commande->setIdStatut($statut);
        $em->flush();


        return $this->render('message/paiement_reussi.html.twig', array('id' => $id));
    }

    /**
     * @Route("/Profil/Commande/exporter/{id}",name="commande-liste-exporter/{id}")
     * @return Response
     */
    public function exporterCommandeController(SessionInterface $session, EntityManagerInterface $em, $id)
    {
        $commande = $em->getRepository(Commande::class)->findOneBy(array("idCommande" => $id));
        $lignes = $em->getRepository(LignesCommande::class)->findBy(array("idCommande" => $id));
        $user = $this->getUser()->getUserName();
        $nb = 0;
        foreach ($lignes as $article) {
            $nb += $article->getQuantiteCommande();
        }

        // On crée une  instance pour définir les options de notre fichier pdf
        $options = new Options();

        // Pour simplifier l'affichage des images, on autorise dompdf à utiliser
        // des  url pour les nom de  fichier
        $options->set('isRemoteEnabled', TRUE);

        // On crée une instance de dompdf avec  les options définies
        $dompdf = new Dompdf($options);

        // On demande à Symfony de générer  le code html  correspondant à
        // notre template, et on stocke ce code dans une variable
        // Le fichier twig sera présent dans templates/pdf/

        $etat['idStatut'] = 3;
        $html = $this->renderView('profil/export.html.twig', array('commande' => $lignes, "prix" => $commande->getPrixCommande(), 'numero' => $commande->getIdCommande(), 'nb' => $nb, 'etat' => $etat)
        );
        // On envoie le code html  à notre instance de dompdf
        $dompdf->loadHtml($html);
        $dompdf->set_base_path(realpath('/public/build/css/app.css'));
        // On demande à dompdf de générer le  pdf
        $dompdf->render();

        // On renvoie le flux (stream) du fichier pdf dans une  Response pour l'utilisateur
        return new Response ($dompdf->stream());
        //return $this->render('profil/export.html.twig', array('commande' => $lignes, "prix" => $commande->getPrixCommande(), 'numero' => $commande->getIdCommande(), 'nb' => $nb, 'etat' => $etat));

        //return $this->render('profil/detail-commande.html.twig', array('commande' => $lignes, "prix" => $commande->getPrixCommande(), 'numero' => $commande->getIdCommande(), 'nb' => $nb, 'etat' => $commande->getIdStatut()));
    }
}