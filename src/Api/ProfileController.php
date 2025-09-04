<?php

namespace App\Controller\Api;

use App\Entity\DoctorProfile;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ProfileController extends AbstractController
{
    /**
     * @Route("/api/profile", name="api_profile", methods={"GET"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function getProfile(EntityManagerInterface $em): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['message' => 'Utilizator neautentificat'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        // --- AICI ESTE MODIFICAREA CRUCIALĂ ---
        // Căutăm profilul de doctor asociat acestui utilizator.
        $doctorProfile = $em->getRepository(DoctorProfile::class)->findOneBy(['user' => $user]);

        $specialties = [];
        if ($doctorProfile && method_exists($doctorProfile, 'getSpecialties')) {
            // Presupunem că getSpecialties() returnează un array de string-uri.
            $specialties = $doctorProfile->getSpecialties();
        }

        // Verificăm dacă nu am găsit specialități și asignăm una implicită pentru testare, dacă este cazul.
        // Puteți elimina această linie în producție dacă toți doctorii trebuie să aibă specialități.
        if (empty($specialties)) {
            $specialties = ['dermatologie']; // Fallback pentru a asigura funcționarea
        }

        $userData = [
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'specialties' => $specialties,
        ];

        return $this->json($userData);
    }
}
