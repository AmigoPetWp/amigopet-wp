<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Database\Migrations;

if (!defined('ABSPATH')) {
    exit;
}

use AmigoPetWp\Domain\Database\Migration;

class SeedTerms extends Migration {
    public function __construct() {
        parent::__construct();
        $this->prefix = $this->wpdb->prefix . 'apwp_';
    }

    public function getDescription(): string {
        return 'Insere os termos padrão';
    }

    public function getVersion(): string {
        return '1.0.4';
    }

    public function up(): void {
        $wpdb = $this->wpdb;
        $table = preg_replace('/[^a-zA-Z0-9_]/', '', $this->prefix . 'terms');
        $typeTable = preg_replace('/[^a-zA-Z0-9_]/', '', $this->prefix . 'term_types');
        if (!is_string($table) || $table === '' || !is_string($typeTable) || $typeTable === '') {
            return;
        }
        
        $adoptionTypeId = $wpdb->get_var(
            $wpdb->prepare(
                'SELECT id FROM %i WHERE name = %s',
                $typeTable,
                'Termo de Adoção'
            )
        );
        $volunteerTypeId = $wpdb->get_var(
            $wpdb->prepare(
                'SELECT id FROM %i WHERE name = %s',
                $typeTable,
                'Termo de Voluntariado'
            )
        );

        if (!$adoptionTypeId || !$volunteerTypeId) {
            return;
        }

        $adoptionTerm = [
            'type_id' => $adoptionTypeId,
            'title' => 'Termo de Responsabilidade para Adoção',
            'content' => 'Eu, [NOME_COMPLETO], portador do CPF [CPF], declaro estar ciente das responsabilidades ao adotar um animal de estimação...',
            'version' => '1.0',
            'status' => 'active',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];

        $volunteerTerm = [
            'type_id' => $volunteerTypeId,
            'title' => 'Termo de Voluntariado',
            'content' => 'Eu, [NOME_COMPLETO], portador do CPF [CPF], me comprometo a exercer trabalho voluntário...',
            'version' => '1.0',
            'status' => 'active',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];

        $this->wpdb->replace($table, $adoptionTerm);
        $this->wpdb->replace($table, $volunteerTerm);
    }

    public function down(): void {
        $wpdb = $this->wpdb;
        $table = preg_replace('/[^a-zA-Z0-9_]/', '', $this->prefix . 'terms');
        if (!is_string($table) || $table === '') {
            return;
        }
        $wpdb->query($wpdb->prepare('DELETE FROM %i', $table));
    }
}