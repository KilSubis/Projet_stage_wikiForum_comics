<?php

namespace App\Controller;

use App\Entity\Comics;
use App\Form\ComicsType;
use App\Repository\ComicsRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

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
    public function index(ComicsRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {

        $comics = $paginator->paginate(
            $repository->findAll(),
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
    public function new(
        Request $request,
        EntityManagerInterface $manager
    ): Response {

        $comics = new Comics();
        $form = $this->createForm(ComicsType::class, $comics);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
             $comics = $form->getData();
             
             $manager->persist($comics);
             $manager->flush();

             $this->addFlash(
                'succes',
                'Votre comics a bien été ajouté !'
             );

            return $this->redirectToRoute('app_comics');
        }

        return $this->render('pages/comics/new.html.twig',[
            'form' => $form->createview()
        ]);
    }

    
    
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
                'succes',
                'Votre comics a bien été modifié !'
             );

            return $this->redirectToRoute('app_comics');
        }

        return $this->render('pages/comics/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/comics/suppression/{id}', 'comics.delete', methods: ['GET'])]
    public function delete(EntityManagerInterface $manager, Comics $comics) : Response 
    {

        if(!$comics) {

            $this->addFlash(
                'succes',
                'Le comics n\'a pas été trouvé!'
             );

            return $this->redirectToRoute('app_comics');
        }

        $manager->remove($comics);
        $manager->flush();

        $this->addFlash(
            'succes',
            'Votre comics a bien été supprimé !'
         );

        return $this->redirectToRoute('app_comics');
    }

}
