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
        // Create user Iza
        $izaUser = new User();
        $izaUser->setEmail('Iza@vlad.ro');
        $izaUser->setRoles(['ROLE_DOCTOR', 'ROLE_USER']);
        $hashedPassword = $this->passwordHasher->hashPassword($izaUser, 'IV');
        $izaUser->setPassword($hashedPassword);
        $manager->persist($izaUser);

        $doctorProfile = new DoctorProfile();
        $doctorProfile->setUser($izaUser);
        $doctorProfile->setSpecialties(['dermatologie', 'chirurgie plastică']);
        $manager->persist($doctorProfile);

        // Create admin user
        $adminUser = new User();
        $adminUser->setEmail('admin@example.test');
        $adminUser->setRoles(['ROLE_ADMIN']);
        $hashedPassword = $this->passwordHasher->hashPassword($adminUser, 'password');
        $adminUser->setPassword($hashedPassword);
        $manager->persist($adminUser);

        $adminProfile = new DoctorProfile();
        $adminProfile->setUser($adminUser);
        $adminProfile->setSpecialties(['dermatologie', 'ortopedie']);
        $manager->persist($adminProfile);

        // Create doctor user
        $doctorUser = new User();
        $doctorUser->setEmail('doctor@example.test');
        $doctorUser->setRoles(['ROLE_DOCTOR']);
        $hashedPassword = $this->passwordHasher->hashPassword($doctorUser, 'password');
        $doctorUser->setPassword($hashedPassword);
        $manager->persist($doctorUser);

        $doctorProfile2 = new DoctorProfile();
        $doctorProfile2->setUser($doctorUser);
        $doctorProfile2->setSpecialties(['dermatologie']);
        $manager->persist($doctorProfile2);

        $manager->flush();
    }
}
