<?php
namespace AmigoPetWp\Domain\Database\Migrations;

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
        $table = $this->prefix . 'terms';
        $typeTable = $this->prefix . 'term_types';
        
        // Primeiro obtém os IDs dos tipos de termos
        $adoptionTypeId = $this->wpdb->get_var("SELECT id FROM {$typeTable} WHERE name = 'Termo de Adoção'");
        $volunteerTypeId = $this->wpdb->get_var("SELECT id FROM {$typeTable} WHERE name = 'Termo de Voluntariado'");

        if (!$adoptionTypeId || !$volunteerTypeId) {
            return; // Se não encontrar os tipos, não insere os termos
        }

        // Termo de adoção padrão
        $adoptionTerm = [
            'type_id' => $adoptionTypeId,
            'title' => 'Termo de Responsabilidade para Adoção',
            'content' => 'Eu, [NOME_COMPLETO], portador do CPF [CPF], declaro estar ciente das responsabilidades ao adotar um animal de estimação...',
            'version' => '1.0',
            'status' => 'active',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];

        // Termo de voluntariado padrão
        $volunteerTerm = [
            'type_id' => $volunteerTypeId,
            'title' => 'Termo de Voluntariado',
            'content' => 'Eu, [NOME_COMPLETO], portador do CPF [CPF], me comprometo a exercer trabalho voluntário...',
            'version' => '1.0',
            'status' => 'active',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];

        $this->wpdb->insert($table, $adoptionTerm);
        $this->wpdb->insert($table, $volunteerTerm);
    }

    public function down(): void {
        $table = $this->prefix . 'terms';
        $this->wpdb->query("TRUNCATE TABLE {$table}");
    }
}
