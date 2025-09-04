<?php

namespace App\Tests\Unit\Entity;

use App\Entity\ClinicalCase;
use App\Entity\Enum\CaseStatus;
use PHPUnit\Framework\TestCase;

class ClinicalCaseTest extends TestCase
{
    public function testCanBeCreatedAndStatusDefaultsToDraft(): void
    {
        $case = new ClinicalCase();
        $this->assertInstanceOf(ClinicalCase::class, $case);
        $this->assertEquals(CaseStatus::DRAFT, $case->getStatus());
    }

    public function testAuditLogAppendsCorrectly(): void
    {
        $case = new ClinicalCase();
        $case->addAuditLogEntry('Prima intrare', 'user1@test.com');
        $this->assertStringContainsString('Prima intrare', $case->getAuditLog());
        $this->assertStringContainsString('user1@test.com', $case->getAuditLog());

        $case->addAuditLogEntry('A doua intrare', 'user2@test.com');
        $this->assertStringContainsString('Prima intrare', $case->getAuditLog());
        $this->assertStringContainsString('A doua intrare', $case->getAuditLog());
        $this->assertCount(2, explode(PHP_EOL, $case->getAuditLog()));
    }
}
