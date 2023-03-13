<?php

namespace App\Controller;

use App\Repository\SeriesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', 'home.index', methods: ['GET'])]
    public function index(
        SeriesRepository $seriesRepository
    ): Response
    {
        return $this->render('pages/home.html.twig', [
            'series' => $seriesRepository->findPublicSerie(3)
        ]);
    }
}