<?php

namespace App\Tests\Integration\Api;

use App\DataFixtures\AppFixtures;
use App\Entity\ClinicalCase;
use App\Entity\Enum\CaseStatus;
use App\Entity\User;
use App\Repository\ClinicalCaseRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ClinicalCaseApiTest extends WebTestCase
{
    protected AbstractDatabaseTool $databaseTool;
    protected $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->databaseTool->loadFixtures([AppFixtures::class]);
    }

    public function testGetCasesListIsProtectedForAnonymous(): void
    {
        $this->client->request('GET', '/api/cases');
        $this->assertResponseStatusCodeSame(401);
    }

    public function testUserCanViewPublishedCases(): void
    {
        $userRepository = $this->databaseTool->getRepo(User::class);
        $doctorUser = $userRepository->findOneByEmail('doctor@example.test');
        $this->client->loginUser($doctorUser);

        $this->client->request('GET', '/api/cases');
        $this->assertResponseIsSuccessful();
        $responseContent = $this->client->getResponse()->getContent();
        $this->assertJson($responseContent);
        $data = json_decode($responseContent, true);
        $this->assertCount(2, $data);
    }

    public function testAdminCanCreateCase(): void
    {
        $userRepository = $this->databaseTool->getRepo(User::class);
        $adminUser = $userRepository->findOneByEmail('admin@example.test');
        $this->client->loginUser($adminUser);

        $this->client->request(
            'POST',
            '/api/cases',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['name' => 'Angină pectorală', 'description' => 'Durere toracică.'])
        );

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonStringContainsString('Caz creat', $this->client->getResponse()->getContent());
    }

    public function testUserCannotCreateCase(): void
    {
        $userRepository = $this->databaseTool->getRepo(User::class);
        $doctorUser = $userRepository->findOneByEmail('doctor@example.test');
        $this->client->loginUser($doctorUser);

        $this->client->request(
            'POST',
            '/api/cases',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['name' => 'Caz neautorizat'])
        );

        $this->assertResponseStatusCodeSame(403);
    }

    public function testAdminCanUpdateCase(): void
    {
        $userRepository = $this->databaseTool->getRepo(User::class);
        $adminUser = $userRepository->findOneByEmail('admin@example.test');
        $this->client->loginUser($adminUser);

        $caseRepository = $this->getContainer()->get(ClinicalCaseRepository::class);
        $caseToUpdate = $caseRepository->findOneBy(['name' => 'Hipertensiune arterială esențială']);

        $this->client->request(
            'PUT',
            '/api/cases/' . $caseToUpdate->getId(),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['name' => 'Hipertensiune arterială - Nume Actualizat'])
        );

        $this->assertResponseIsSuccessful();
        $this->assertJsonStringContainsString('Nume Actualizat', $this->client->getResponse()->getContent());
    }

    public function testAdminCanSoftDeleteCase(): void
    {
        $userRepository = $this->databaseTool->getRepo(User::class);
        $adminUser = $userRepository->findOneByEmail('admin@example.test');
        $this->client->loginUser($adminUser);

        $caseRepository = $this->getContainer()->get(ClinicalCaseRepository::class);
        $caseToDelete = $caseRepository->findOneBy(['name' => 'Hipertensiune arterială esențială']);
        $caseId = $caseToDelete->getId();

        $this->client->request('DELETE', '/api/cases/' . $caseId);
        $this->assertResponseStatusCodeSame(204);

        // Verificăm că statusul s-a schimbat în "archived"
        $deletedCase = $caseRepository->find($caseId);
        $this->assertEquals(CaseStatus::ARCHIVED, $deletedCase->getStatus());

        // Verificăm că nu mai apare în lista publică
        $this->client->request('GET', '/api/cases');
        $responseContent = $this->client->getResponse()->getContent();
        $this->assertJson($responseContent);
        $this->assertStringNotContainsString($caseId, $responseContent);
    }
}
