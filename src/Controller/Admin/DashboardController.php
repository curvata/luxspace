<?php

namespace App\Controller\Admin;

use App\Repository\ReservationRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * Retourne le nombre de nouveaux clients et de nouvelles réservations par mois pour l'année courante
     */
    #[Route('/admin/graph', name: 'admin.graph', methods: ['GET'])]
    public function index(ReservationRepository $reservationRepo, UserRepository $userRepo): JsonResponse
    {
        $datasUser = $userRepo->findUserByMonthYearNow();
        $datasReservation = $reservationRepo->findReservationByMonthYearNow();
        
        for ($i = 1; $i <= 12; $i++) {
            $byMonthAsc['reservation'][$i] = 0;
            $byMonthAsc['user'][$i] = 0;
        }

        if ($datasUser) {
            foreach ($datasUser as $value) {
                $byMonthAsc['user'][$value['mois']] = $value['users'];
            }
        }

        if ($datasReservation) {
            foreach ($datasReservation as $value) {
                $byMonthAsc['reservation'][$value['mois']] = $value['reservations'];
            }
        }

        return new JsonResponse($byMonthAsc);
    }
}
