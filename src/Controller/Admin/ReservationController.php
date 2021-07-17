<?php

namespace App\Controller\Admin;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/reservation')]
class ReservationController extends AbstractController
{
    /**
     * Index des réservations
     */
    #[Route('/', name: 'reservation.index', methods: ['GET'])]
    public function index(ReservationRepository $reservationRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $reservations = $paginator->paginate($reservationRepository->findAll(), $request->query->getInt('page', 1), 5);

        return $this->render(
            'admin/reservation/index.html.twig',
            [
            'reservations' => $reservations,
            ]
        );
    }

    /**
     * Créer une nouvelle réservation
     */
    #[Route('/new', name: 'reservation.new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response|RedirectResponse
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($reservation);
            $entityManager->flush();
            $this->addFlash('success', 'La réservation '. $reservation->getReference() . ' a bien été créée');

            return $this->redirectToRoute('reservation.index');
        }

        return $this->render(
            'admin/reservation/new.html.twig',
            [
            'reservation' => $reservation,
            'form' => $form->createView(),
            ]
        );
    }

    /**
     * Afficher une réservation
     */
    #[Route('/{id}', name: 'reservation.show', methods: ['GET'])]
    public function show(Reservation $reservation): Response
    {
        return $this->render(
            'admin/reservation/show.html.twig',
            [
            'reservation' => $reservation,
            ]
        );
    }

    /**
     * Editer une réservaiton
     */
    #[Route('/{id}/edit', name: 'reservation.edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reservation $reservation): Response|RedirectResponse
    {
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'La réservation '. $reservation->getReference() . ' a bien été éditée');

            return $this->redirectToRoute('reservation.index');
        }

        return $this->render(
            'admin/reservation/edit.html.twig',
            [
            'reservation' => $reservation,
            'form' => $form->createView(),
            ]
        );
    }

    /**
     * Supprimer une réservation
     */
    #[Route('/{id}', name: 'reservation.delete', methods: ['POST'])]
    public function delete(Request $request, Reservation $reservation): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$reservation->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();

            if ($reservation->getStatus() === "Payé") {
                $departure = $reservation->getDeparture();
                $return = $reservation->getReturned();
                $passengers = count($reservation->getPassengers());
                $departure->setSeat($departure->getSeat() + $passengers);
                $return->setSeat($return->getSeat() + $passengers);
            }
            $entityManager->remove($reservation);
            $entityManager->flush();
            $this->addFlash('success', 'La réservation '. $reservation->getReference() . ' a bien été supprimée');
        }

        return $this->redirectToRoute('reservation.index');
    }
}
