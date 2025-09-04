<?php

namespace App\Tests\Integration\Repository;

use App\Entity\ClinicalCase;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ClinicalCaseRepositoryTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
    }

    public function testCaseCanBeSavedAndRetrieved(): void
    {
        // 1. Creăm un utilizator de test
        $user = new User();
        $user->setEmail('test.user@example.com');
        $user->setPassword('password'); // Într-un test real, parola ar trebui hash-uită
        $this->entityManager->persist($user);

        // 2. Creăm un caz clinic nou
        $case = new ClinicalCase();
        $case->setName('Dermatită atopică severă');
        $case->setCreatedBy($user);
        $case->setAuditLog('Caz creat de utilizatorul de test.');

        $this->entityManager->persist($case);
        $this->entityManager->flush();
        $this->entityManager->clear(); // Curățăm entity manager-ul pentru a asigura o citire proaspătă din DB

        // 3. Regăsim cazul din repository
        $caseRepository = $this->entityManager->getRepository(ClinicalCase::class);
        $savedCase = $caseRepository->findOneBy(['name' => 'Dermatită atopică severă']);

        // 4. Verificăm datele
        $this->assertNotNull($savedCase);
        $this->assertEquals('Dermatită atopică severă', $savedCase->getName());
        $this->assertNotNull($savedCase->getCreatedBy());
        $this->assertEquals('test.user@example.com', $savedCase->getCreatedBy()->getEmail());
        $this->assertStringContainsString('Caz creat', $savedCase->getAuditLog());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
