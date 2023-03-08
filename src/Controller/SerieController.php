<?php

namespace App\Controller;

use App\Entity\Series;
use App\Form\SerieType;
use App\Repository\SeriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SerieController extends AbstractController
{
  /**
   * Ce controlleur montre toutes les series
   *
   * @param SeriesRepository $repository
   * @param PaginatorInterface $paginator
   * @param Request $request
   * @return Response
   */
    #[IsGranted('ROLE_USER')]
    #[Route('/series', name: 'series.index', methods: ['GET'])]
    public function index(
        SeriesRepository $repository, 
        PaginatorInterface $paginator, 
        Request $request
        ): Response
    {

        $series = $paginator->paginate(
            $repository->findBy(['user' => $this->getUser()]),
            $request->query->getInt('page', 1), 
            10 
        );

        return $this->render('pages/serie/index.html.twig', [
            'series' => $series,
        ]);
    }

    /**
     * Ce controlleur affiche un formulaire qui ajoute une serie 
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[IsGranted('ROLE_USER')]
    #[Route('/series/creation', name: 'series.new', methods: ['GET','POST'])]
    public function new(Request $request, EntityManagerInterface $manager) : Response
    {

        $series = new Series();
        $form = $this->createForm(SerieType::class, $series);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
           $series = $form->getData();
           $series->setUser($this->getUser());

           $manager->persist($series);
           $manager->flush();

           $this->addFlash(
            'success',
            'Votre serie bien été ajouté !'
 );

           return $this->redirectToRoute('series.index');
        }

        return $this->render('pages/serie/new.html.twig', [
           'form' => $form->createView()
        ]);   
    }

    /**
     * Ce controlleur permet de modifier une serie 
     *
     * @param Series $series
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Security("is_granted('ROLE_USER') and user === series.getUser()")]
    #[Route('/series/edition/{id}', 'series.edit', methods: ['GET', 'POST'])]
    public function edit( 
        Series $series, 
        Request $request, 
        EntityManagerInterface $manager 
        ) : Response
    {
        $form = $this->createForm(SerieType::class, $series);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
             $series = $form->getData();
             
             $manager->persist($series);
             $manager->flush();

             $this->addFlash(
                'success',
                'Votre serie a bien été modifiée !'
             );

            return $this->redirectToRoute('series.index');
        }

        return $this->render('pages/serie/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/series/suppression/{id}', 'series.delete', methods: ['GET'])]
    
    public function delete(EntityManagerInterface $manager, Series $series) : Response 
    {

        if(!$series) {

            $this->addFlash(
                'success',
                'La serie n\'a pas été trouvé!'
             );

            return $this->redirectToRoute('series.index');
        }

        $manager->remove($series);
        $manager->flush();

        $this->addFlash(
            'success',
            'Votre serie a bien été supprimé !'
         );

        return $this->redirectToRoute('series.index');
    }

}


