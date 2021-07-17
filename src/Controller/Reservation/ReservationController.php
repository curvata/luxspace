<?php

namespace App\Controller\Reservation;

use App\Entity\Departure;
use App\Entity\Returned;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ReservationController extends AbstractController
{
    /**
     * Première page du tunnel d'achat vol aller, vol retour et renseignement des noms et prénoms des passagers
     */
    #[Route('/reservation', name:'reservations.index', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $em, SessionInterface $session): Response|RedirectResponse
    {
        if ($session->has('stripeReference')) {
            $session->remove('stripeReference');
        }

        $passengers = $request->get('passengers');
        $departure = $request->get('departure');
        $return = $request->get('return');
        
        if ($passengers && $passengers <= 9 && $passengers > 0 && $departure && $return) {
            $departure = $em->getRepository(Departure::class)->find($departure);
            $return = $em->getRepository(Returned::class)->find($return);
        
            if ($departure && $return && $departure->getDate() > new DateTime() && $return->getDate() > $departure->getDate()) {
                if ($departure->getDestination() === $return->getFfrom()
                    && $departure->getDate() < $return->getDate()
                ) {
                    return $this->render(
                        'reservation/reservation.html.twig',
                        [
                        'departure' => $departure,
                        'return' => $return,
                        'passengers' => $passengers
                        ]
                    );
                }
            }
        }

        $this->addFlash('error', 'Merci de sélectionner un vol aller et retour');
        return $this->redirectToRoute('search.flight');
    }
}
