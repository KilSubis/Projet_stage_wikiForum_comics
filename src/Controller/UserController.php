<?php

namespace App\Controller;



use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    #[Route('/utilisateur/edition/{id}', name: 'user.edit')]
    public function edit(User $user, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {

       if (!$this->getUser()) {
          return $this->redirectToRoute('security.login');
       }

       if($this->getUser() !== $user) {
        return $this->redirectToRoute('series.index');
       }

       $form = $this->createForm(UserType::class, $user);

       $form->handleRequest($request);
       if ($form->isSubmitted() && $form->isValid()) {
        if($hasher->isPasswordValid($user, $form->getData()->getPlainPassworD())) {
          $user = $form->getData();
          $manager->persist($user);
          $manager->flush();


       

          $this->addFlash(
        'success',
        'Les informations de votre compte on bien été changées'
        );
         
        return $this->redirectToRoute('series.index');

       
        }else{
            $this->addFlash(
                'warning',
                'le mot de passe est incorrecte'
               );
            }
       }
          
       

       


        return $this->render('pages/user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
