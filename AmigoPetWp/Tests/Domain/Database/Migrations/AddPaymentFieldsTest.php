<?php
namespace AmigoPetWp\Tests\Domain\Database\Migrations;

use AmigoPetWp\Domain\Database\Migrations\AddPaymentFields;
use AmigoPetWp\Domain\Entities\AdoptionPayment;
use PHPUnit\Framework\TestCase;

class AddPaymentFieldsTest extends TestCase {
    private $wpdb;
    private $migration;
    private $prefix;

    protected function setUp(): void {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->prefix = $wpdb->prefix;
        $this->migration = new AddPaymentFields();
    }

    public function testUpAddsNewColumns(): void {
        // Executa a migração
        $this->migration->up();

        // Verifica se as colunas foram adicionadas
        $columns = $this->wpdb->get_results("SHOW COLUMNS FROM {$this->prefix}adoption_payments");
        $columnNames = array_map(function($col) { return $col->Field; }, $columns);

        // Verifica novas colunas
        $this->assertContains('payer_name', $columnNames);
        $this->assertContains('payer_email', $columnNames);
        $this->assertContains('payer_document', $columnNames);
        $this->assertContains('payment_date', $columnNames);
        $this->assertContains('refund_date', $columnNames);
        $this->assertContains('refund_reason', $columnNames);
        $this->assertContains('gateway_response', $columnNames);
        $this->assertContains('updated_at', $columnNames);

        // Verifica modificações nos campos existentes
        $paymentMethodColumn = array_filter($columns, function($col) {
            return $col->Field === 'payment_method';
        })[0];
        $this->assertStringContainsString("'credit_card','pix','bank_transfer','cash','other'", $paymentMethodColumn->Type);

        $paymentStatusColumn = array_filter($columns, function($col) {
            return $col->Field === 'payment_status';
        })[0];
        $this->assertStringContainsString("'pending','processing','completed','failed','refunded','cancelled'", $paymentStatusColumn->Type);

        // Verifica índices
        $indexes = $this->wpdb->get_results("SHOW INDEX FROM {$this->prefix}adoption_payments");
        $indexNames = array_map(function($idx) { return $idx->Key_name; }, $indexes);

        $this->assertContains('idx_payment_method', $indexNames);
        $this->assertContains('idx_payment_status', $indexNames);
        $this->assertContains('idx_payment_date', $indexNames);
        $this->assertContains('idx_transaction_id', $indexNames);
    }

    public function testDownRemovesNewColumns(): void {
        // Primeiro executa up para garantir que as colunas existem
        $this->migration->up();

        // Executa down
        $this->migration->down();

        // Verifica se as colunas foram removidas
        $columns = $this->wpdb->get_results("SHOW COLUMNS FROM {$this->prefix}adoption_payments");
        $columnNames = array_map(function($col) { return $col->Field; }, $columns);

        // Verifica que as novas colunas foram removidas
        $this->assertNotContains('payer_name', $columnNames);
        $this->assertNotContains('payer_email', $columnNames);
        $this->assertNotContains('payer_document', $columnNames);
        $this->assertNotContains('payment_date', $columnNames);
        $this->assertNotContains('refund_date', $columnNames);
        $this->assertNotContains('refund_reason', $columnNames);
        $this->assertNotContains('gateway_response', $columnNames);
        $this->assertNotContains('updated_at', $columnNames);

        // Verifica que os campos voltaram ao estado original
        $paymentMethodColumn = array_filter($columns, function($col) {
            return $col->Field === 'payment_method';
        })[0];
        $this->assertEquals('varchar(50)', strtolower($paymentMethodColumn->Type));

        $paymentStatusColumn = array_filter($columns, function($col) {
            return $col->Field === 'payment_status';
        })[0];
        $this->assertStringContainsString("'pending','paid','failed','refunded'", $paymentStatusColumn->Type);

        // Verifica que os índices foram removidos
        $indexes = $this->wpdb->get_results("SHOW INDEX FROM {$this->prefix}adoption_payments");
        $indexNames = array_map(function($idx) { return $idx->Key_name; }, $indexes);

        $this->assertNotContains('idx_payment_method', $indexNames);
        $this->assertNotContains('idx_payment_status', $indexNames);
        $this->assertNotContains('idx_payment_date', $indexNames);
        $this->assertNotContains('idx_transaction_id', $indexNames);
    }

    public function testMigrationVersion(): void {
        $this->assertEquals('2.1.0', $this->migration->getVersion());
    }

    public function testMigrationDescription(): void {
        $this->assertNotEmpty($this->migration->getDescription());
    }

    protected function tearDown(): void {
        // Limpa qualquer alteração feita no banco
        $this->migration->down();
    }
}
