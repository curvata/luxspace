<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AboutController extends AbstractController
{
    /**
     * Page c'est quoi LUXSPACE ?
     */
    #[Route('/a-propos', name: 'about', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('cestQuoi/index.html.twig');
    }
}
