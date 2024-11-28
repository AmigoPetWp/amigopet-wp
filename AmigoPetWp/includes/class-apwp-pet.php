<?php
/**
 * Classe para gerenciar os pets
 */
class APWP_Pet {
    private $table_name;
    
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'apwp_pets';
    }

    /**
     * Cria a tabela de pets no banco de dados
     */
    public static function create_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'apwp_pets';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            advertiser_id bigint(20) NOT NULL,
            organization_id bigint(20),
            name varchar(100) NOT NULL,
            species varchar(50) NOT NULL,
            breed varchar(50),
            age int,
            gender enum('male', 'female'),
            size enum('small', 'medium', 'large'),
            weight decimal(5,2),
            description text,
            status enum('available', 'adopted', 'pending', 'unavailable') DEFAULT 'available',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Adiciona um novo pet
     *
     * @param array $data Dados do pet
     * @return int|false ID do pet inserido ou false em caso de erro
     */
    public function add($data) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'apwp_pets';
        
        $result = $wpdb->insert(
            $table_name,
            array(
                'advertiser_id' => $data['advertiser_id'],
                'organization_id' => $data['organization_id'] ?? null,
                'name' => $data['name'],
                'species' => $data['species'],
                'breed' => $data['breed'] ?? '',
                'age' => $data['age'] ?? null,
                'gender' => $data['gender'] ?? '',
                'size' => $data['size'] ?? '',
                'weight' => $data['weight'] ?? null,
                'description' => $data['description'] ?? '',
                'status' => $data['status'] ?? 'available',
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ),
            array(
                '%d',
                '%d',
                '%s',
                '%s',
                '%s',
                '%d',
                '%s',
                '%s',
                '%f',
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
     * Lista todos os pets
     *
     * @param array $args Argumentos para filtrar
     * @return array Lista de pets
     */
    public function list($args = array()) {
        global $wpdb;
        
        $defaults = array(
            'status' => '',
            'species' => '',
            'organization_id' => 0,
            'advertiser_id' => 0,
            'limit' => 10,
            'offset' => 0,
            'orderby' => 'created_at',
            'order' => 'DESC'
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $where = array('1=1');
        $values = array();
        
        if (!empty($args['status'])) {
            $where[] = 'status = %s';
            $values[] = $args['status'];
        }
        
        if (!empty($args['species'])) {
            $where[] = 'species = %s';
            $values[] = $args['species'];
        }
        
        if (!empty($args['organization_id'])) {
            $where[] = 'organization_id = %d';
            $values[] = $args['organization_id'];
        }
        
        if (!empty($args['advertiser_id'])) {
            $where[] = 'advertiser_id = %d';
            $values[] = $args['advertiser_id'];
        }
        
        $sql = "SELECT * FROM {$this->table_name} WHERE " . implode(' AND ', $where);
        
        // Adiciona ordenação
        $sql .= sprintf(" ORDER BY %s %s",
            esc_sql($args['orderby']),
            esc_sql($args['order'])
        );
        
        // Adiciona limite
        $sql .= sprintf(" LIMIT %d OFFSET %d",
            (int) $args['limit'],
            (int) $args['offset']
        );
        
        if (!empty($values)) {
            $sql = $wpdb->prepare($sql, $values);
        }
        
        return $wpdb->get_results($sql);
    }

    /**
     * Obtém um pet pelo ID
     *
     * @param int $id ID do pet
     * @return object|false Objeto com os dados do pet ou false se não encontrado
     */
    public function get($id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'apwp_pets';
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d",
            $id
        ));
    }

    /**
     * Atualiza um pet
     *
     * @param int $id ID do pet
     * @param array $data Dados do pet
     * @return bool True se atualizado com sucesso, false caso contrário
     */
    public function update($id, $data) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'apwp_pets';
        
        $result = $wpdb->update(
            $table_name,
            array(
                'organization_id' => $data['organization_id'] ?? null,
                'name' => $data['name'],
                'species' => $data['species'],
                'breed' => $data['breed'] ?? '',
                'age' => $data['age'] ?? null,
                'gender' => $data['gender'] ?? '',
                'size' => $data['size'] ?? '',
                'weight' => $data['weight'] ?? null,
                'description' => $data['description'] ?? '',
                'status' => $data['status'] ?? 'available',
                'updated_at' => current_time('mysql')
            ),
            array('id' => $id),
            array(
                '%d',
                '%s',
                '%s',
                '%s',
                '%d',
                '%s',
                '%s',
                '%f',
                '%s',
                '%s',
                '%s'
            ),
            array('%d')
        );
        
        return $result !== false;
    }

    /**
     * Remove um pet
     *
     * @param int $id ID do pet
     * @return bool True se removido com sucesso, false caso contrário
     */
    public function delete($id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'apwp_pets';
        
        return $wpdb->delete(
            $table_name,
            array('id' => $id),
            array('%d')
        ) !== false;
    }

    /**
     * Atualiza o status de um pet
     *
     * @param int $id ID do pet
     * @param string $status Novo status
     * @return bool True se atualizado com sucesso, false caso contrário
     */
    public function update_status($id, $status) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'apwp_pets';
        
        $result = $wpdb->update(
            $table_name,
            array(
                'status' => $status,
                'updated_at' => current_time('mysql')
            ),
            array('id' => $id),
            array('%s', '%s'),
            array('%d')
        );
        
        return $result !== false;
    }

    /**
     * Obtém o anunciante de um pet
     *
     * @param int $id ID do pet
     * @return object|false Objeto com os dados do anunciante ou false se não encontrado
     */
    public function get_advertiser($id) {
        $pet = $this->get($id);
        if (!$pet) {
            return false;
        }

        $advertiser = new APWP_Advertiser();
        return $advertiser->get($pet->advertiser_id);
    }

    /**
     * Obtém a organização de um pet
     *
     * @param int $id ID do pet
     * @return object|false Objeto com os dados da organização ou false se não encontrado
     */
    public function get_organization($id) {
        $pet = $this->get($id);
        if (!$pet || !$pet->organization_id) {
            return false;
        }

        $organization = new APWP_Organization();
        return $organization->get($pet->organization_id);
    }

    /**
     * Obtém as fotos de um pet
     *
     * @param int $id ID do pet
     * @return array Lista de fotos
     */
    public function get_photos($id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'apwp_pet_photos';
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE pet_id = %d ORDER BY is_primary DESC, created_at DESC",
            $id
        ));
    }

    /**
     * Adiciona uma foto ao pet
     *
     * @param int $pet_id ID do pet
     * @param string $photo_url URL da foto
     * @param bool $is_primary Se é a foto principal
     * @return int|false ID da foto inserida ou false em caso de erro
     */
    public function add_photo($pet_id, $photo_url, $is_primary = false) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'apwp_pet_photos';
        
        // Se for foto principal, remove a marcação das outras fotos
        if ($is_primary) {
            $wpdb->update(
                $table_name,
                array('is_primary' => 0),
                array('pet_id' => $pet_id),
                array('%d'),
                array('%d')
            );
        }
        
        $result = $wpdb->insert(
            $table_name,
            array(
                'pet_id' => $pet_id,
                'photo_url' => $photo_url,
                'is_primary' => $is_primary ? 1 : 0,
                'created_at' => current_time('mysql')
            ),
            array('%d', '%s', '%d', '%s')
        );
        
        if ($result === false) {
            return false;
        }
        
        return $wpdb->insert_id;
    }

    /**
     * Remove uma foto do pet
     *
     * @param int $photo_id ID da foto
     * @return bool True se removida com sucesso, false caso contrário
     */
    public function remove_photo($photo_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'apwp_pet_photos';
        
        return $wpdb->delete(
            $table_name,
            array('id' => $photo_id),
            array('%d')
        ) !== false;
    }

    /**
     * Conta pets por status
     *
     * @param string $status Status para filtrar (opcional)
     * @return int Número de pets
     */
    public function count_by_status($status = '') {
        global $wpdb;
        
        $sql = "SELECT COUNT(*) FROM {$this->table_name}";
        if (!empty($status)) {
            $sql .= $wpdb->prepare(" WHERE status = %s", $status);
        }
        
        return (int) $wpdb->get_var($sql);
    }
}
