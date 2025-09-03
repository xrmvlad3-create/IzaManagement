<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SpaController extends AbstractController
{
    #[Route('/{reactRouting}', name: 'app_home', requirements: ['reactRouting' => '^(?!api|_(profiler|wdt)).*'], defaults: ['reactRouting' => null], methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('base.html.twig');
    }
}
