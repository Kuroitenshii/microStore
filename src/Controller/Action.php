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
     * formulaire de création d'article
     */
    public function ajoutProduitController(EntityManagerInterface $em, Request $request)
    {
        //récupération du produit et des categorie d'article
        $produit = new Produits();
        $cat1 = $em->getRepository(Categorie::class)->find(1);
        $cat2 = $em->getRepository(Categorie::class)->find(2);

        // création du formulaire
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

        //quand le formulaire est confirmé et valide
        if ($form->isSubmitted() && $form->isValid()) {
            //transformation des image en blob
            /** @var UploadedFile $image */
            $image = $produit->getImage();
            /** @var UploadedFile $imageF */
            $imageF = $produit->getImageFabricant();

            //enregistrement des images
            $strm = fopen($image, 'rb');
            $produit->setImage(stream_get_contents($strm));
            $strm2 = fopen($imageF, 'rb');
            $produit->setImageFabricant(stream_get_contents($strm2));

            //enregistrement de la categorie
            $id = $produit->getIdCategorie();
            $id = $em->getRepository(Categorie::class)->find($id);
            $produit->setIdCategorie($id);

            // création et enrefgistrement du produit dans la bdd
            $em->persist($produit);
            $em->flush();
            //redirige vers la page de succées
            $new = $em->getRepository(Produits::class)->findOneBy(array("nom" => $produit->getNom()));
            $ref = $new->getRef();
            return $this->redirect("/Modif/Stock/Produit/".$ref);
        }

        //affiche le formulaire
        return $this->render('form/formulaire_ajout_produit.html.twig', array('form' => $form->createView(),));

    }

    /**
     * @Route("/Ajout/Produit/Succes",name="ajout-produit-succes")
     * succés de l'ajout d'un produit
     */
    public function inscriptionSucces()
    {
        //affiche du message de succées
        return $this->render('message/ajout-produit-succes.html.twig');

    }

    /**
     * @Route("/Modif/Produit", name="modif-produit")
     * affichage de la liste de produit (par categorie)
     */
    public function listeModifProduitController(EntityManagerInterface $em)
    {
        //recupération des produit de chaque catégorie
        $console = $em->getRepository(Produits::class)->findBy(array("idCategorie" => 1));
        $portable = $em->getRepository(Produits::class)->findBy(array("idCategorie" => 2));

        //affichage du render avec les produits
        return $this->render('actions/modif.html.twig', array("console" => $console, "portable" => $portable));
    }

    /**
     * @Route("/Modif/Produit/{id}", name="modif-produit-{id}")
     * formulaire de modification d'un produit
     */
    public function ModifProduitController(EntityManagerInterface $em, Request $request, $id)
    {
        //paramettrage de l'ancienproduit pour remplir les champs
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


        //création du formulaire
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

        //si le formulaire est envoyé et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            //si il n'y a pas d'image
            if ($produit->getImage() == "") {
                //recupération de l'ancienne image
                $imageAncien = stream_get_contents($ancien->getImage());
                $produit->setImage($imageAncien);
            } else {
                //sinon transformation de l'image en blob
                /** @var UploadedFile $image */
                $image = $produit->getImage();
                $strm = fopen($image, 'rb');
                $produit->setImage(stream_get_contents($strm));
            }
            //si il n'y a pas d'image pour le fabricant
            if ($produit->getImageFabricant() == "") {
                //récupération de l'ancienne image de fabricant
                $imageAncien = stream_get_contents($ancien->getImageFabricant());
                $produit->setImageFabricant($imageAncien);
            } else {
                // sinon transformation de l'image en blob
                /** @var UploadedFile $imageF */
                $imageF = $produit->getImageFabricant();

                $strm2 = fopen($imageF, 'rb');
                $produit->setImageFabricant(stream_get_contents($strm2));
            }
            //récupération de la categorie
            $idCat = $produit->getIdCategorie();
            $idCat = $em->getRepository(Categorie::class)->find($idCat);
            $produit->setIdCategorie($idCat);

            //récupération du produit actuellement dans la base de données et modification des information
            $maj = $em->getRepository(Produits::class)->find($id);
            $maj->setNom($produit->getNom());
            $maj->setDescription($produit->getDescription());
            $maj->setPrix($produit->getPrix());
            $maj->setImage($produit->getImage());
            $maj->setAnnee($produit->getAnnee());
            $maj->setFabricant($produit->getFabricant());
            $maj->setImageFabricant($produit->getImageFabricant());
            $maj->setIdCategorie($produit->getIdCategorie());

            //enregistrement du produit
            $em->flush();

            //redirection vers la route de succés
            return $this->redirect('/Modif/Produit/'.$id.'/Succes');
        }

        //affichage du formulaire
        return $this->render('form/formulaire_modif_produit.html.twig', array('form' => $form->createView(),));

    }

    /**
     * @Route("/Modif/Produit/{id}/Succes",name="modif-produit-{id}-succes")
     * affiche du message de succées pour la modification de produit
     */
    public function modifProduitSuccesController()
    {
        return $this->render('message/modif-produit-succes.html.twig');

    }

    /**
     * @Route("/Modif/Stock/Produit", name="modif-stock-produit")
     * affichage des produit et de leur stock respectif
     */
    public function listeModifStockProduitController(EntityManagerInterface $em)
    {
        //récupération des produit par categorie
        $console = $em->getRepository(Produits::class)->findBy(array("idCategorie" => 1));
        $portable = $em->getRepository(Produits::class)->findBy(array("idCategorie" => 2));
        //pour chaque produit de chaque categorie, récupération du stock correspondant
        foreach ($console as $key => $cons){
            $stockcons[$key] = $em->getRepository(Stock::class)->findOneBy(array('refProduit' => $cons->getRef()))->getQuantiteStock();
        }
        foreach ($portable as $key => $port){
            $stockport[$key] = $em->getRepository(Stock::class)->findOneBy(array('refProduit' => $port->getRef()))->getQuantiteStock();
        }

        //affichage du render
        return $this->render('actions/stock.html.twig', array("console" => $console, "portable" => $portable, "stockC" => $stockcons, "stockP" => $stockport));
    }

    /**
     * @Route("/Modif/Stock/Produit/{id}", name="modif-stock-produit-{id}")
     * affichage du formulaire pour modifier le stock
     */
    public function ModifStockProduitController(EntityManagerInterface $em, Request $request, $id)
    {
        //création d'un variable Stock et récupération des données actuelle du stock
        $stock = new Stock();
        $ancien = $em->getRepository(Stock::class)->findOneBy(array("refProduit" => $id));
        $stock->setQuantiteStock($ancien->getQuantiteStock());

        //création du formulaire
        $form = $this->createFormBuilder($stock)
            ->add('QuantiteStock', IntegerType::class, ['label' => 'Stock:', 'label_attr' => ['class' => 'normal-cursor'], 'attr' => ['class' => 'text-cursor', 'style' => "margin-bottom: 10%"],])
            ->getForm();

        $form->handleRequest($request);

        //si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            //récupératiion du stock actuel, modification et enregistrement dans la base de données
            $stockfinal = $em->getRepository(Stock::class)->findOneBy(array("refProduit" => $id));
            $stockfinal->setQuantiteStock($stock->getQuantiteStock());
            $em->flush();
            //redirection vers la page de succés
            return $this->redirect('/Modif/Stock/Produit/'.$id.'/Succes');
        }

        //affichage du render du formulaire
        return $this->render('form/formulaire_modif_stock_produit.html.twig', array('form' => $form->createView(),));

    }

    /**
     * @Route("/Modif/Stock/Produit/{id}/Succes",name="modif-stock-produit-{id}-succes")
     * affichage du succées de la modification du stock
     */
    public function modifStockProduitSuccesController()
    {
        return $this->render('message/modif-stock-produit-succes.html.twig');

    }

    /**
     * @Route("/Reset/Password", name="reset-password")
     * affichage des clients de la base de données
     */
    public function listeClientController(EntityManagerInterface $em)
    {
        //récupération de la liste des clients
        $client = $em->getRepository(User::class)->findAll();

        //affichage de la vue
        return $this->render('actions/client.html.twig', array("client" => $client));
    }

    /**
     * @Route("/Reset/Password/{id}",name="reset-password-{id}")
     * reinitialisation du mot de passe du client
     */
    public function resetMdpClientController(EntityManagerInterface $em, $id)
    {
        //récupération du client par l'id
        $client = $em->getRepository(User::class)->findOneBy(array("userName" => $id));

        //génération d'un nouveau mot de passe et enregistrement dans la bdd
        $client->setPassword($client->generate());
        $em->flush();

        //affichage du message de succès
        return $this->render('message/reset_password_succes.html.twig', array("id" => $id, "mdp" => $client->getPassword()));
    }

    /**
     * @Route("/Info/Societe", name="info-societe")
     * affichage des info de la société
     */
    public function infoSocieteController(EntityManagerInterface $em){
        //récupération du nombre de client, d'article et de commande
        $nbClient = count($em->getRepository(User::class)->findBy(array("role" => "ROLE_USER")));
        $nbArticle = count($em->getRepository(Produits::class)->findAll());
        $commandes = $em->getRepository(Commande::class)->findAll();
        $nbValide = count($commandes);

        //calcul du chiffre d'affaire de la société
        $ca = 0;
        foreach ($commandes as $commande){
            $ca += $commande->getPrixCommande();
        }

        //affichage de la vue contenant les infos
        return $this->render('info/info_societe.html.twig', array("nbClient" => $nbClient, "nbArticle" => $nbArticle, "nbCommande" => $nbValide, "chiffreAffaire" => $ca));
    }
}