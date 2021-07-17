<?php

namespace App\Controller\Account;

use App\Repository\ReservationRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    /**
     * Index du compte client
     */
    #[Route('/compte', name: 'account.index', methods: ['GET'])]
    public function index(ReservationRepository $resaRepo): Response
    {
        $data = $resaRepo->findShortDepartureDate($this->getUser());
        $nextDeparture = null;

        if ($data) {
            $nextDeparture['date'] = $data['date']->diff(new DateTime());
            $nextDeparture['reservation'] = $data[0];
        }

        return $this->render(
            'account/index.html.twig',
            ['nextDeparture' => $nextDeparture]
        );
    }
}
