<?php

namespace App\Controller\Reservation;

use App\Class\Message;
use App\Class\MyMailer;
use App\Entity\Passenger;
use App\Entity\Reservation;
use App\Repository\PassengerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ReservationSuccessController extends AbstractController
{
    /**
     * Succès de la réservation
     */
    #[Route('/reservation/merci/{StripeSessionId}', name: 'reservation.success', methods: ['GET'])]
    public function index(string $StripeSessionId, EntityManagerInterface $em, MyMailer $mailer, SessionInterface $session): Response|RedirectResponse
    {
        /** @var ReservationRepository */
        $resaRepo = $em->getRepository(Reservation::class);

        $reservation = $resaRepo->findOneByStripeReference($StripeSessionId);

        $user = $this->getUser();

        if (!$reservation || $reservation->getClient() != $this->getUser() || $reservation->getStatus() === 'Payé') {
            return $this->redirectToRoute('home');
        }

        $reservation->setStatus(1);

        $departure = $reservation->getDeparture();
        $return = $reservation->getReturned();
        $passengers = count($reservation->getPassengers());

        $departure->setSeat($departure->getSeat() - $passengers);
        $return->setSeat($return->getSeat() - $passengers);

        $em->flush();

        if ($session->has('stripeReference')) {
            $session->remove('stripeReference');
        }

        try {
            $message = new Message(
                $reservation->getClient()->getEmail(),
                'Votre commande numéro ' . $reservation->getReference(),
                'mails/reservationSuccess.html.twig',
                $reservation
            );
            $mailer->send($message);
        } catch (Exception $e) {
            $this->addFlash('error', "Nous avons bien reçu votre réservation, mais une erreur s'est produite lors de l'envoi de l'e-mail de confirmation");
        }

        $reservationsNotPaid = $resaRepo->findNotPaidReservation($user);

        if ($reservationsNotPaid) {
            foreach ($reservationsNotPaid as $value) {
                $em->remove($value);
            }
        }
        $em->flush();

        return $this->render(
            'reservation/success.html.twig',
            ['reservation' => $reservation]
        );
    }
}
