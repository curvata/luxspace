<?php

namespace App\Controller\Reservation;

use App\Entity\Departure;
use App\Entity\Passenger;
use App\Entity\Reservation;
use App\Entity\Returned;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Validation;

class ReservationRecapController extends AbstractController
{
    /**
     * Deuxième page du tunnel d'achat
     * récapitulatif de la réservation avant le paiement
     */
    #[Route('/reservation/recapitulatif', name:'reservation.recap', methods:['POST'])]
    public function index(Request $request, EntityManagerInterface $em, SessionInterface $session): Response|RedirectResponse
    {
        if ($this->isCsrfTokenValid('reservation', $request->get('token')) && $this->getUser()) {
            if ($session->has('stripeReference')) {
                $reservation = $em->getRepository(Reservation::class)->findOneByStripeReference($session->get('stripeReference'));
                $session->remove('stripeReference');
            } else {
                $passengers = $request->get('passengers');
                $departure = $request->get('departure');
                $return = $request->get('return');
                if ($passengers && $departure && $return) {
                    $countPassengers = count($passengers);
                    $departure = $em->getRepository(Departure::class)->find($departure);
                    $return = $em->getRepository(Returned::class)->find($return);
                    
                    if ($departure && $return && $countPassengers <= 9
                    && $countPassengers > 0 && $departure->getDate() > new DateTime()
                    && $return->getDate() > $departure->getDate()) {
                        if ($departure->getDestination() === $return->getFfrom()
                            && $departure->getDate() < $return->getDate()
                        ) {
                            $reservation = (new Reservation())
                                ->setClient($this->getUser())
                                ->setReference(uniqid('LUXSPACE'))
                                ->setCreateAt(new DateTime())
                                ->setDeparturePrice($departure->getPrice())
                                ->setReturnPrice($return->getPrice())
                                ->setDeparture($departure)
                                ->setReturned($return);

                            foreach ($passengers as $passenger) {
                                if (isset($passenger['lastname']) && isset($passenger['firstname'])) {
                                    $validator = Validation::createValidator();
                                    $inputs = [
                                            'firstname' => $passenger['lastname'],
                                            'lastname' => $passenger['firstname']
                                    ];
                                    $constraints = new Collection(
                                        [
                                        'firstname' => [
                                            new Length(['min' => 3, 'max' => 30]),
                                            new Regex('/^[a-zA-Z\s]+$/'),
                                            new NotBlank()
                                        ],
                                        'lastname' => [
                                            new Length(['min' => 3, 'max' => 30]),
                                            new Regex('/^[a-zA-Z\s]+$/'),
                                            new NotBlank()
                                        ]
                                        ]
                                    );
                                    if (0 !== count($validator->validate($inputs, $constraints))) {
                                        $this->addFlash('errorPassager', 'Merci de renseigner des noms et prénoms valides');
                                        return new RedirectResponse($request->server->get('HTTP_REFERER'));
                                    }
                                    $voyager = (new Passenger())
                                        ->setFirstname($passenger['firstname'])
                                        ->setLastname($passenger['lastname']);
                                    $reservation->addPassenger($voyager);
                                }
                            }
                            $em->persist($reservation);
                            $em->flush();
                        }
                    } else {
                        $this->addFlash('error', 'Vols invalide !');
                        return new RedirectResponse($request->server->get('HTTP_REFERER'));
                    }
                }
            }
            return $this->render(
                'reservation/summary.html.twig',
                ['reservation' => $reservation]
            );
        }

        $this->addFlash('error', 'Merci de vous connecter à votre compte afin de valider votre réservation');
        return new RedirectResponse($request->server->get('HTTP_REFERER'));
    }
}
