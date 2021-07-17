<?php

namespace App\Controller\Account;

use App\Entity\User;
use App\Form\PasswordRecovery;
use App\Form\UserEditType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountProfilController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Affichage du profil
     */
    #[Route('/compte/mon-profil', name: 'account.profil', methods: ['GET'])]
    public function profil(): Response
    {
        return $this->render('account/profil/show.html.twig');
    }

     /**
     * Editer le mot de passe
     */
    #[Route('/compte/mon-profil/mot-de-passe', name: 'account.profil.password', methods: ['GET', 'POST'])]
    public function password(Request $request, UserPasswordEncoderInterface $encoder): RedirectResponse|Response
    {
        $user = $this->getUser();
        $form = $this->createForm(PasswordRecovery::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $encoder->encodePassword($user, $form->getData()['password']);
            $user->setPassword($newPassword);
            $this->em->flush();
            $this->addFlash("success", "Votre mot de passe a bien été modifié");
            return $this->redirectToRoute('account.index');
        }
        return $this->render(
            'account/password/edit.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Editer le profil
     */
    #[Route('/compte/mon-profil/{id}', name: 'account.profil.edit', methods: ['GET', 'POST'])]
    public function editProfil(User $user, Request $request): RedirectResponse|Response
    {
        if ($user != $this->getUser()) {
            return $this->redirectToRoute('account.index');
        }

        $form = $this->createForm(
            UserEditType::class,
            [
                'firstname' => $user->getFirstname(),
                'lastname' => $user->getLastname(),
                'address' => $user->getAddress(),
                'postalCode' => $user->getPostalCode(),
                'city' => $user->getCity(),
                'country' => $user->getCountry(),
                'phone' => $user->getPhone(),
                'email' => $user->getEmail()
            ]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user->setAddress($data['address'])
                ->setPostalCode($data['postalCode'])
                ->setFirstname($data['firstname'])
                ->setLastname($data['lastname'])
                ->setCity($data['city'])
                ->setCountry($data['country'])
                ->setPhone($data['phone'])
                ->setEmail($data['email']);

            $this->em->flush();
            $this->addFlash('success', 'Vos cordonnées ont bien été mises à jour');
            return $this->redirectToRoute('account.profil');
        }

        return $this->render(
            'account/profil/edit.html.twig',
            [
            'form' => $form->createView(),
            'user' => $user
            ]
        );
    }
}
