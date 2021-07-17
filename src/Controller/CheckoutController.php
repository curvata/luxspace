<?php

namespace App\Controller;

use App\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CheckoutController extends AbstractController
{
    /**
     * Processus de paiement Stripe
     */
    #[Route('/paiement/{id}', name: 'checkout', methods: ['POST'])]
    public function index(Reservation $reservation, EntityManagerInterface $em, SessionInterface $session, Request $request): JsonResponse
    {
        if ($reservation->getClient() === $this->getUser() && $reservation->getStatus() === 'Non payé') {
            Stripe::setApiKey($request->server->get('STRIPE_PRIVATE'));

            $MyDomain = $request->server->get('MY_DOMAIN');
            $picture = $reservation->getDeparture()->getDestination()->getPictureHeader();
            $urlPicture = $MyDomain."/images/location/".$picture;

            $checkout_session = Session::create(
                [
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'eur',
                            'unit_amount' => ($reservation->getDeparturePrice() * 100),
                            'product_data' => [
                                'name' => 'Vol aller Luxembourg vers '. $reservation->getDeparture()->getDestination(),
                                'images' => [$urlPicture],
                            ],
                        ],
                        'quantity' => count($reservation->getPassengers()),
                    ],
                    [
                        'price_data' => [
                            'currency' => 'eur',
                            'unit_amount' => ($reservation->getReturnPrice() * 100),
                            'product_data' => [
                                'name' => 'Vol retour '. $reservation->getReturned()->getFfrom(). ' vers Luxembourg',
                                'images' => [$urlPicture],
                            ],
                        ],
                        'quantity' => count($reservation->getPassengers()),
                    ]
                ],
                'mode' => 'payment',
                'customer_email' => $this->getUser()->getEmail(),
                'success_url' => $MyDomain . '/reservation/merci/{CHECKOUT_SESSION_ID}',
                'cancel_url' => $MyDomain . '/reservation/erreur/{CHECKOUT_SESSION_ID}',
                ]
            );

            $reservation->setStripeReference($checkout_session->id);
            $em->flush($reservation);
            $session->set('stripeReference', $checkout_session->id);
            return new JsonResponse(['id' => $checkout_session->id]);
        }

        return new JsonResponse(['erreur' => 'Réservation invalide !']);
    }
}
