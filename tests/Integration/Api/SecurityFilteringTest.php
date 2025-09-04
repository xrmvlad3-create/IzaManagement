<?php

namespace App\Tests\Integration\Api;

use App\DataFixtures\AppFixtures;
use App\Entity\User;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityFilteringTest extends WebTestCase
{
    protected AbstractDatabaseTool $databaseTool;
    protected $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->databaseTool->loadFixtures([AppFixtures::class]);
    }

    public function testDermatologistSeesOnlyDermatologyProcedures(): void
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->databaseTool->getRepo(User::class);
        $dermatologist = $userRepository->findOneByEmail('dermatolog@example.com');

        $this->client->loginUser($dermatologist);
        $this->client->request('GET', '/api/procedures');

        $this->assertResponseIsSuccessful();
        $responseContent = $this->client->getResponse()->getContent();

        $this->assertStringContainsString('Terapia PRP', $responseContent, 'Procedura de dermatologie ar trebui să fie vizibilă.');
        $this->assertStringNotContainsString('Incizie artroscopică', $responseContent, 'Procedura de ortopedie NU ar trebui să fie vizibilă.');
    }

    public function testAdminSeesAllProcedures(): void
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->databaseTool->getRepo(User::class);
        $admin = $userRepository->findOneByEmail('admin@example.com');

        $this->client->loginUser($admin);
        $this->client->request('GET', '/api/procedures');

        $this->assertResponseIsSuccessful();
        $responseContent = $this->client->getResponse()->getContent();

        $this->assertStringContainsString('Terapia PRP', $responseContent, 'Adminul ar trebui să vadă procedura de dermatologie.');
        $this->assertStringContainsString('Incizie artroscopică', $responseContent, 'Adminul ar trebui să vadă procedura de ortopedie.');
    }
}
