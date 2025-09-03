<?php
// src/Controller/ZzDefaultController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ZzDefaultController extends AbstractController
{
    #[Route('/{reactRouting?}', name: 'home', requirements: ['reactRouting' => '^(?!api|login|_profiler|_wdt).+'], defaults: ['reactRouting' => null])]
    public function index(): Response
    {
        // Redăm fișierul de bază care conține <div id="root"> și tag-urile Encore
        return $this->render('base.html.twig');
    }
}
