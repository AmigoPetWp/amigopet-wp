<?php

/**
 * Classe para gerenciar raças de pets
 */
class APWP_Breed {
    private $table_name;
    
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'apwp_pet_breeds';
    }

    /**
     * Adiciona uma nova raça
     *
     * @param string $name Nome da raça
     * @param int $species_id ID da espécie
     * @param string $description Descrição da raça
     * @return int|false ID da raça inserida ou false em caso de erro
     */
    public function add($name, $species_id, $description = '') {
        global $wpdb;

        $result = $wpdb->insert(
            $this->table_name,
            array(
                'name' => $name,
                'species_id' => $species_id,
                'description' => $description,
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ),
            array('%s', '%d', '%s', '%s', '%s')
        );

        if ($result === false) {
            return false;
        }

        return $wpdb->insert_id;
    }

    /**
     * Lista todas as raças
     *
     * @param array $args Argumentos para filtrar
     * @return array Lista de raças
     */
    public function list($args = array()) {
        global $wpdb;
        
        $defaults = array(
            'species_id' => 0,
            'orderby' => 'name',
            'order' => 'ASC',
            'limit' => 0,
            'offset' => 0,
            'search' => '',
        );

        $args = wp_parse_args($args, $defaults);
        
        $sql = "SELECT b.*, s.name as species_name 
                FROM {$this->table_name} b
                LEFT JOIN {$wpdb->prefix}apwp_pet_species s ON b.species_id = s.id";
        
        $where = array();
        
        if ($args['species_id'] > 0) {
            $where[] = $wpdb->prepare("b.species_id = %d", $args['species_id']);
        }
        
        if (!empty($args['search'])) {
            $search = '%' . $wpdb->esc_like($args['search']) . '%';
            $where[] = $wpdb->prepare("b.name LIKE %s", $search);
        }
        
        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        
        $sql .= " ORDER BY b.{$args['orderby']} {$args['order']}";
        
        if ($args['limit'] > 0) {
            $sql .= $wpdb->prepare(" LIMIT %d OFFSET %d", $args['limit'], $args['offset']);
        }
        
        return $wpdb->get_results($sql);
    }

    /**
     * Atualiza uma raça
     *
     * @param int $id ID da raça
     * @param array $data Dados para atualizar
     * @return bool True em caso de sucesso, false em caso de erro
     */
    public function update($id, $data) {
        global $wpdb;

        $data['updated_at'] = current_time('mysql');

        return $wpdb->update(
            $this->table_name,
            $data,
            array('id' => $id),
            array('%s', '%d', '%s', '%s'),
            array('%d')
        ) !== false;
    }

    /**
     * Exclui uma raça
     *
     * @param int $id ID da raça
     * @return bool True em caso de sucesso, false em caso de erro
     */
    public function delete($id) {
        global $wpdb;

        return $wpdb->delete(
            $this->table_name,
            array('id' => $id),
            array('%d')
        ) !== false;
    }

    /**
     * Busca uma raça pelo ID
     *
     * @param int $id ID da raça
     * @return object|null Dados da raça ou null se não encontrada
     */
    public function get($id) {
        global $wpdb;

        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT b.*, s.name as species_name 
                FROM {$this->table_name} b
                LEFT JOIN {$wpdb->prefix}apwp_pet_species s ON b.species_id = s.id
                WHERE b.id = %d",
                $id
            )
        );
    }
}
