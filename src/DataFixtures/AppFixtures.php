<?php

namespace App\DataFixtures;

use App\Entity\DoctorProfile;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // --- Utilizatorul personalizat: Iza ---
        $izaUser = new User();

        // Utilizatorul (poate fi și un email valid, ex: 'iza@management.com')
        $izaUser->setEmail('Iza@vlad.ro');

        // Rolurile necesare pentru a fi considerat doctor
        $izaUser->setRoles(['ROLE_DOCTOR', 'ROLE_USER']);

        // Parola 'IV' va fi hash-uită automat
        $hashedPassword = $this->passwordHasher->hashPassword(
            $izaUser,
            'IV'
        );
        $izaUser->setPassword($hashedPassword);

        $manager->persist($izaUser);

        // --- Crearea profilului de doctor pentru Iza ---
        $doctorProfile = new DoctorProfile();
        $doctorProfile->setUser($izaUser); // Asocierea cu utilizatorul Iza

        // Adăugăm specialități pentru a asigura funcționarea dashboard-ului
        $doctorProfile->setSpecialties(['dermatologie', 'chirurgie plastică']);

        $manager->persist($doctorProfile);

        // Se salvează totul în baza de date
        $manager->flush();
    }
}
