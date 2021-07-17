<?php

namespace App\Controller\Reservation;

use App\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ReservationCancelController extends AbstractController
{
    /**
     * Page d'erreur ou d'annulation de la commande
     */
    #[Route('/reservation/erreur/{StripeSessionId}', name: 'order.cancel', methods: ['GET'])]
    public function index(string $StripeSessionId, EntityManagerInterface $em, SessionInterface $session): Response|RedirectResponse
    {
        $reservation = $em->getRepository(Reservation::class)->findOneByStripeReference($StripeSessionId);
        
        if (!$reservation || $reservation->getClient() != $this->getUser()) {
            return $this->redirectToRoute('home');
        }

        $session->set('stripeReference', $reservation->getStripeReference());

        return $this->render(
            'reservation/cancel.html.twig',
            [
            'reservation' => $reservation
            ]
        );
    }
}
