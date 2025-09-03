<?php

namespace App\DataFixtures;

use App\Entity\DermCondition;
use App\Entity\Enum\DermConditionStatus;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // === Creează Utilizatori ===
        $this->createUsers($manager);

        // === Creează Afecțiuni Dermatologice ===
        $this->createDermConditions($manager);

        $manager->flush();
    }

    private function createUsers(ObjectManager $manager): void
    {
        $usersData = [
            ['email' => 'admin@example.test', 'roles' => ['ROLE_ADMIN'], 'name' => 'Admin User'],
            ['email' => 'doctor@example.test', 'roles' => ['ROLE_USER'], 'name' => 'Doctor User'],
            ['email' => 'prof@example.test', 'roles' => ['ROLE_USER'], 'name' => 'Professor User'],
        ];

        foreach ($usersData as $data) {
            $user = new User();
            $user->setEmail($data['email']);
            $user->setRoles($data['roles']);
            $user->setName($data['name']);
            // Toți utilizatorii vor avea parola 'password123'
            $hashedPassword = $this->passwordHasher->hashPassword($user, 'password123');
            $user->setPassword($hashedPassword);
            $manager->persist($user);
        }
    }

    private function createDermConditions(ObjectManager $manager): void
    {
        $conditionsData = [
            ['slug' => 'acne-vulgaris', 'title' => 'Acne Vulgaris', 'summary' => 'A common chronic skin disease involving inflammation of the sebaceous glands.'],
            ['slug' => 'eczema', 'title' => 'Eczema (Atopic Dermatitis)', 'summary' => 'A condition that makes your skin red and itchy. It\'s common in children but can occur at any age.'],
            ['slug' => 'psoriasis', 'title' => 'Psoriasis', 'summary' => 'A skin disease that causes red, itchy scaly patches, most commonly on the knees, elbows, trunk and scalp.'],
            ['slug' => 'tinea', 'title' => 'Tinea (Ringworm)', 'summary' => 'A common fungal infection of the skin. The name is a misnomer, as it is caused by a fungus, not a worm.'],
            ['slug' => 'urticaria', 'title' => 'Urticaria (Hives)', 'summary' => 'A skin rash triggered by a reaction to food, medicine, or other irritants. Also known as hives.'],
        ];

        foreach ($conditionsData as $data) {
            $condition = new DermCondition();
            $condition->setSlug($data['slug']);
            $condition->setTitle($data['title']);
            $condition->setSummary($data['summary']);
            $condition->setStatus(DermConditionStatus::PUBLISHED);
            // Opțional, poți seta și alte câmpuri (epidemiology, clinical, etc.) cu text mai detaliat
            $manager->persist($condition);
        }
    }
}
