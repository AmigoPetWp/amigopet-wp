<?php declare(strict_types=1);
namespace AmigoPetWp\Tests\Domain\Api;

if (!defined('ABSPATH')) {
    exit;
}

use AmigoPetWp\Domain\Api\PetFinderIntegration;
use PHPUnit\Framework\TestCase;

class PetFinderIntegrationTest extends TestCase
{
    public function testConstructorSetsApiKey(): void
    {
        $integration = new PetFinderIntegration('test_key');
        $reflection = new \ReflectionClass(PetFinderIntegration::class);
        $property = $reflection->getProperty('apiKey');
        $property->setAccessible(true);

        $this->assertEquals('test_key', $property->getValue($integration));
    }

    public function testConstructorWithSettings(): void
    {
        // This test would ideally mock Settings::getAll(), 
        // but for now we're just verifying the structure.
        $integration = new PetFinderIntegration();
        $this->assertInstanceOf(PetFinderIntegration::class, $integration);
    }
}