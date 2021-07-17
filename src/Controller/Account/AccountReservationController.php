<?php

namespace App\Controller\Account;

use App\Class\Message;
use App\Class\MyMailer;
use App\Entity\Reservation;
use App\Repository\PassengerRepository;
use App\Repository\ReservationRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Stripe\Checkout\Session;
use Stripe\Refund;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;

class AccountReservationController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Index des réservations
     */
    #[Route('/compte/mes-reservations', name: 'account.reservations', methods: ['GET'])]
    public function reservation(ReservationRepository $reservationRepo, PaginatorInterface $paginator, Request $request): Response
    {
        $datas = $reservationRepo->findReservationPaidAndRefund($this->getUser());
        $reservations = $paginator->paginate($datas, $request->query->getInt('page', 1), 5);
        return $this->render(
            'account/reservation/index.html.twig',
            ['reservations' => $reservations]
        );
    }

    /**
     * Affichage de la facture pour la réservation concernée
     */
    #[Route('/compte/mes-reservations/{id}', name: 'account.reservations.incoice', methods: ['GET'])]
    public function show(Reservation $reservation): Response|RedirectResponse
    {
        if ($this->getUser() === $reservation->getClient()) {
            return $this->render(
                'account/reservation/invoice.html.twig',
                ['reservation' => $reservation]
            );
        }

        return $this->redirectToRoute('account.reservations');
    }

    /**
     * Rembourser une réservation
     */
    #[Route('/compte/mes-reservations/rembourser/{id}', name:'account.reservations.refund', methods:['POST'])]
    public function refund(Reservation $reservation, Request $request, MyMailer $mailer): RedirectResponse
    {
        if ($this->isCsrfTokenValid('refund', $request->get('token'))
            && $this->getUser() === $reservation->getClient() && $reservation->getDeparture()->getDate() > (new DateTime())
        ) {
            try {
                Stripe::setApiKey($request->server->get('STRIPE_PRIVATE'));
                $session = Session::retrieve($reservation->getStripeReference());
                Refund::create(
                    [
                    'payment_intent' => $session->payment_intent,
                    ]
                );
            } catch (Exception $e) {
                $this->addFlash('error', 'Erreur lors du remboursement de la réservation n° ' . $reservation->getReference());
                return $this->redirectToRoute('account.reservations');
            }

            $departure = $reservation->getDeparture();
            $return = $reservation->getReturned();
            $nbrPassengers = count($reservation->getPassengers());

            $departure->setSeat($departure->getSeat() + $nbrPassengers);
            $return->setSeat($return->getSeat() + $nbrPassengers);
            $reservation->setStatus(2);
            
            $this->em->flush();

            try {
                $message = new Message(
                    $reservation->getClient()->getEmail(),
                    'Annulation de votre réservation ' . $reservation->getReference(),
                    'mails/reservationCancel.html.twig',
                    $reservation
                );
                $mailer->send($message);
            } catch (Exception $e) {
                $this->addFlash('success', "Nous avons effectué le remboursement pour la réservation n° ". $reservation->getReference(). "mais une erreur s'est produite lors de l'envoi de l'e-mail de confirmation");
                return $this->redirectToRoute('account.reservations');
            }
            $this->addFlash('success', 'Nous avons effectué le remboursement pour la réservation n° '. $reservation->getReference());
        } else {
            $this->addFlash('error', "Oups, une erreur s'est produite !");
        }

        return $this->redirectToRoute('account.reservations');
    }

    /**
     * Modifier les passagers d'une réservation
     */
    #[Route('/compte/mes-reservations/passager/{id}', name:'account.reservations.passengers', methods: ['GET', 'POST'])]
    public function passengers(Reservation $reservation, Request $request, PassengerRepository $passengerRepo): Response|RedirectResponse
    {
        if ($this->getUser() === $reservation->getClient() && $reservation->getStatus() != 'Remboursé' && $reservation->getDeparture()->getDate() > (new DateTime())) {
            if ($request->isMethod('POST') && $this->isCsrfTokenValid('passengers', $request->get('token'))) {
                $passengers = $request->get('passengers');
                foreach ($passengers as $id => $passenger) {
                    $newPassenger = $passengerRepo->find($id);
                    if ($reservation->getPassengers()->contains($newPassenger)
                        && isset($passenger['lastname'])
                        && isset($passenger['firstname'])
                    ) {
                        if (0 !== count($this->valid($passenger))) {
                            $this->addFlash('error', 'Merci de renseigner des noms et prénoms valides');
                            return $this->redirectToRoute('account.reservations.passengers', ['id' => $reservation->getId()]);
                        }
                        $newPassenger->setFirstname($passenger['firstname']);
                        $newPassenger->setLastname($passenger['lastname']);
                    } else {
                        $this->addFlash('error', "Oups, vous avez essayé de mettre à jour des passagers inexistants pour votre réservation !");
                        return $this->redirectToRoute('account.reservations.passengers', ['id' => $reservation->getId()]);
                    }
                }
                $this->em->flush();
                $this->addFlash('success', 'Les passagers de la réservation '. $reservation->getReference().' ont bien été mis à jour');
                return $this->redirectToRoute('account.reservations');
            }

            return $this->render(
                'account/reservation/passengers.html.twig',
                ['reservation' => $reservation]
            );
        }
        return $this->redirectToRoute('account.reservations');
    }
    
    /**
     * Valider les noms et les prénoms des passagers
     */
    private function valid(array $passenger): ConstraintViolationListInterface
    {
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
        return $validator->validate($inputs, $constraints);
    }
}
