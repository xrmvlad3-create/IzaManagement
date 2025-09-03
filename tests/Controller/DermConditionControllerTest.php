<?php

namespace App\Tests\Controller;

use App\DataFixtures\AppFixtures;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DermConditionControllerTest extends WebTestCase
{
    /** @var AbstractDatabaseTool */
    protected $databaseTool;
    private $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();

        // Se încarcă un set de date de test (fixtures) într-o bază de date de test separată
        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->databaseTool->loadFixtures([
            AppFixtures::class
        ]);
    }

    /**
     * Testează dacă un utilizator neautentificat (anonim) primește eroarea 401 (Unauthorized)
     * când încearcă să acceseze lista de afecțiuni.
     */
    public function testGetDermConditionsAsAnonymousUserIsDenied(): void
    {
        $this->client->request('GET', '/api/derm/conditions');
        $this->assertResponseStatusCodeSame(401); // Se așteaptă statusul "Neautorizat"
    }

    /**
     * Testează dacă un utilizator autentificat poate accesa lista de afecțiuni
     * și dacă răspunsul este formatat corect ca JSON.
     */
    public function testGetDermConditionsAsAuthenticatedUser(): void
    {
        // Se autentifică utilizatorul "doctor@example.test" creat de fixtures
        $user = $this->databaseTool->getRepo('App\Entity\User')->findOneByEmail('doctor@example.test');
        $this->client->loginUser($user);

        $this->client->request('GET', '/api/derm/conditions');
        $this->assertResponseIsSuccessful();

        $response = $this->client->getResponse();
        $this->assertJson($response->getContent());

        $data = json_decode($response->getContent(), true);

        // Verifică dacă răspunsul este un array și nu este gol
        $this->assertIsArray($data);
        $this->assertNotEmpty($data);

        // Verifică dacă primul element din listă are cheile așteptate
        $firstCondition = $data[0];
        $this->assertArrayHasKey('id', $firstCondition);
        $this->assertArrayHasKey('slug', $firstCondition);
        $this->assertArrayHasKey('title', $firstCondition);
        $this->assertArrayHasKey('status', $firstCondition);
    }
}
