<?php

namespace App\Controller;

use App\Class\SearchFlight;
use App\Form\SearchFlightType;
use App\Repository\LocationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * Page d'accueil
     */
    #[Route('/', name: 'home', methods: ['GET'])]
    public function index(LocationRepository $repoLocation): Response
    {
        $search = new SearchFlight();

        $searchForm = $this->createForm(SearchFlightType::class, $search);

        $destinations = $repoLocation->findPopularDestination();

        return $this->render(
            'home/index.html.twig',
            [
            'destinations' => $destinations,
            'searchForm' => $searchForm->createView()
            ]
        );
    }
}
