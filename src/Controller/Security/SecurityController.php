<?php

namespace App\Controller\Security;

use App\Class\Message;
use App\Class\MyMailer;
use App\Entity\User;
use App\Form\UserRegistrationType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * Création d'un compte client
     */
    #[Route('/inscription', name: 'register')]
    public function create(Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $em, MyMailer $mailer): Response|RedirectResponse
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('account.index');
        }

        $user = new User;
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $em->persist($user);
            $em->flush();
            try {
                $message = new Message(
                    $user->getEmail(),
                    'Création de votre compte client',
                    'mails/registration.html.twig',
                    $user
                );
                $mailer->send($message);
            } catch (Exception $e) {
                $this->addFlash('success', "Votre compte a bien été créé mais une erreur s'est produite lors de l'envoi de l'e-mail de confirmation");
                return $this->redirectToRoute('login');
            }
            $this->addFlash('success', 'Le compte pour l\'utilisateur '. $user->getEmail() . ' a bien été créé');
            return $this->redirectToRoute('login');
        }

        return $this->render(
            'security/create.html.twig',
            [
            'form' => $form->createView()
            ]
        );
    }

    /**
     * Se connecter
     */
    #[Route('/connexion', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response|RedirectResponse
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('account.index');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $email = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['email' => $email, 'error' => $error]);
    }

    /**
     * Se déconnecter
     */
    #[Route('/deconnexion', name: 'logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
