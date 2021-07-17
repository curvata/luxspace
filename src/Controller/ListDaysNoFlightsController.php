<?php

namespace App\Controller;

use App\Repository\DepartureRepository;
use App\Repository\LocationRepository;
use App\Repository\ReturnedRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ListDaysNoFlightsController extends AbstractController
{
    /**
     * Retourne les dates sans aucun vol de départs ou de retours
     */
    #[Route('/list-days-no-flights/{id}', name: 'list.days.no.flights', methods: ['GET'])]
    public function index(
        int $id,
        LocationRepository $locationRepo,
        DepartureRepository $departureRepo,
        ReturnedRepository $returnedRepo
    ): JsonResponse {
        $location = $locationRepo->find($id);
        if ($location) {
            for ($i = 0; $i <= 365; $i++) {
                $date = (new DateTime())->modify('+ '. $i . ' day');
                $flights ['departures'][$date->format("ymd")] = $date->format("d-m-Y");
            }
    
            $flights ['returneds'] = $flights ['departures'];
    
            $departuresDate = $departureRepo->findValidDateFlight($location);
            $returnedsDate = $returnedRepo->findValidDateFlight($location);
    
            foreach ($departuresDate as $value) {
                if (isset($flights['departures'][$value['date']->format("ymd")])) {
                    unset($flights['departures'][$value['date']->format("ymd")]);
                }
            }
    
            foreach ($returnedsDate as $value) {
                if (isset($flights['returneds'][$value['date']->format("ymd")])) {
                    unset($flights['returneds'][$value['date']->format("ymd")]);
                }
            }
    
            return new JsonResponse($flights);
        }
        return new JsonResponse("La destination n\'existe pas", 401);
    }
}
