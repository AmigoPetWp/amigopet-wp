<?php

/**
 * Classe responsável pelo gerenciamento de organizações.
 *
 * @since      1.0.0
 * @package    AmigoPetWp
 * @subpackage AmigoPetWp/includes
 */
class APWP_Organization {

    /**
     * Construtor.
     *
     * @since    1.0.0
     */
    public function __construct() {
        // Inicialização
    }

    /**
     * Lista todas as organizações
     *
     * @param array $args Argumentos para filtrar a listagem
     * @return array Lista de organizações
     */
    public function list($args = array()) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'apwp_organizations';
        
        $defaults = array(
            'orderby' => 'name',
            'order' => 'ASC',
            'limit' => 10,
            'offset' => 0,
            'search' => '',
            'city' => null,
            'state' => null
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $where = array('1=1');
        $values = array();
        
        if (!empty($args['search'])) {
            $where[] = '(name LIKE %s OR email LIKE %s)';
            $values[] = '%' . $wpdb->esc_like($args['search']) . '%';
            $values[] = '%' . $wpdb->esc_like($args['search']) . '%';
        }
        
        if (!empty($args['city'])) {
            $where[] = 'city = %s';
            $values[] = $args['city'];
        }
        
        if (!empty($args['state'])) {
            $where[] = 'state = %s';
            $values[] = $args['state'];
        }
        
        $sql = "SELECT * FROM $table_name WHERE " . implode(' AND ', $where);
        $sql .= " ORDER BY {$args['orderby']} {$args['order']}";
        $sql .= " LIMIT %d OFFSET %d";
        
        $values[] = $args['limit'];
        $values[] = $args['offset'];
        
        if (!empty($values)) {
            $sql = $wpdb->prepare($sql, $values);
        }
        
        return $wpdb->get_results($sql);
    }

    /**
     * Obtém uma organização pelo ID
     *
     * @param int $id ID da organização
     * @return object|false Objeto com os dados da organização ou false se não encontrada
     */
    public function get($id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'apwp_organizations';
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d",
            $id
        ));
    }

    /**
     * Adiciona uma nova organização
     *
     * @param array $data Dados da organização
     * @return int|false ID da organização inserida ou false em caso de erro
     */
    public function add($data) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'apwp_organizations';
        
        $result = $wpdb->insert(
            $table_name,
            array(
                'user_id' => $data['user_id'] ?? get_current_user_id(),
                'name' => $data['name'],
                'cnpj' => $data['cnpj'] ?? '',
                'email' => $data['email'],
                'phone' => $data['phone'] ?? '',
                'address' => $data['address'] ?? '',
                'city' => $data['city'] ?? '',
                'state' => $data['state'] ?? '',
                'zip' => $data['zip'] ?? '',
                'logo_url' => $data['logo_url'] ?? '',
                'description' => $data['description'] ?? '',
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ),
            array(
                '%d',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s'
            )
        );
        
        if ($result === false) {
            return false;
        }
        
        return $wpdb->insert_id;
    }

    /**
     * Atualiza uma organização
     *
     * @param int $id ID da organização
     * @param array $data Dados da organização
     * @return bool True se atualizada com sucesso, false caso contrário
     */
    public function update($id, $data) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'apwp_organizations';
        
        $result = $wpdb->update(
            $table_name,
            array(
                'name' => $data['name'],
                'cnpj' => $data['cnpj'] ?? '',
                'email' => $data['email'],
                'phone' => $data['phone'] ?? '',
                'address' => $data['address'] ?? '',
                'city' => $data['city'] ?? '',
                'state' => $data['state'] ?? '',
                'zip' => $data['zip'] ?? '',
                'logo_url' => $data['logo_url'] ?? '',
                'description' => $data['description'] ?? '',
                'updated_at' => current_time('mysql')
            ),
            array('id' => $id),
            array(
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s'
            ),
            array('%d')
        );
        
        return $result !== false;
    }

    /**
     * Remove uma organização
     *
     * @param int $id ID da organização
     * @return bool True se removida com sucesso, false caso contrário
     */
    public function delete($id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'apwp_organizations';
        
        return $wpdb->delete(
            $table_name,
            array('id' => $id),
            array('%d')
        ) !== false;
    }

    /**
     * Lista os pets de uma organização
     *
     * @param int $organization_id ID da organização
     * @return array Lista de pets
     */
    public function get_pets($organization_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'apwp_pets';
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE organization_id = %d ORDER BY created_at DESC",
            $organization_id
        ));
    }

    /**
     * Lista as adoções de uma organização
     *
     * @param int $organization_id ID da organização
     * @return array Lista de adoções
     */
    public function get_adoptions($organization_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'apwp_adoptions';
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE organization_id = %d ORDER BY created_at DESC",
            $organization_id
        ));
    }

    /**
     * Conta o total de organizações com base nos filtros
     *
     * @param array $args Argumentos para filtrar a contagem
     * @return int Total de organizações
     */
    public function count($args = array()) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'apwp_organizations';
        
        $where = array('1=1');
        $values = array();
        
        if (!empty($args['search'])) {
            $where[] = '(name LIKE %s OR email LIKE %s)';
            $values[] = '%' . $wpdb->esc_like($args['search']) . '%';
            $values[] = '%' . $wpdb->esc_like($args['search']) . '%';
        }
        
        if (!empty($args['city'])) {
            $where[] = 'city = %s';
            $values[] = $args['city'];
        }
        
        $sql = "SELECT COUNT(*) FROM $table_name WHERE " . implode(' AND ', $where);
        
        if (!empty($values)) {
            $sql = $wpdb->prepare($sql, $values);
        }
        
        return (int) $wpdb->get_var($sql);
    }
}
