<?php

namespace App\Controller;

use App\Entity\Mark;
use App\Entity\Series;
use App\Form\MarkType;
use App\Form\SerieType;
use App\Repository\MarkRepository;
use App\Repository\SeriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security as ConfigurationSecurity;


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
     * Ce controlleur permet de voir des series publiques
     *
     * @return Response
     */
    #[Route('/series/communaute', 'serie.community', methods: ['GET'])]
    public function indexPublic(
        SeriesRepository $repository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $cache = new FilesystemAdapter();
        $data = $cache->get('series', function (ItemInterface $item) use ($repository) {
            $item->expiresAfter(15);
            return $repository->findPublicSerie(null);
        });

        $series = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/serie/community.html.twig', [
            'series' => $series
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

    #[ConfigurationSecurity("is_granted('ROLE_USER') and (series.getIsPublic() === true || user === series.getUser())")]
    #[Route('/series/{id}', name: 'series.show', methods: ['GET', 'POST'])]
    public function show(
        Series $series, 
        Request $request, 
        MarkRepository $markRepository,
        EntityManagerInterface $manager
        ) : Response
    {
        $mark = new Mark();
        $form = $this->createForm(MarkType::class, $mark);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $mark->setUser($this->getUser());
            $mark->setSeries($series);

            $existingMark = $markRepository->findOneBy([
                'user' => $this->getUser(),
                'series' => $series
            ]);

            if (!$existingMark) {
                $manager->persist($mark);
            }else {
                $existingMark->setMark(
                    $form->getData()->getMark()
                );
            }

            $manager->flush();

            $this->addFlash(
               'success',
                'Votre note a bien été prise en compte!'
            );

            return $this->redirectToRoute('series.show', ['id' => $series->getId()]);

        }

        return $this->render('pages/serie/show.html.twig', [
            'series' => $series,
            'form' => $form->createView()
        ]);
    }
}


