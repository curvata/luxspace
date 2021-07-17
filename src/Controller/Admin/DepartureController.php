<?php

namespace App\Controller\Admin;

use App\Entity\Departure;
use App\Form\DepartureType;
use App\Repository\DepartureRepository;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/departure')]
class DepartureController extends AbstractController
{
    /**
     * Index des vols de départs
     */
    #[Route('/', name: 'departure.index', methods: ['GET'])]
    public function index(DepartureRepository $departureRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $departures = $paginator->paginate($departureRepository->findAll(), $request->query->getInt('page', 1), 5);

        return $this->render(
            'admin/departure/index.html.twig',
            [
            'departures' => $departures,
            ]
        );
    }

    /**
     * Créer un nouveau vol de départ
     */
    #[Route('/new', name: 'departure.new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response|RedirectResponse
    {
        $departure = new Departure();
        $form = $this->createForm(DepartureType::class, $departure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($departure);
            $entityManager->flush();
            $this->addFlash('success', 'Le vol de départ '. $departure->getReference() . ' a bien été créé');
            return $this->redirectToRoute('departure.index');
        }

        return $this->render(
            'admin/departure/new.html.twig',
            [
            'departure' => $departure,
            'form' => $form->createView(),
            ]
        );
    }

    /**
     * Afficher un vol de départ
     */
    #[Route('/{id}', name: 'departure.show', methods: ['GET'])]
    public function show(Departure $departure): Response
    {
        return $this->render(
            'admin/departure/show.html.twig',
            [
            'departure' => $departure,
            ]
        );
    }

    /**
     * Editer un vol de départ
     */
    #[Route('/{id}/edit', name: 'departure.edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Departure $departure): Response|RedirectResponse
    {
        $form = $this->createForm(DepartureType::class, $departure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Le vol de départ '. $departure->getReference() . ' a bien été mis à jour');

            return $this->redirectToRoute('departure.index');
        }

        return $this->render(
            'admin/departure/edit.html.twig',
            [
            'departure' => $departure,
            'form' => $form->createView(),
            ]
        );
    }

     /**
     * Supprimer un vol de départ
     */
    #[Route('/{id}', name: 'departure.delete', methods: ['POST'])]
    public function delete(Request $request, Departure $departure): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$departure->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();

            try {
                $entityManager->remove($departure);
                $entityManager->flush();
            } catch (Exception $e) {
                $this->addFlash('error', 'Impossible de supprimer le vol de départ '. $departure->getReference() . ' car il est lié à des réservations');
                return $this->redirectToRoute('departure.index');
            }
            
            $this->addFlash('success', 'Le vol de départ '. $departure->getReference() . ' a bien été supprimé');
        }

        return $this->redirectToRoute('departure.index');
    }
}
