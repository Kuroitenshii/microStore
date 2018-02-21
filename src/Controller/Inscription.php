<?php
/**
 * Created by PhpStorm.
 * User: Kuroi-Tenshi
 * Date: 21/02/2018
 * Time: 11:34
 */

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;


class Inscription extends AbstractController{
    /**
     * @Route("/Inscription",name="inscription")
     */
    public function inscription(Request $request){
        $user = new User();
        $user->setRole("ROLE_USER");
        $user->setPassword($user->generate());

        $form = $this->createFormBuilder($user)
            ->add('pseudo', TextType::class, ['label' => 'Pseudo :', 'label_attr' => ['class' => 'normal-cursor'], 'attr' => ['class' => 'text-cursor', 'style' => "margin-bottom: 10%"],])
            ->add('email', EmailType::class, ['label' => 'E-mail :', 'label_attr' => ['class' => 'normal-cursor'], 'attr' => ['class' => 'text-cursor', 'style' => "margin-bottom: 10%"],])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $user = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('inscription-succes', array('id' => $user->getUserName(), 'mdp' => $user->getPassword()));
        }

        return $this->render('inscription/inscription.html.twig', array('form' => $form->createView(),));

    }

    /**
     * @Route("/Inscription-Succes",name="inscription-succes")
     */
    public function inscriptionSucces(Request $request){
        $id = $request->get('id');
        $mdp = $request->get('mdp');
        return $this->render('message/inscription_reussi.html.twig', array('id' => $id, 'password' => $mdp));

    }
}