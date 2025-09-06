<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\ClinicalCaseRepository;
use App\Repository\ProcedureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api')]
class DashboardController extends AbstractController
{
    #[Route('/dashboard/stats', name: 'api_dashboard_stats', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getStats(
        Request $request,
        ClinicalCaseRepository $caseRepo,
        ProcedureRepository $procedureRepo
    ): JsonResponse {
        /** @var User $user */
        $user = $this->getUser();
        $specialty = $request->query->get('specialty');

        if (!$specialty || !is_string($specialty)) {
            return $this->json(['message' => 'Parametrul "specialty" este obligatoriu.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        if (!in_array($specialty, $user->getSpecialties())) {
            return $this->json(['message' => 'Acces neautorizat la această specialitate.'], JsonResponse::HTTP_FORBIDDEN);
        }

        try {
            $totalCases = $caseRepo->count(['specialty' => $specialty]);
            $totalProcedures = $procedureRepo->count(['specialty' => $specialty]);

            // acum folosim metoda din repository
            $newProceduresCount = $procedureRepo->countRecentBySpecialty($specialty, 30);
        } catch (\Exception $e) {
            return $this->json(['message' => 'Eroare la calcularea statisticilor.'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json([
            'totalCasesInSpecialty' => $totalCases,
            'totalProceduresInSpecialty' => $totalProcedures,
            'newProceduresLast30Days' => (int) $newProceduresCount,
        ]);
    }

    #[Route('/procedures/recent', name: 'api_procedures_recent', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getRecentProcedures(Request $request, ProcedureRepository $procedureRepo): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $specialty = $request->query->get('specialty');

        if (!$specialty || !is_string($specialty)) {
            return $this->json(['message' => 'Parametrul "specialty" este obligatoriu.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        if (!in_array($specialty, $user->getSpecialties())) {
            return $this->json(['message' => 'Acces neautorizat la această specialitate.'], JsonResponse::HTTP_FORBIDDEN);
        }

        try {
            // acum folosim metoda din repository
            $recentProcedures = $procedureRepo->findRecentBySpecialty($specialty, 30);
        } catch (\Exception $e) {
            return $this->json(['message' => 'Eroare la preluarea procedurilor recente.'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        $data = array_map(fn($procedure) => [
            'id' => $procedure->getId(),
            'name' => $procedure->getName(),
            'specialty' => $procedure->getSpecialty(),
        ], $recentProcedures);

        return $this->json($data);
    }
}
