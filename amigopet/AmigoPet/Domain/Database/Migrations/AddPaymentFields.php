<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Database\Migrations;

if (!defined('ABSPATH')) {
    exit;
}

use AmigoPetWp\Domain\Database\Migration;

/**
 * Migração para adicionar novos campos à tabela de pagamentos
 */
class AddPaymentFields extends Migration
{
    public function getVersion(): string
    {
        return '2.1.0';
    }

    public function getDescription(): string
    {
        return 'Adiciona novos campos à tabela de pagamentos de adoção';
    }

    public function up(): void
    {
        return;
    }

    public function down(): void
    {
        return;
    }
}