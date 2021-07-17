<?php

namespace App\Controller\Security;

use App\Class\Message;
use App\Class\MyMailer;
use App\Entity\PasswordReset;
use App\Entity\User;
use App\Form\PasswordRecovery;
use App\Form\PasswordRecoveryMail;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Validation;

class PasswordRecoveryController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Renseigner l'adresse e-mail
     */
    #[Route('/mot-de-passe-oublie', name: 'password.recovery.index')]
    public function index(Request $request, MyMailer $mailer): Response|RedirectResponse
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('account.index');
        }

        $form = $this->createForm(PasswordRecoveryMail::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mail = $form->getData()['email'];
            $validator = Validation::createValidator();

            if (count($validator->validate($mail, [new Email()])) > 0) {
                $this->addFlash('error', 'Merci de renseigner une adresse e-mail valide');
            } else {
                $user = $this->em->getRepository(User::class)->findOneByEmail($mail);

                if ($user) {
                    $reset = (new PasswordReset())
                        ->setCreatedAt(new DateTime())
                        ->setUser($user)
                        ->setToken(uniqid('reset_luxspace'));
                    $user->setPasswordReset($reset);

                    $this->em->persist($reset);
                    $this->em->flush();

                    try {
                        $message = new Message(
                            $user->getEmail(),
                            'Réinitialisation du mot de passe',
                            'mails/passwordRecovery.html.twig',
                            $reset
                        );
                        $mailer->send($message);
                    } catch (Exception $e) {
                        $this->addFlash('error', "Erreur lors de l'envoi de l'email de récupération, merci de réessayer");
                        return $this->redirectToRoute('password.recovery.index');
                    }

                    return $this->redirectToRoute('home');
                }
                $this->addFlash("error", "Aucun compte trouvé pour l'adresse e-mail " . $mail);
            }
        }

        return $this->render(
            'security/password/mail.html.twig',
            [
            'form' => $form->createView()
            ]
        );
    }

    /**
     * Renseigner le nouveau mot de passe
     */
    #[Route('/mot-de-passe-oublie/reinitialisation/{token}', name:'password.recovery')]
    public function recovery(string $token, Request $request, UserPasswordEncoderInterface $encoder): Response|RedirectResponse
    {
        $reset = $this->em->getRepository(PasswordReset::class)->findOneByToken($token);

        if ($reset) {
            $interval = $reset->getCreatedAt()->diff(new DateTime());
            if ($interval->i < 5) {
                $user = $reset->getUser();
                $form = $this->createForm(PasswordRecovery::class);
                $form->handleRequest($request);
                
                if ($form->isSubmitted() && $form->isValid()) {
                    $newPassword = $encoder->encodePassword($user, $form->getData()['password']);
                    $user->setPassword($newPassword);
                    $this->em->flush();
                    $this->addFlash("success", "Votre mot de passe a bien été réinitialisé");
                    return $this->redirectToRoute('login');
                }

                return $this->render(
                    'security/password/recovery.html.twig',
                    [
                    'form' => $form->createView()
                    ]
                );
            }
        }

        $this->addFlash('error', 'Token invalide ou expiré');
        return $this->redirectToRoute('password.recovery.index');
    }
}
