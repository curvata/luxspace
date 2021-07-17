<?php

namespace App\Controller;

use App\Class\SearchFlight;
use App\Entity\Departure;
use App\Entity\Returned;
use App\Form\SearchFlightType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchFlightController extends AbstractController
{
    /**
     * Page de recherche des vols
     */
    #[Route('/recherche-vols', name: 'search.flight', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $search = new SearchFlight();

        $searchForm = $this->createForm(SearchFlightType::class, $search);

        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $departures = $em->getRepository(Departure::class)->findFlights($search);
            $returneds = $em->getRepository(Returned::class)->findFlights($search);

            return $this->render(
                'search/index.html.twig',
                [
                'searchForm' => $searchForm->createView(),
                'passengers' => $search->getPassagers(),
                'departures' => $departures,
                'returneds' => $returneds
                ]
            );
        }

        return $this->render(
            'search/index.html.twig',
            ['searchForm' => $searchForm->createView()]
        );
    }
}
