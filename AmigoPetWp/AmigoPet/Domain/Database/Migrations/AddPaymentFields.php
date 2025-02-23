<?php
namespace AmigoPetWp\Domain\Database\Migrations;

/**
 * Migração para adicionar novos campos à tabela de pagamentos
 */
class AddPaymentFields extends Migration {
    public function getVersion(): string {
        return '2.1.0';
    }

    public function getDescription(): string {
        return 'Adiciona novos campos à tabela de pagamentos de adoção';
    }

    public function up(): void {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        // Adicionando novos campos à tabela de pagamentos
        $sql = "ALTER TABLE {$this->prefix}adoption_payments
            ADD COLUMN payer_name VARCHAR(255) NULL AFTER transaction_id,
            ADD COLUMN payer_email VARCHAR(100) NULL AFTER payer_name,
            ADD COLUMN payer_document VARCHAR(20) NULL AFTER payer_email,
            ADD COLUMN payment_date TIMESTAMP NULL AFTER payer_document,
            ADD COLUMN refund_date TIMESTAMP NULL AFTER payment_date,
            ADD COLUMN refund_reason TEXT NULL AFTER refund_date,
            ADD COLUMN gateway_response JSON NULL AFTER refund_reason,
            ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at,
            MODIFY COLUMN payment_method ENUM('credit_card', 'pix', 'bank_transfer', 'cash', 'other') NOT NULL,
            MODIFY COLUMN payment_status ENUM('pending', 'processing', 'completed', 'failed', 'refunded', 'cancelled') DEFAULT 'pending',
            ADD INDEX idx_payment_method (payment_method),
            ADD INDEX idx_payment_status (payment_status),
            ADD INDEX idx_payment_date (payment_date),
            ADD INDEX idx_transaction_id (transaction_id)";

        $this->wpdb->query($sql);

        // Atualizando registros existentes
        $this->wpdb->query("
            UPDATE {$this->prefix}adoption_payments
            SET payment_status = 'completed'
            WHERE payment_status = 'paid'
        ");

        $this->wpdb->query("
            UPDATE {$this->prefix}adoption_payments
            SET payment_date = created_at
            WHERE payment_status = 'completed' AND payment_date IS NULL
        ");
    }

    public function down(): void {
        // Removendo índices
        $this->wpdb->query("
            ALTER TABLE {$this->prefix}adoption_payments
            DROP INDEX idx_payment_method,
            DROP INDEX idx_payment_status,
            DROP INDEX idx_payment_date,
            DROP INDEX idx_transaction_id
        ");

        // Removendo colunas
        $this->wpdb->query("
            ALTER TABLE {$this->prefix}adoption_payments
            DROP COLUMN payer_name,
            DROP COLUMN payer_email,
            DROP COLUMN payer_document,
            DROP COLUMN payment_date,
            DROP COLUMN refund_date,
            DROP COLUMN refund_reason,
            DROP COLUMN gateway_response,
            DROP COLUMN updated_at,
            MODIFY COLUMN payment_method VARCHAR(50) NOT NULL,
            MODIFY COLUMN payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending'
        ");

        // Revertendo status
        $this->wpdb->query("
            UPDATE {$this->prefix}adoption_payments
            SET payment_status = 'paid'
            WHERE payment_status = 'completed'
        ");
    }
}
