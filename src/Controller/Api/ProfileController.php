<?php

namespace App\Controller\Api;

use App\Entity\DoctorProfile;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface; // Importăm Logger-ul
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ProfileController extends AbstractController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @Route("/api/profile", name="api_profile", methods={"GET"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function getProfile(EntityManagerInterface $em): JsonResponse
    {
        /** @var User|null $user */
        $user = $this->getUser();

        if (!$user) {
            $this->logger->warning('API /api/profile a fost accesat fără un utilizator autentificat.');
            return $this->json(['message' => 'Utilizator neautentificat'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $this->logger->info('Căutare profil pentru utilizatorul: ' . $user->getEmail());

        $doctorProfile = $em->getRepository(DoctorProfile::class)->findOneBy(['user' => $user]);

        $specialties = [];
        if ($doctorProfile) {
            $this->logger->info('Profil de doctor găsit. Se încearcă extragerea specialităților.');
            // Asigurăm că metoda există înainte de a o apela
            if (method_exists($doctorProfile, 'getSpecialties')) {
                $specialties = $doctorProfile->getSpecialties() ?? []; // Folosim ?? [] pentru a ne proteja de null
                $this->logger->info('Specialități găsite: ' . implode(', ', $specialties));
            } else {
                $this->logger->warning('Entitatea DoctorProfile nu are metoda getSpecialties.');
            }
        } else {
            $this->logger->warning('Nu a fost găsit niciun DoctorProfile pentru utilizatorul ' . $user->getEmail());
        }

        // Fallback robust: dacă, după toate verificările, nu avem specialități,
        // oferim una implicită pentru a nu bloca frontend-ul.
        if (empty($specialties)) {
            $this->logger->error('Nicio specialitate găsită pentru ' . $user->getEmail() . '. Se folosește fallback-ul "dermatologie".');
            $specialties = ['dermatologie'];
        }

        $userData = [
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'specialties' => $specialties,
        ];

        return $this->json($userData);
    }
}
