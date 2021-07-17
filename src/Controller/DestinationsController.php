<?php

namespace App\Controller;

use App\Class\SearchFlight;
use App\Entity\Location;
use App\Form\ProductSearchFlightType;
use App\Repository\LocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DestinationsController extends AbstractController
{
    
    /**
     * Index des destinations
     */
    #[Route('/destination', name: 'destination.index', methods: ['GET'])]
    public function index(LocationRepository $repoLocation): Response
    {
        $destinations = $repoLocation->findAllLocationWithDepartureMinPrice();

        return $this->render(
            'destinations/index.html.twig',
            [
            'destinations' => $destinations
            ]
        );
    }

    /**
     * Fiche produit d'une destination
     */
    #[Route('/destination/{id}', name: 'destination.show', methods: ['GET'])]
    public function show(Location $location, LocationRepository $repoLocation): Response
    {
        $search = new SearchFlight();
        $search->setDestination($location);
        $formSearch = $this->createForm(ProductSearchFlightType::class, $search);

        $destinations = $repoLocation->findWithNoThisLocation($location);

        $anotherDestinations = [];

        if ($destinations) {
            $limit = (count($destinations) < 4)? count($destinations) : 4;

            if ($limit != 1) {
                $randomKeys = array_rand($destinations, $limit);
            } else {
                $randomKeys[] = 0;
            }

            foreach ($randomKeys as $key) {
                $anotherDestinations[] = $destinations[$key];
            }
        }

        return $this->render(
            'destinations/show.html.twig',
            [
            'destination' => $location,
            'destinations' => $anotherDestinations,
            'formSearch' => $formSearch->createView()
            ]
        );
    }
}
