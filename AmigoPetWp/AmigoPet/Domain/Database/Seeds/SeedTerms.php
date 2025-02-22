<?php
namespace AmigoPetWp\Domain\Database\Migrations;

class SeedTerms {
    private $wpdb;
    private $terms_table;
    private $types_table;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->terms_table = $wpdb->prefix . 'amigopet_terms';
        $this->types_table = $wpdb->prefix . 'amigopet_term_types';
    }

    public function up(): void {
        // Pega o ID do tipo de termo de adoção
        $adoption_type_id = $this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT id FROM {$this->types_table} WHERE slug = %s",
                'adoption'
            )
        );

        if (!$adoption_type_id) {
            return; // Não pode continuar sem o tipo de termo
        }

        $default_terms = [
            [
                'title' => 'Termo de Adoção Responsável',
                'content' => $this->getDefaultAdoptionTermContent(),
                'type_id' => $adoption_type_id,
                'version' => '1.0'
            ]
        ];

        foreach ($default_terms as $term) {
            $exists = $this->wpdb->get_var(
                $this->wpdb->prepare(
                    "SELECT COUNT(*) FROM {$this->terms_table} WHERE title = %s AND version = %s",
                    $term['title'],
                    $term['version']
                )
            );

            if (!$exists) {
                $this->wpdb->insert(
                    $this->terms_table,
                    [
                        'title' => $term['title'],
                        'content' => $term['content'],
                        'type_id' => $term['type_id'],
                        'version' => $term['version'],
                        'status' => 'active'
                    ],
                    ['%s', '%s', '%d', '%s', '%s']
                );
            }
        }
    }

    private function getDefaultAdoptionTermContent(): string {
        return '
TERMO DE ADOÇÃO RESPONSÁVEL

Pelo presente instrumento, denominado Termo de Adoção Responsável, o(a) ADOTANTE abaixo qualificado(a) declara expressamente que aceita a guarda e responsabilidade do animal descrito neste termo, comprometendo-se a:

1. CUIDADOS BÁSICOS
   - Fornecer alimentação adequada e água fresca diariamente
   - Manter o ambiente limpo e adequado para o animal
   - Providenciar atendimento veterinário quando necessário
   - Manter a vacinação e vermifugação em dia
   - Garantir espaço adequado para exercício e socialização

2. COMPROMISSOS
   - Não abandonar o animal sob nenhuma circunstância
   - Comunicar a organização em caso de impossibilidade de manter o animal
   - Permitir visitas de acompanhamento pós-adoção
   - Não realizar procedimentos cirúrgicos desnecessários
   - Castrar o animal na idade recomendada pelo veterinário

3. RESPONSABILIDADES
   - Responder legalmente pelo animal adotado
   - Arcar com todas as despesas de manutenção
   - Garantir que o animal não cause danos a terceiros
   - Manter o animal em ambiente seguro
   - Identificar o animal com plaqueta e/ou microchip

4. PENALIDADES
   O não cumprimento das condições estabelecidas neste termo poderá implicar:
   - Perda da guarda do animal
   - Multas previstas na legislação de proteção animal
   - Responsabilização civil e criminal quando aplicável

O presente termo é firmado em caráter irrevogável e irretratável.

[DADOS DO ANIMAL]
Nome: {pet_name}
Espécie: {pet_species}
Raça: {pet_breed}
Idade aproximada: {pet_age}
Características: {pet_description}

[DADOS DO ADOTANTE]
Nome completo: {adopter_name}
CPF: {adopter_cpf}
Endereço: {adopter_address}
Telefone: {adopter_phone}
Email: {adopter_email}

[DADOS DA ORGANIZAÇÃO]
Nome: {org_name}
CNPJ: {org_cnpj}
Endereço: {org_address}
Responsável: {org_responsible}

Local e data: _________________________

Assinaturas:

_______________________
Adotante

_______________________
Organização
        ';
    }
}
