<?php declare(strict_types=1);
namespace AmigoPetWp\Tests\Domain\Api;

if (!defined('ABSPATH')) {
    exit;
}

use AmigoPetWp\Domain\Api\PaymentGateway;
use PHPUnit\Framework\TestCase;

class PaymentGatewayTest extends TestCase
{
    private $gateway;

    protected function setUp(): void
    {
        $this->gateway = new PaymentGateway('test_api_key', 'test_secret_key', true);
    }

    public function testValidatePaymentDataWithMissingFields(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Campo obrigatório não informado: amount');

        $this->gateway->processPayment([]);
    }

    public function testValidatePaymentDataWithInvalidAmount(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Valor deve ser maior que zero');

        $paymentData = [
            'amount' => 0,
            'description' => 'Test',
            'payment_method' => 'pix',
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'customer_document' => '12345678900'
        ];

        $this->gateway->processPayment($paymentData);
    }

    public function testGetApiUrlInSandbox(): void
    {
        $reflection = new \ReflectionClass(PaymentGateway::class);
        $method = $reflection->getMethod('getApiUrl');
        $method->setAccessible(true);

        $this->assertEquals('https://api.sandbox.gateway.com/v1', $method->invoke($this->gateway));
    }

    public function testGetApiUrlInProduction(): void
    {
        $productionGateway = new PaymentGateway('key', 'secret', false);
        $reflection = new \ReflectionClass(PaymentGateway::class);
        $method = $reflection->getMethod('getApiUrl');
        $method->setAccessible(true);

        $this->assertEquals('https://api.gateway.com/v1', $method->invoke($productionGateway));
    }
}