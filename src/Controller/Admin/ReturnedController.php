<?php

namespace App\Controller\Admin;

use App\Entity\Returned;
use App\Form\ReturnedType;
use App\Repository\ReturnedRepository;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/returned')]
class ReturnedController extends AbstractController
{
    /**
     * Index des vols retours
     */
    #[Route('/', name: 'returned.index', methods: ['GET'])]
    public function index(ReturnedRepository $returnedRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $returneds = $paginator->paginate($returnedRepository->findAll(), $request->query->getInt('page', 1), 5);

        return $this->render(
            'admin/returned/index.html.twig',
            [
            'returneds' => $returneds,
            ]
        );
    }

    /**
     * Créer un nouveau vol de retour
     */
    #[Route('/new', name: 'returned.new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response|RedirectResponse
    {
        $returned = new Returned();
        $form = $this->createForm(ReturnedType::class, $returned);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($returned);
            $entityManager->flush();
            $this->addFlash('success', 'Le vol retour '. $returned->getReference() . ' a bien été créé');

            return $this->redirectToRoute('returned.index');
        }

        return $this->render(
            'admin/returned/new.html.twig',
            [
            'returned' => $returned,
            'form' => $form->createView(),
            ]
        );
    }

    /**
     * Afficher un vol de retour
     */
    #[Route('/{id}', name: 'returned.show', methods: ['GET'])]
    public function show(Returned $returned): Response
    {
        return $this->render(
            'admin/returned/show.html.twig',
            [
            'returned' => $returned,
            ]
        );
    }

    /**
     * Editer un vol de retour
     */
    #[Route('/{id}/edit', name: 'returned.edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Returned $returned): Response|RedirectResponse
    {
        $form = $this->createForm(ReturnedType::class, $returned);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Le vol retour '. $returned->getReference() . ' a bien été édité');

            return $this->redirectToRoute('returned.index');
        }

        return $this->render(
            'admin/returned/edit.html.twig',
            [
            'returned' => $returned,
            'form' => $form->createView(),
            ]
        );
    }

    /**
     * Supprimer un vol de retour
     */
    #[Route('/{id}', name: 'returned.delete', methods: ['POST'])]
    public function delete(Request $request, Returned $returned): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$returned->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            try {
                $entityManager->remove($returned);
                $entityManager->flush();
            } catch (Exception $e) {
                $this->addFlash('error', 'Impossible de supprimer le vol de retour '. $returned->getReference() . ' car il est lié à des réservations');
                return $this->redirectToRoute('returned.index');
            }

            $this->addFlash('success', 'Le vol retour '. $returned->getReference() . ' a bien été supprimé');
        }

        return $this->redirectToRoute('returned.index');
    }
}
