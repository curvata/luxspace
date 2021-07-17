<?php

namespace App\Controller;

use App\Class\Contact;
use App\Class\Message;
use App\Class\MyMailer;
use App\Form\ContactType;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * Page de contact
     */
    #[Route('/contact', name: 'contact', methods: ['GET', 'POST'])]
    public function index(Request $request, MyMailer $mailer): Response
    {
        $contact = new Contact();

        $user = $this->getUser();

        if ($user) {
            $contact->setEmail($user->getEmail());
            $contact->setFirstname($user->getFirstname());
            $contact->setLastname($user->getLastname());
        }

        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $message = new Message(
                    $request->server->get('MY_MAIL'),
                    '
                    Contact depuis le formulaire de LUXSPACE',
                    'mails/contact.html.twig',
                    $contact
                );
                $mailer->send($message);
            } catch (Exception $e) {
                $this->addFlash('error', "Erreur lors de l'envoi de votre message");
                return $this->render(
                    'contact/index.html.twig',
                    [
                    'form' => $form->createView()
                    ]
                );
            }
            $this->addFlash('success', 'Votre message a bien été envoyé');
        }

        return $this->render(
            'contact/index.html.twig',
            [
            'form' => $form->createView()
            ]
        );
    }
}
