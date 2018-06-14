<?php
/**
 * Created by PhpStorm.
 * User: Kuroi
 * Date: 04/06/2018
 * Time: 13:31
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
use Symfony\Component\Form\Extension\Core\Type\DateType;

class MeilleurClient extends AbstractController
{
    /**
     * @Route("/Meilleur",name="meilleur")
     * @return Response
     * affichage de la vue pour choisir la categorie
     */
    public function meilleurClientController(EntityManagerInterface $em, Request $request){
        //création du formulaire
        $form = $this->createFormBuilder()
            ->add('date_debut', DateType::class, ['label' => 'date de debut :', 'label_attr' => ['class' => 'normal-cursor'], 'attr' => ['class' => 'text-cursor', 'style' => "margin-bottom: 10%"],])
            ->add('date_fin', DateType::class, ['label' => 'date de fin :', 'label_attr' => ['class' => 'normal-cursor'], 'attr' => ['class' => 'text-cursor', 'style' => "margin-bottom: 10%"],])
            ->getForm();

        $form->handleRequest($request);

        //uen fois le formulaire soumis et valide
        if ($form->isSubmitted() && $form->isValid()){
            //récupération des info du formulaire, création et enregistrement de l'utilisateur dans la base
            dump($request->get("form")["date_debut"]);
            $debut = $request->get("form")["date_debut"]["year"]."-".$request->get("form")["date_debut"]["month"]."-".$request->get("form")["date_debut"]["day"];
            $fin = $request->get("form")["date_fin"]["year"]."-".$request->get("form")["date_fin"]["month"]."-".$request->get("form")["date_fin"]["day"];

            // on supprime ensuite le panier
            $conn = $this->getDoctrine()->getConnection();
            $sql = 'SELECT id_client, user.pseudo, sum(prix_commande) AS achat FROM commande,user WHERE user.id = commande.id_client AND date_commande BETWEEN '.$debut.' AND '.$debut.' GROUP BY `id_client`, user.pseudo ORDER BY id_client DESC';
            $res = $conn->prepare($sql);
            $res->execute([]);
            $resultat = $res->fetchAll();

            dump($resultat);
            //return $this->redirectToRoute('meilleur-succes', array('user' => $resultat));
        }

        //affichage du formulaire
        return $this->render('info/meilleur_client.html.twig', array('form' => $form->createView(),));
    }

    /**
     * @Route("/Meilleur-succes",name="meilleur-succes")
     * @return Response
     * affichage de la vue pour choisir la categorie
     */
    public function meilleurSuccesController(EntityManagerInterface $em, Request $request){
        return $this->render('info/meilleur_succes.html.twig', array('user' => $form->createView(),));
    }
}