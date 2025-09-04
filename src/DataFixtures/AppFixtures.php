<?php

namespace App\DataFixtures;

use App\Entity\ClinicalCase;
use App\Entity\Procedure;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher) {}

    public function load(ObjectManager $manager): void
    {
        // === Utilizatori ===
        $adminUser = new User();
        $adminUser->setEmail('admin@example.com');
        $adminUser->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $adminUser->setPassword($this->passwordHasher->hashPassword($adminUser, 'password123'));
        // Specialitățile sunt gestionate automat de metoda getSpecialties() pentru admin
        $manager->persist($adminUser);

        $dermatologist = new User();
        $dermatologist->setEmail('dermatolog@example.com');
        $dermatologist->setRoles(['ROLE_USER']);
        $dermatologist->setPassword($this->passwordHasher->hashPassword($dermatologist, 'password123'));
        $dermatologist->setSpecialties(['dermatologie', 'estetica']);
        $manager->persist($dermatologist);

        // === Cazuri Clinice și Proceduri ===
        $alopeciaCase = new ClinicalCase();
        $alopeciaCase->setName('Alopecie androgenetică, stadiu incipient');
        $alopeciaCase->setSpecialty('dermatologie');
        $alopeciaCase->setCreatedBy($adminUser);
        $alopeciaCase->setSymptoms('Rărirea părului în zona vertexului.');
        $alopeciaCase->setTreatmentProtocol('Minoxidil 5% topic, Finasteridă 1mg oral.');
        $manager->persist($alopeciaCase);

        $prpProcedure = new Procedure();
        $prpProcedure->setName('Terapia PRP (Plasma Bogată în Trombocite)');
        $prpProcedure->setDescription('Procedură minim invazivă pentru stimularea creșterii părului.');
        $prpProcedure->setSpecialty('dermatologie');
        $prpProcedure->setCreatedBy($adminUser);
        $alopeciaCase->addProcedure($prpProcedure); // Legăm procedura de caz
        $manager->persist($prpProcedure);

        $meniscusCase = new ClinicalCase();
        $meniscusCase->setName('Leziune de menisc');
        $meniscusCase->setSpecialty('ortopedie');
        $meniscusCase->setCreatedBy($adminUser);
        $manager->persist($meniscusCase);

        $manager->flush();
    }
}
