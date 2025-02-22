<?php
namespace AmigoPetWp\Domain\Database\Migrations;

class SeedTermTypes {
    private $wpdb;
    private $table_name;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table_name = $wpdb->prefix . 'amigopet_term_types';
    }

    public function up(): void {
        $default_types = [
            [
                'name' => 'Termo de Adoção',
                'slug' => 'adoption',
                'description' => 'Termo de responsabilidade para adoção de pets'
            ],
            [
                'name' => 'Termo de Voluntariado',
                'slug' => 'volunteer',
                'description' => 'Termo de compromisso para voluntários'
            ],
            [
                'name' => 'Termo de Doação',
                'slug' => 'donation',
                'description' => 'Termo para registro de doações'
            ]
        ];

        foreach ($default_types as $type) {
            $exists = $this->wpdb->get_var(
                $this->wpdb->prepare(
                    "SELECT COUNT(*) FROM {$this->table_name} WHERE slug = %s",
                    $type['slug']
                )
            );

            if (!$exists) {
                $this->wpdb->insert(
                    $this->table_name,
                    [
                        'name' => $type['name'],
                        'slug' => $type['slug'],
                        'description' => $type['description'],
                        'status' => 'active'
                    ],
                    ['%s', '%s', '%s', '%s']
                );
            }
        }
    }
}
