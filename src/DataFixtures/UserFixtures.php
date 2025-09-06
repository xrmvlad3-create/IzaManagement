<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\DoctorProfile;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Create test user
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setRoles(['ROLE_USER', 'ROLE_DOCTOR']);

        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            'password123'
        );
        $user->setPassword($hashedPassword);

        // Create doctor profile
        $profile = new DoctorProfile();
        $profile->setUser($user);
        $profile->setSpecialties(['dermatologie', 'chirurgie plastica']);
        $profile->setLicenseNumber('12345');

        $manager->persist($user);
        $manager->persist($profile);
        $manager->flush();
    }
}
