<?php

/**
 * Classe para gerenciar anunciantes
 *
 * @link       https://github.com/AmigoPetWp/amigopet-wp
 * @since      1.0.0
 *
 * @package    AmigoPet_Wp
 * @subpackage AmigoPet_Wp/includes
 */

/**
 * Classe para gerenciar anunciantes
 *
 * @package    AmigoPet_Wp
 * @subpackage AmigoPet_Wp/includes
 * @author     Jackson Sá <jacksonwendel@gmail.com>
 */
class APWP_Advertiser {

    /**
     * ID do anunciante
     *
     * @var int
     */
    private $id;

    /**
     * ID do usuário WordPress
     *
     * @var int
     */
    private $wp_user_id;

    /**
     * Nome do anunciante
     *
     * @var string
     */
    private $name;

    /**
     * Email do anunciante
     *
     * @var string
     */
    private $email;

    /**
     * Telefone do anunciante
     *
     * @var string
     */
    private $phone;

    /**
     * Endereço do anunciante
     *
     * @var string
     */
    private $address;

    /**
     * Cidade do anunciante
     *
     * @var string
     */
    private $city;

    /**
     * Estado do anunciante
     *
     * @var string
     */
    private $state;

    /**
     * CEP do anunciante
     *
     * @var string
     */
    private $zip;

    /**
     * Construtor
     */
    public function __construct() {
    }

    /**
     * Obtém um anunciante pelo ID
     *
     * @param int $id ID do anunciante
     * @return object|false Objeto com os dados do anunciante ou false se não encontrado
     */
    public function get($id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'apwp_advertisers';
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d",
            $id
        ));
    }

    /**
     * Obtém um anunciante pelo ID do usuário WordPress
     *
     * @param int $wp_user_id ID do usuário WordPress
     * @return object|false Objeto com os dados do anunciante ou false se não encontrado
     */
    public function get_by_wp_user($wp_user_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'apwp_advertisers';
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE wp_user_id = %d",
            $wp_user_id
        ));
    }

    /**
     * Lista todos os anunciantes
     *
     * @param array $args Argumentos para filtrar a listagem
     * @return array Lista de anunciantes
     */
    public function list($args = array()) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'apwp_advertisers';
        
        $defaults = array(
            'orderby' => 'name',
            'order' => 'ASC',
            'limit' => 10,
            'offset' => 0,
            'search' => '',
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $where = array('1=1');
        $values = array();
        
        if (!empty($args['search'])) {
            $where[] = '(name LIKE %s OR email LIKE %s)';
            $values[] = '%' . $wpdb->esc_like($args['search']) . '%';
            $values[] = '%' . $wpdb->esc_like($args['search']) . '%';
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
     * Adiciona um novo anunciante
     *
     * @param array $data Dados do anunciante
     * @return int|false ID do anunciante inserido ou false em caso de erro
     */
    public function add($data) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'apwp_advertisers';
        
        $result = $wpdb->insert(
            $table_name,
            array(
                'wp_user_id' => $data['wp_user_id'],
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? '',
                'address' => $data['address'] ?? '',
                'city' => $data['city'] ?? '',
                'state' => $data['state'] ?? '',
                'zip' => $data['zip'] ?? '',
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
                '%s'
            )
        );
        
        if ($result === false) {
            return false;
        }
        
        return $wpdb->insert_id;
    }

    /**
     * Atualiza um anunciante
     *
     * @param int $id ID do anunciante
     * @param array $data Dados do anunciante
     * @return bool True se atualizado com sucesso, false caso contrário
     */
    public function update($id, $data) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'apwp_advertisers';
        
        $result = $wpdb->update(
            $table_name,
            array(
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? '',
                'address' => $data['address'] ?? '',
                'city' => $data['city'] ?? '',
                'state' => $data['state'] ?? '',
                'zip' => $data['zip'] ?? '',
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
                '%s'
            ),
            array('%d')
        );
        
        return $result !== false;
    }

    /**
     * Remove um anunciante
     *
     * @param int $id ID do anunciante
     * @return bool True se removido com sucesso, false caso contrário
     */
    public function delete($id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'apwp_advertisers';
        
        return $wpdb->delete(
            $table_name,
            array('id' => $id),
            array('%d')
        ) !== false;
    }

    /**
     * Conta o total de anunciantes
     *
     * @param array $args Argumentos para filtrar a contagem
     * @return int Total de anunciantes
     */
    public function count($args = array()) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'apwp_advertisers';
        
        $where = array('1=1');
        $values = array();
        
        if (!empty($args['search'])) {
            $where[] = '(name LIKE %s OR email LIKE %s)';
            $values[] = '%' . $wpdb->esc_like($args['search']) . '%';
            $values[] = '%' . $wpdb->esc_like($args['search']) . '%';
        }
        
        $sql = "SELECT COUNT(*) FROM $table_name WHERE " . implode(' AND ', $where);
        
        if (!empty($values)) {
            $sql = $wpdb->prepare($sql, $values);
        }
        
        return (int) $wpdb->get_var($sql);
    }

    /**
     * Lista os pets de um anunciante
     *
     * @param int $advertiser_id ID do anunciante
     * @return array Lista de pets
     */
    public function get_pets($advertiser_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'apwp_pets';
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE advertiser_id = %d ORDER BY created_at DESC",
            $advertiser_id
        ));
    }

    /**
     * Lista as adoções de um anunciante
     *
     * @param int $advertiser_id ID do anunciante
     * @return array Lista de adoções
     */
    public function get_adoptions($advertiser_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'apwp_adoptions';
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE advertiser_id = %d ORDER BY created_at DESC",
            $advertiser_id
        ));
    }
}
