<?php

namespace App\Controller;

use App\Entity\Comics;
use App\Form\ComicsType;
use Doctrine\ORM\EntityManager;
use App\Repository\ComicsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ComicsController extends AbstractController
{
    /**
     * Ce controlleur montre tous les comics 
     * 
     * @param ComicsRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/comics', name: 'app_comics', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(ComicsRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {

        $comics = $paginator->paginate(
            $repository->findBy(['user' => $this->getUser()]),
            $request->query->getInt('page', 1), 
            10 
        );

        return $this->render('pages/comics/index.html.twig', [
            'comics' => $comics
        ]);
    }

    /**
     * Ce controlleur montre un formulaire qui créer un comics
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('comics/nouveau', 'comics.new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(
        Request $request,
        EntityManagerInterface $manager
    ): Response {

        $comics = new Comics();
        $form = $this->createForm(ComicsType::class, $comics);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
             $comics = $form->getData();
             $comics->setUser($this->getUser());
             
             $manager->persist($comics);
             $manager->flush();

             $this->addFlash(
                'success',
                'Votre comics a bien été ajouté !'
             );

            return $this->redirectToRoute('app_comics');
        }

        return $this->render('pages/comics/new.html.twig',[
            'form' => $form->createview()
        ]);
    }

    
    /**
     * Ce controlleur premet de modifier un comics 
     *
     * @param Comics $comics
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */ 
    #[Security("is_granted('ROLE_USER') and user === comics.getUser()")]
    #[Route('/comics/edition/{id}', 'comics.edit', methods: ['GET', 'POST'])]
    public function edit( 
        Comics $comics, 
        Request $request, 
        EntityManagerInterface $manager 
        ) : Response
    {
        $form = $this->createForm(ComicsType::class, $comics);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
             $comics = $form->getData();
             
             $manager->persist($comics);
             $manager->flush();

             $this->addFlash(
                'success',
                'Votre comics a bien été modifié !'
             );

            return $this->redirectToRoute('app_comics');
        }

        return $this->render('pages/comics/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    
    #[Route('/comics/suppression/{id}', 'comics.delete', methods: ['GET'])]
    #[Security("is_granted('ROLE_USER') and user === comics.getUser()")]
    /**
     * Ce controlleur permet de supprimer un comics 
     *
     * @param EntityManagerInterface $manager
     * @param Comics $comics
     * @return Response
     */
    public function delete(EntityManagerInterface $manager, Comics $comics) : Response 
    {

        if(!$comics) {

            $this->addFlash(
                'success',
                'Le comics n\'a pas été trouvé!'
             );

            return $this->redirectToRoute('app_comics');
        }

        $manager->remove($comics);
        $manager->flush();

        $this->addFlash(
            'success',
            'Votre comics a bien été supprimé !'
         );

        return $this->redirectToRoute('app_comics');
    }

}
