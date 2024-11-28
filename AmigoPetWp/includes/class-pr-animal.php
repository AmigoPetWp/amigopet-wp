<?php

/**
 * Classe responsável pelo gerenciamento de animais.
 *
 * @since      1.0.0
 * @package    AmigoPetWp
 * @subpackage AmigoPetWp/includes
 */
class APWP_Animal {

    /**
     * ID do animal no banco de dados.
     *
     * @since    1.0.0
     * @access   private
     * @var      integer    $id    ID do animal.
     */
    private $id;

    /**
     * Nome do animal.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $name    Nome do animal.
     */
    private $name;

    /**
     * Espécie do animal (cão, gato, etc.).
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $species    Espécie do animal.
     */
    private $species;

    /**
     * Idade aproximada do animal.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $age    Idade do animal.
     */
    private $age;

    /**
     * Status do animal (disponível, adotado, etc.).
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $status    Status do animal.
     */
    private $status;

    /**
     * Construtor.
     *
     * @since    1.0.0
     */
    public function __construct() {
        // Inicialização
    }

    /**
     * Salva um novo animal no banco de dados.
     *
     * @since    1.0.0
     * @param    array    $data    Dados do animal.
     * @return   integer|WP_Error  ID do animal inserido ou objeto de erro.
     */
    public function save($data) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'apwp_animals';
        
        $result = $wpdb->insert(
            $table_name,
            array(
                'name' => sanitize_text_field($data['name']),
                'species' => sanitize_text_field($data['species']),
                'age' => sanitize_text_field($data['age']),
                'status' => sanitize_text_field($data['status']),
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ),
            array('%s', '%s', '%s', '%s', '%s', '%s')
        );

        if ($result === false) {
            return new WP_Error('db_insert_error', 'Não foi possível salvar o animal.');
        }

        return $wpdb->insert_id;
    }

    /**
     * Atualiza um animal existente.
     *
     * @since    1.0.0
     * @param    integer    $id      ID do animal.
     * @param    array      $data    Dados do animal.
     * @return   boolean|WP_Error    True em caso de sucesso ou objeto de erro.
     */
    public function update($id, $data) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'apwp_animals';
        
        $result = $wpdb->update(
            $table_name,
            array(
                'name' => sanitize_text_field($data['name']),
                'species' => sanitize_text_field($data['species']),
                'age' => sanitize_text_field($data['age']),
                'status' => sanitize_text_field($data['status']),
                'updated_at' => current_time('mysql')
            ),
            array('id' => $id),
            array('%s', '%s', '%s', '%s', '%s'),
            array('%d')
        );

        if ($result === false) {
            return new WP_Error('db_update_error', 'Não foi possível atualizar o animal.');
        }

        return true;
    }

    /**
     * Obtém um animal pelo ID.
     *
     * @since    1.0.0
     * @param    integer    $id    ID do animal.
     * @return   object|null       Dados do animal ou null se não encontrado.
     */
    public function get($id) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'apwp_animals';
        
        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE id = %d",
                $id
            )
        );
    }

    /**
     * Lista todos os animais.
     *
     * @since    1.0.0
     * @param    array     $args    Argumentos de filtragem.
     * @return   array              Lista de animais.
     */
    public function list($args = array()) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'apwp_animals';
        
        $query = "SELECT * FROM $table_name";
        
        if (!empty($args['status'])) {
            $query .= $wpdb->prepare(" WHERE status = %s", $args['status']);
        }
        
        $query .= " ORDER BY created_at DESC";
        
        return $wpdb->get_results($query);
    }

    /**
     * Remove um animal.
     *
     * @since    1.0.0
     * @param    integer    $id    ID do animal.
     * @return   boolean           True em caso de sucesso.
     */
    public function delete($id) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'apwp_animals';
        
        return $wpdb->delete(
            $table_name,
            array('id' => $id),
            array('%d')
        );
    }
}
