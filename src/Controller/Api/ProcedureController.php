<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\ProcedureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/procedures')]
class ProcedureController extends AbstractController
{
    #[Route('', name: 'api_procedures_list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function list(ProcedureRepository $repository, SerializerInterface $serializer): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        // Asigură-te că metoda getSpecialties() există pe entitatea User
        if (!method_exists($user, 'getSpecialties')) {
            return $this->json(['error' => 'User specialties not configured.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $userSpecialties = $user->getSpecialties();

        if (empty($userSpecialties)) {
            return $this->json([]); // Returnează un array gol dacă user-ul nu are specialități
        }

        $procedures = $repository->findBy(['specialty' => $userSpecialties]);

        $json = $serializer->serialize($procedures, 'json', ['groups' => 'procedure:read']);
        return new Response($json, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }
}
