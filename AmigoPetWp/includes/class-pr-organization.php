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
     * ID da organização no banco de dados.
     *
     * @since    1.0.0
     * @access   private
     * @var      integer    $id    ID da organização.
     */
    private $id;

    /**
     * ID do usuário WordPress associado.
     *
     * @since    1.0.0
     * @access   private
     * @var      integer    $user_id    ID do usuário WordPress.
     */
    private $user_id;

    /**
     * Nome da organização.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $name    Nome da organização.
     */
    private $name;

    /**
     * Construtor.
     *
     * @since    1.0.0
     */
    public function __construct() {
        // Inicialização
    }

    /**
     * Salva uma nova organização no banco de dados.
     *
     * @since    1.0.0
     * @param    array    $data    Dados da organização.
     * @return   integer|WP_Error  ID da organização inserida ou objeto de erro.
     */
    public function save($data) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'apwp_organizations';
        
        $result = $wpdb->insert(
            $table_name,
            array(
                'user_id' => absint($data['user_id']),
                'name' => sanitize_text_field($data['name']),
                'email' => sanitize_email($data['email']),
                'phone' => sanitize_text_field($data['phone']),
                'address' => sanitize_textarea_field($data['address']),
                'description' => sanitize_textarea_field($data['description']),
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ),
            array('%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
        );

        if ($result === false) {
            return new WP_Error('db_insert_error', 'Não foi possível salvar a organização.');
        }

        return $wpdb->insert_id;
    }

    /**
     * Atualiza uma organização existente.
     *
     * @since    1.0.0
     * @param    integer    $id      ID da organização.
     * @param    array      $data    Dados da organização.
     * @return   boolean|WP_Error    True em caso de sucesso ou objeto de erro.
     */
    public function update($id, $data) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'apwp_organizations';
        
        $result = $wpdb->update(
            $table_name,
            array(
                'name' => sanitize_text_field($data['name']),
                'email' => sanitize_email($data['email']),
                'phone' => sanitize_text_field($data['phone']),
                'address' => sanitize_textarea_field($data['address']),
                'description' => sanitize_textarea_field($data['description']),
                'updated_at' => current_time('mysql')
            ),
            array('id' => $id),
            array('%s', '%s', '%s', '%s', '%s', '%s'),
            array('%d')
        );

        if ($result === false) {
            return new WP_Error('db_update_error', 'Não foi possível atualizar a organização.');
        }

        return true;
    }

    /**
     * Obtém uma organização pelo ID.
     *
     * @since    1.0.0
     * @param    integer    $id    ID da organização.
     * @return   object|null       Dados da organização ou null se não encontrada.
     */
    public function get($id) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'apwp_organizations';
        
        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE id = %d",
                $id
            )
        );
    }

    /**
     * Obtém uma organização pelo ID do usuário WordPress.
     *
     * @since    1.0.0
     * @param    integer    $user_id    ID do usuário WordPress.
     * @return   object|null            Dados da organização ou null se não encontrada.
     */
    public function get_by_user_id($user_id) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'apwp_organizations';
        
        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE user_id = %d",
                $user_id
            )
        );
    }

    /**
     * Lista todas as organizações.
     *
     * @since    1.0.0
     * @return   array    Lista de organizações.
     */
    public function list() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'apwp_organizations';
        
        return $wpdb->get_results(
            "SELECT * FROM $table_name ORDER BY name ASC"
        );
    }

    /**
     * Remove uma organização.
     *
     * @since    1.0.0
     * @param    integer    $id    ID da organização.
     * @return   boolean           True em caso de sucesso.
     */
    public function delete($id) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'apwp_organizations';
        
        return $wpdb->delete(
            $table_name,
            array('id' => $id),
            array('%d')
        );
    }

    /**
     * Verifica se o usuário atual pertence a alguma organização.
     *
     * @since    1.0.0
     * @return   boolean    True se o usuário pertence a uma organização.
     */
    public function current_user_belongs_to_organization() {
        $current_user_id = get_current_user_id();
        return (bool) $this->get_by_user_id($current_user_id);
    }
}
