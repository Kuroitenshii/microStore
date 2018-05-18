<?php
/**
 * Created by PhpStorm.
 * User: Kuroi-Tenshi
 * Date: 21/02/2018
 * Time: 11:34
 */

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Form\Tests\Extension\Core\Type\IntegerTypeTest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class Inscription extends AbstractController{
    /**
     * @Route("/Inscription",name="inscription")
     * affichage du formualire d'inscription
     */
    public function inscription(Request $request){
        //creation d'un nouvel utilisateurs
        $user = new User();

        //paramettrage du role et du mot de passe aléatoire
        $user->setRole("ROLE_USER");

        //création du formulaire
        $form = $this->createFormBuilder($user)
            ->add('pseudo', TextType::class, ['label' => 'Pseudo :', 'label_attr' => ['class' => 'normal-cursor'], 'attr' => ['class' => 'text-cursor', 'style' => "margin-bottom: 10%"],])
            ->add('email', EmailType::class, ['label' => 'E-mail :', 'label_attr' => ['class' => 'normal-cursor'], 'attr' => ['class' => 'text-cursor', 'style' => "margin-bottom: 10%"],])
            ->add('adresse', TextType::class, ['label' => 'Adresse :', 'label_attr' => ['class' => 'normal-cursor'], 'attr' => ['class' => 'text-cursor', 'style' => "margin-bottom: 10%"],])
            ->add('postal', IntegerType::class, ['label' => 'Code postal :', 'label_attr' => ['class' => 'normal-cursor'], 'attr' => ['class' => 'text-cursor', 'style' => "margin-bottom: 10%"],])
            ->add('ville', TextType::class, ['label' => 'Ville :', 'label_attr' => ['class' => 'normal-cursor'], 'attr' => ['class' => 'text-cursor', 'style' => "margin-bottom: 10%"],])
            ->getForm();

        $form->handleRequest($request);

        //uen fois le formulaire soumis et valide
        if ($form->isSubmitted() && $form->isValid()){
            //récupération des info du formulaire, création et enregistrement de l'utilisateur dans la base
            $password = $user->generate();
            $user = $form->getData();
            $password = $user->getPassword();
            $user->setPassword($password);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            //redirection vers la page de succès
            return $this->redirectToRoute('inscription-succes', array('id' => $user->getUserName(), 'mdp' => $password));
        }

        //affichage du formulaire
        return $this->render('form/formulaire_inscription.html.twig', array('form' => $form->createView(),));

    }

    /**
     * @Route("/Inscription-Succes",name="inscription-succes")
     * affichage de la page indiquant l'identifiant et le mot de passe du nouvel utilisateur
     */
    public function inscriptionSucces(Request $request){
        $id = $request->get('id');
        $mdp = $request->get('mdp');
        return $this->render('message/inscription_reussi.html.twig', array('id' => $id, 'password' => $mdp));

    }
}