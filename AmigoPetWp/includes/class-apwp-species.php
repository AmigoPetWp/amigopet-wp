<?php

/**
 * Classe para gerenciar espécies de pets
 */
class APWP_Species {
    private $table_name;
    
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'apwp_pet_species';
    }

    /**
     * Adiciona uma nova espécie
     *
     * @param string $name Nome da espécie
     * @param string $description Descrição da espécie
     * @return int|false ID da espécie inserida ou false em caso de erro
     */
    public function add($name, $description = '') {
        global $wpdb;

        $result = $wpdb->insert(
            $this->table_name,
            array(
                'name' => $name,
                'description' => $description,
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ),
            array('%s', '%s', '%s', '%s')
        );

        if ($result === false) {
            return false;
        }

        return $wpdb->insert_id;
    }

    /**
     * Lista todas as espécies
     *
     * @param array $args Argumentos para filtrar
     * @return array Lista de espécies
     */
    public function list($args = array()) {
        global $wpdb;
        
        $defaults = array(
            'orderby' => 'name',
            'order' => 'ASC',
            'limit' => 0,
            'offset' => 0,
            'search' => '',
        );

        $args = wp_parse_args($args, $defaults);
        
        $sql = "SELECT * FROM {$this->table_name}";
        $where = array();
        
        if (!empty($args['search'])) {
            $search = '%' . $wpdb->esc_like($args['search']) . '%';
            $where[] = $wpdb->prepare("name LIKE %s", $search);
        }
        
        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        
        $sql .= " ORDER BY {$args['orderby']} {$args['order']}";
        
        if ($args['limit'] > 0) {
            $sql .= $wpdb->prepare(" LIMIT %d OFFSET %d", $args['limit'], $args['offset']);
        }
        
        return $wpdb->get_results($sql);
    }

    /**
     * Atualiza uma espécie
     *
     * @param int $id ID da espécie
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
            array('%s', '%s', '%s'),
            array('%d')
        ) !== false;
    }

    /**
     * Exclui uma espécie
     *
     * @param int $id ID da espécie
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
     * Busca uma espécie pelo ID
     *
     * @param int $id ID da espécie
     * @return object|null Dados da espécie ou null se não encontrada
     */
    public function get($id) {
        global $wpdb;

        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$this->table_name} WHERE id = %d",
                $id
            )
        );
    }
}
