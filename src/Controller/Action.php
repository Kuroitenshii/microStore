<?php
/**
 * Created by PhpStorm.
 * User: Kuroi-Tenshi
 * Date: 14/03/2018
 * Time: 21:16
 */

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Commande;
use App\Entity\Produits;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use App\Entity\Stock;

class Action extends AbstractController
{
    /**
     * @Route("/Ajout/Produit", name="ajout-produit")
     */
    public function ajoutProduitController(EntityManagerInterface $em, Request $request)
    {
        $produit = new Produits();
        $cat1 = $em->getRepository(Categorie::class)->find(1);
        $cat2 = $em->getRepository(Categorie::class)->find(2);


        $form = $this->createFormBuilder($produit)
            ->add('nom', TextType::class, ['label' => 'Nom :', 'label_attr' => ['class' => 'normal-cursor'], 'attr' => ['class' => 'text-cursor', 'style' => "margin-bottom: 10%"],])
            ->add('description', TextareaType::class, ['label' => 'Description :', 'label_attr' => ['class' => 'normal-cursor'], 'attr' => ['class' => 'text-cursor', 'style' => "margin-bottom: 10%"],])
            ->add('prix', MoneyType::class, ['label' => 'Prix :', 'label_attr' => ['class' => 'normal-cursor'], 'attr' => ['class' => 'text-cursor', 'style' => "margin-bottom: 10%"],])
            ->add('image', FileType::class, ['label' => 'Image :', 'label_attr' => ['class' => 'normal-cursor'], 'attr' => ['class' => 'text-cursor', 'style' => "margin-bottom: 10%"],])
            ->add('annee', IntegerType::class, ['label' => 'Année de sortie :', 'label_attr' => ['class' => 'normal-cursor'], 'attr' => ['class' => 'text-cursor', 'style' => "margin-bottom: 10%"],])
            ->add('fabricant', TextType::class, ['label' => 'fabricant :', 'label_attr' => ['class' => 'normal-cursor'], 'attr' => ['class' => 'text-cursor', 'style' => "margin-bottom: 10%"],])
            ->add('imageFabricant', FileType::class, ['label' => 'logo fabriquant :', 'label_attr' => ['class' => 'normal-cursor'], 'attr' => ['class' => 'text-cursor', 'style' => "margin-bottom: 10%"],])
            ->add('idCategorie', ChoiceType::class, array("choices" => ["salon" => $cat1, "portable" => $cat2]), ['label' => 'logo fabriquant :', 'label_attr' => ['class' => 'normal-cursor'], 'attr' => ['class' => 'text-cursor', 'style' => "margin-bottom: 10%"],])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //transformation des image en blob
            /** @var UploadedFile $image */
            $image = $produit->getImage();
            /** @var UploadedFile $imageF */
            $imageF = $produit->getImageFabricant();

            $strm = fopen($image, 'rb');
            $produit->setImage(stream_get_contents($strm));
            $strm2 = fopen($imageF, 'rb');
            $produit->setImageFabricant(stream_get_contents($strm2));

            $id = $produit->getIdCategorie();
            $id = $em->getRepository(Categorie::class)->find($id);
            $produit->setIdCategorie($id);

            $em->persist($produit);
            $em->flush();
            return $this->redirectToRoute('ajout-produit-succes');
        }

        return $this->render('form/formulaire_ajout_produit.html.twig', array('form' => $form->createView(),));

    }

    /**
     * @Route("/Ajout/Produit/Succes",name="ajout-produit-succes")
     */
    public function inscriptionSucces()
    {
        return $this->render('message/ajout-produit-succes.html.twig');

    }

    /**
     * @Route("/Modif/Produit", name="modif-produit")
     */
    public function listeModifProduitController(EntityManagerInterface $em)
    {
        $console = $em->getRepository(Produits::class)->findBy(array("idCategorie" => 1));
        $portable = $em->getRepository(Produits::class)->findBy(array("idCategorie" => 2));

        return $this->render('actions/modif.html.twig', array("console" => $console, "portable" => $portable));
    }

    /**
     * @Route("/Modif/Produit/{id}", name="modif-produit-{id}")
     */
    public function ModifProduitController(EntityManagerInterface $em, Request $request, $id)
    {
        dump ($id);
        $produit = new Produits();
        $cat1 = $em->getRepository(Categorie::class)->find(1);
        $cat2 = $em->getRepository(Categorie::class)->find(2);
        $ancien = $em->getRepository(Produits::class)->find($id);
        $produit->setNom($ancien->getNom());
        $produit->setDescription($ancien->getDescription());
        $produit->setPrix($ancien->getPrix());
        $produit->setAnnee($ancien->getAnnee());
        $produit->setFabricant($ancien->getFabricant());
        $produit->setIdCategorie($ancien->getIdCategorie());


        $form = $this->createFormBuilder($produit)
            ->add('nom', TextType::class, ['label' => 'Nom :', 'label_attr' => ['class' => 'normal-cursor'], 'attr' => ['class' => 'text-cursor', 'style' => "margin-bottom: 10%"],])
            ->add('description', TextareaType::class, ['label' => 'Description :', 'label_attr' => ['class' => 'normal-cursor'], 'attr' => ['class' => 'text-cursor', 'style' => "margin-bottom: 10%"],])
            ->add('prix', MoneyType::class, ['label' => 'Prix :', 'label_attr' => ['class' => 'normal-cursor'], 'attr' => ['class' => 'text-cursor', 'style' => "margin-bottom: 10%"],])
            ->add('image', FileType::class, array('required' => false), ['label' => 'Image :', 'label_attr' => ['class' => 'normal-cursor'], 'attr' => ['class' => 'text-cursor', 'style' => "margin-bottom: 10%"]])
            ->add('annee', IntegerType::class, ['label' => 'Année de sortie :', 'label_attr' => ['class' => 'normal-cursor'], 'attr' => ['class' => 'text-cursor', 'style' => "margin-bottom: 10%"],])
            ->add('fabricant', TextType::class, ['label' => 'fabricant :', 'label_attr' => ['class' => 'normal-cursor'], 'attr' => ['class' => 'text-cursor', 'style' => "margin-bottom: 10%"],])
            ->add('imageFabricant', FileType::class, array('required' => false), ['label' => 'logo fabriquant :', 'label_attr' => ['class' => 'normal-cursor'], 'attr' => ['class' => 'text-cursor', 'style' => "margin-bottom: 10%"],])
            ->add('idCategorie', ChoiceType::class, array("choices" => ["salon" => $cat1, "portable" => $cat2]), ['label' => 'logo fabriquant :', 'label_attr' => ['class' => 'normal-cursor'], 'attr' => ['class' => 'text-cursor', 'style' => "margin-bottom: 10%"],])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($produit->getImage() == "") {
                $imageAncien = stream_get_contents($ancien->getImage());
                $produit->setImage($imageAncien);
            } else {
                //transformation des image en blob
                /** @var UploadedFile $image */
                $image = $produit->getImage();
                $strm = fopen($image, 'rb');
                $produit->setImage(stream_get_contents($strm));
            }
            if ($produit->getImageFabricant() == "") {
                $imageAncien = stream_get_contents($ancien->getImageFabricant());
                $produit->setImageFabricant($imageAncien);
            } else {
                //transformation des image en blob
                /** @var UploadedFile $imageF */
                $imageF = $produit->getImageFabricant();

                $strm2 = fopen($imageF, 'rb');
                $produit->setImageFabricant(stream_get_contents($strm2));
            }
            $idCat = $produit->getIdCategorie();
            $idCat = $em->getRepository(Categorie::class)->find($idCat);
            $produit->setIdCategorie($idCat);

            $maj = $em->getRepository(Produits::class)->find($id);
            $maj->setNom($produit->getNom());
            $maj->setDescription($produit->getDescription());
            $maj->setPrix($produit->getPrix());
            $maj->setImage($produit->getImage());
            $maj->setAnnee($produit->getAnnee());
            $maj->setFabricant($produit->getFabricant());
            $maj->setImageFabricant($produit->getImageFabricant());
            $maj->setIdCategorie($produit->getIdCategorie());
            $em->flush();
            return $this->redirect('/Modif/Produit/'.$id.'/Succes');
        }

        return $this->render('form/formulaire_modif_produit.html.twig', array('form' => $form->createView(),));

    }

    /**
     * @Route("/Modif/Produit/{id}/Succes",name="modif-produit-{id}-succes")
     */
    public function modifProduitSuccesController()
    {
        return $this->render('message/modif-produit-succes.html.twig');

    }

    /**
     * @Route("/Modif/Stock/Produit", name="modif-stock-produit")
     */
    public function listeModifStockProduitController(EntityManagerInterface $em)
    {
        $console = $em->getRepository(Produits::class)->findBy(array("idCategorie" => 1));
        $portable = $em->getRepository(Produits::class)->findBy(array("idCategorie" => 2));
        foreach ($console as $key => $cons){
            $stockcons[$key] = $em->getRepository(Stock::class)->findOneBy(array('refProduit' => $cons->getRef()))->getQuantiteStock();
        }
        foreach ($portable as $key => $port){
            $stockport[$key] = $em->getRepository(Stock::class)->findOneBy(array('refProduit' => $port->getRef()))->getQuantiteStock();
        }

        return $this->render('actions/stock.html.twig', array("console" => $console, "portable" => $portable, "stockC" => $stockcons, "stockP" => $stockport));
    }

    /**
     * @Route("/Modif/Stock/Produit/{id}", name="modif-stock-produit-{id}")
     */
    public function ModifStockProduitController(EntityManagerInterface $em, Request $request, $id)
    {
        $stock = new Stock();
        $ancien = $em->getRepository(Stock::class)->findOneBy(array("refProduit" => $id));
        $stock->setQuantiteStock($ancien->getQuantiteStock());


        $form = $this->createFormBuilder($stock)
            ->add('QuantiteStock', IntegerType::class, ['label' => 'Stock:', 'label_attr' => ['class' => 'normal-cursor'], 'attr' => ['class' => 'text-cursor', 'style' => "margin-bottom: 10%"],])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $stockfinal = $em->getRepository(Stock::class)->findOneBy(array("refProduit" => $id));
            $stockfinal->setQuantiteStock($stock->getQuantiteStock());
            $em->flush();
            return $this->redirect('/Modif/Stock/Produit/'.$id.'/Succes');
        }

        return $this->render('form/formulaire_modif_stock_produit.html.twig', array('form' => $form->createView(),));

    }

    /**
     * @Route("/Modif/Stock/Produit/{id}/Succes",name="modif-stock-produit-{id}-succes")
     */
    public function modifStockProduitSuccesController()
    {
        return $this->render('message/modif-stock-produit-succes.html.twig');

    }

    /**
     * @Route("/Reset/Password", name="reset-password")
     */
    public function listeClientController(EntityManagerInterface $em)
    {
        $client = $em->getRepository(User::class)->findAll();

        return $this->render('actions/client.html.twig', array("client" => $client));
    }

    /**
     * @Route("/Reset/Password/{id}",name="reset-password-{id}")
     */
    public function resetMdpClientController(EntityManagerInterface $em, $id)
    {
        $client = $em->getRepository(User::class)->findOneBy(array("userName" => $id));
        $client->setPassword($client->generate());
        $em->flush();
        return $this->render('message/reset_password_succes.html.twig', array("id" => $id, "mdp" => $client->getPassword()));
    }

    /**
     * @Route("/Info/Societe", name="info-societe")
     */
    public function infoSocieteController(EntityManagerInterface $em){
        $nbClient = count($em->getRepository(User::class)->findBy(array("role" => "ROLE_USER")));
        $nbArticle = count($em->getRepository(Produits::class)->findAll());
        $commandes = $em->getRepository(Commande::class)->findAll();
        $nbValide = count($commandes);
        $ca = 0;
        foreach ($commandes as $commande){
            $ca += $commande->getPrixCommande();
        }

        return $this->render('info/info_societe.html.twig', array("nbClient" => $nbClient, "nbArticle" => $nbArticle, "nbCommande" => $nbValide, "chiffreAffaire" => $ca));
    }
}