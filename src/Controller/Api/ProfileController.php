<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api')]
#[IsGranted('ROLE_USER')]
class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'api_profile', methods: ['GET'])]
    public function profile(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['error' => 'User not authenticated'], 401);
        }

        // Get specialties from DoctorProfile if exists
        $specialties = [];
        if (method_exists($user, 'getDoctorProfile') && $user->getDoctorProfile()) {
            $specialties = $user->getDoctorProfile()->getSpecialties() ?? [];
        }

        // If no specialties, provide default ones
        if (empty($specialties)) {
            $specialties = ['dermatologie', 'chirurgie plastica'];
        }

        return $this->json([
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'specialties' => $specialties,
        ]);
    }
}
