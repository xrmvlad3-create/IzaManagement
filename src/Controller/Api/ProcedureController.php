<?php

namespace App\Controller\Api;

use App\Repository\ProcedureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api')]
#[IsGranted('ROLE_USER')]
class ProcedureApiController extends AbstractController
{
    #[Route('/procedures', name: 'api_procedures_list', methods: ['GET'])]
    public function list(
        Request $request,
        ProcedureRepository $repository
    ): JsonResponse {
        $specialty = $request->query->get('specialty');

        if (!$specialty) {
            return $this->json(['error' => 'Specialty parameter is required'], 400);
        }

        $procedures = $repository->findBy(['specialty' => $specialty]);

        $data = array_map(function($procedure) {
            return [
                'id' => $procedure->getId(),
                'name' => $procedure->getName(),
                'description' => $procedure->getDescription(),
                'tutorialSteps' => $procedure->getTutorialSteps() ?? [],
                'videoLinks' => $procedure->getVideoLinks() ?? [],
                'warnings' => $procedure->getWarnings() ?? '',
                'specialty' => $procedure->getSpecialty(),
                'clinicalCase' => $procedure->getClinicalCase() ? [
                    'id' => $procedure->getClinicalCase()->getId(),
                    'name' => $procedure->getClinicalCase()->getName(),
                ] : null,
            ];
        }, $procedures);

        return $this->json($data);
    }

    #[Route('/procedures/recent', name: 'api_procedures_recent', methods: ['GET'])]
    public function recent(
        Request $request,
        ProcedureRepository $repository
    ): JsonResponse {
        $specialty = $request->query->get('specialty');

        if (!$specialty) {
            return $this->json(['error' => 'Specialty parameter is required'], 400);
        }

        // Get procedures created in last 30 days
        $procedures = $repository->findRecentBySpecialty($specialty, 30);

        $data = array_map(function($procedure) {
            return [
                'id' => $procedure->getId(),
                'name' => $procedure->getName(),
                'specialty' => $procedure->getSpecialty(),
            ];
        }, $procedures);

        return $this->json($data);
    }
}
