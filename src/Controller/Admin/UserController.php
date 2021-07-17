<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserEditAdminType;
use App\Form\UserEditType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

#[Route('/admin/user')]
class UserController extends AbstractController
{
    /**
     * Index des utilisateurs
     */
    #[Route('/', name: 'user.index', methods: ['GET'])]
    public function index(UserRepository $userRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $users = $paginator->paginate($userRepository->findAll(), $request->query->getInt('page', 1), 5);

        return $this->render(
            'admin/user/index.html.twig',
            [
            'users' => $users,
            ]
        );
    }

    /**
     * Créer un nouvel utilisateur
     */
    #[Route('/new', name: 'user.new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserPasswordEncoderInterface $encoder): Response|RedirectResponse
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'L\'utilisateur '. $user->getLastname() . ' a bien été créé');

            return $this->redirectToRoute('user.index');
        }

        return $this->render(
            'admin/user/new.html.twig',
            [
            'user' => $user,
            'form' => $form->createView(),
            ]
        );
    }

    /**
     * Afficher un utilisateur
     */
    #[Route('/{id}', name: 'user.show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render(
            'admin/user/show.html.twig',
            [
            'user' => $user,
            ]
        );
    }

    /**
     * Editer un utilisateur
     */
    #[Route('/{id}/edit', name: 'user.edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user): Response|RedirectResponse
    {
        $form = $this->createForm(UserEditAdminType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'L\'utilisateur '. $user->getLastname() . ' a bien été édité');

            return $this->redirectToRoute('user.index');
        }

        return $this->render(
            'admin/user/edit.html.twig',
            [
            'user' => $user,
            'form' => $form->createView(),
            ]
        );
    }

    /**
     * Supprimer un utilisateur
     */
    #[Route('/{id}', name: 'user.delete', methods: ['POST'])]
    public function delete(Request $request, User $user): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();

            try {
                $entityManager->remove($user);
                $entityManager->flush();
            } catch (Exception $e) {
                $this->addFlash('error', "Impossible de supprimer l'utilisateur ". $user->getLastname() . ' ' . $user->getFirstname() . ' car il est lié à des réservations');
                return $this->redirectToRoute('user.index');
            }
            
            $this->addFlash('success', 'L\'utilisateur '. $user->getLastname() . ' a bien été supprimé');
        }

        return $this->redirectToRoute('user.index');
    }
}
