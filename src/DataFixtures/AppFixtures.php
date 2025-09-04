<?php

namespace App\DataFixtures;

use App\Entity\ClinicalCase;
use App\Entity\Enum\CaseStatus;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        // === Create Users ===
        $adminUser = new User();
        $adminUser->setEmail('admin@example.test');
        $adminUser->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $adminUser->setPassword($this->passwordHasher->hashPassword($adminUser, 'password123'));
        $manager->persist($adminUser);
        $this->addReference('admin-user', $adminUser);

        $doctorUser = new User();
        $doctorUser->setEmail('doctor@example.test');
        $doctorUser->setRoles(['ROLE_USER']);
        $doctorUser->setPassword($this->passwordHasher->hashPassword($doctorUser, 'password123'));
        $manager->persist($doctorUser);
        $this->addReference('doctor-user', $doctorUser);

        // === Create Clinical Cases ===
        $casesData = [
            [
                'name' => 'Hipertensiune arterială esențială',
                'description' => 'Caz standard de hipertensiune la un pacient de 55 de ani.',
                'symptoms' => 'Cefalee, amețeli ocazionale.',
                'treatmentProtocol' => 'IECA + diuretic tiazidic.',
                'procedureCodes' => ['I10'],
                'status' => CaseStatus::PUBLISHED,
                'createdBy' => 'admin-user',
            ],
            [
                'name' => 'Diabet zaharat tip 2',
                'description' => 'Pacient nou diagnosticat cu diabet tip 2, fără complicații.',
                'symptoms' => 'Poliurie, polidipsie.',
                'treatmentProtocol' => 'Metformin 500mg de două ori pe zi.',
                'procedureCodes' => ['E11.9'],
                'status' => CaseStatus::PUBLISHED,
                'createdBy' => 'admin-user',
            ],
            [
                'name' => 'Caz dermatologic în pregătire',
                'description' => 'Caz dermatologic complex, necesită investigații suplimentare.',
                'symptoms' => 'Leziuni cutanate polimorfe.',
                'treatmentProtocol' => null,
                'procedureCodes' => [],
                'status' => CaseStatus::DRAFT,
                'createdBy' => 'doctor-user',
            ],
        ];

        foreach ($casesData as $data) {
            $case = new ClinicalCase();
            $case->setName($data['name']);
            $case->setDescription($data['description']);
            $case->setSymptoms($data['symptoms']);
            $case->setTreatmentProtocol($data['treatmentProtocol']);
            $case->setProcedureCodes($data['procedureCodes']);
            $case->setStatus($data['status']);
            $case->setCreatedBy($this->getReference($data['createdBy']));
            $case->addAuditLogEntry('Caz creat prin fixtures.', 'system@fixtures');
            $manager->persist($case);
        }

        $manager->flush();
    }
}
