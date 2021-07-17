<?php

namespace App\Controller\Admin;

use App\Entity\Location;
use App\Form\LocationType;
use App\Repository\LocationRepository;
use DateTime;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/location')]
class LocationController extends AbstractController
{
    /**
     * Index des destinations
     */
    #[Route('/', name: 'location.index', methods: ['GET'])]
    public function index(LocationRepository $locationRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $locations = $paginator->paginate($locationRepository->findAll(), $request->query->getInt('page', 1), 5);
        
        return $this->render(
            'admin/location/index.html.twig',
            [
            'locations' => $locations,
            ]
        );
    }

    /**
     * Créer une nouvelle destination
     */
    #[Route('/new', name: 'location.new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response|RedirectResponse
    {
        $location = new Location();
        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($location);
            $entityManager->flush();
            $this->addFlash('success', 'La destination '. $location->getTitle() . ' a bien été créée');

            return $this->redirectToRoute('location.index');
        }

        return $this->render(
            'admin/location/new.html.twig',
            [
            'location' => $location,
            'form' => $form->createView(),
            ]
        );
    }

    /**
     * Afficher d'une destination
     */
    #[Route('/{id}', name: 'location.show', methods: ['GET'])]
    public function show(Location $location): Response
    {
        return $this->render(
            'admin/location/show.html.twig',
            [
            'location' => $location,
            ]
        );
    }

    /**
     * Editer une destination
     */
    #[Route('/{id}/edit', name: 'location.edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Location $location): Response|RedirectResponse
    {
        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $location->setUpdatedAt(new DateTime());

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'La destination '. $location->getTitle() . ' a bien été mise à jour');

            return $this->redirectToRoute('location.index');
        }

        return $this->render(
            'admin/location/edit.html.twig',
            [
            'location' => $location,
            'form' => $form->createView(),
            ]
        );
    }

    /**
     * Supprimer une desination
     */
    #[Route('/{id}', name: 'location.delete', methods: ['POST'])]
    public function delete(Request $request, Location $location): Response|RedirectResponse
    {
        $path = 'images/location/';

        if ($this->isCsrfTokenValid('delete'.$location->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();

            try {
                $entityManager->remove($location);
                $entityManager->flush();
            } catch (Exception $e) {
                $this->addFlash('error', 'Impossible de supprimer la destination '. $location->getTitle() . ' car elle est liée à des vols de départs et de retours');
                return $this->redirectToRoute('location.index');
            }

            $this->addFlash('success', 'La destination '. $location->getTitle() . ' a bien été supprimée');

            if ($location->getPictureHeader() != 'placeholder.png'
                && file_exists($path.$location->getPictureHeader())
            ) {
                unlink($path.$location->getPictureHeader());
            }
            foreach ($location->getPictures() as $picture) {
                if (file_exists($path.'small_'.$picture->getfilename())) {
                    unlink($path.$picture->getfilename());
                    unlink($path.'small_'.$picture->getfilename());
                }
            }
        }

        return $this->redirectToRoute('location.index');
    }
}
