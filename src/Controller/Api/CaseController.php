<?php

namespace App\Controller\Api;

use App\Repository\ClinicalCaseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api')]
#[IsGranted('ROLE_USER')]
class CaseController extends AbstractController
{
    #[Route('/cases', name: 'api_cases_list', methods: ['GET'])]
    public function list(
        Request $request,
        ClinicalCaseRepository $repository
    ): JsonResponse {
        $specialty = $request->query->get('specialty');

        if (!$specialty) {
            return $this->json(['error' => 'Specialty parameter is required'], 400);
        }

        $cases = $repository->findBy(['specialty' => $specialty]);

        $data = array_map(function($case) {
            return [
                'id' => $case->getId(),
                'name' => $case->getName(),
                'specialty' => $case->getSpecialty(),
                'description' => $case->getDescription(),
            ];
        }, $cases);

        return $this->json($data);
    }
