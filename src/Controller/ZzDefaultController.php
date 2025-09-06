<?php
// src/Controller/SpaController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ZzDefaultController extends AbstractController
{
    #[Route('/{reactRouting?}', name: 'app_home', requirements: ['reactRouting' => '^(?!api|_profiler|_wdt).*'], defaults: ['reactRouting' => null])]
    public function index(): Response
    {
        return $this->render('base.html.twig');
    }
}
