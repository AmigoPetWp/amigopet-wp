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
                'species_id' => $data['species_id'],
                'breed_id' => $data['breed_id'] ?? null,
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
                '%d',
                '%d',
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
            'species_id' => '',
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
        
        if (!empty($args['species_id'])) {
            $where[] = 'species_id = %d';
            $values[] = $args['species_id'];
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
                'species_id' => $data['species_id'],
                'breed_id' => $data['breed_id'] ?? null,
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
                '%d',
                '%d',
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

    /**
     * Lista todas as espécies cadastradas
     *
     * @return array Lista de espécies
     */
    public function get_species_list() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'apwp_pet_species';
        
        $cache_key = 'apwp_species_list';
        $species = wp_cache_get($cache_key);
        
        if ($species === false) {
            $species = $wpdb->get_results("SELECT * FROM $table_name ORDER BY name ASC");
            wp_cache_set($cache_key, $species, '', 3600); // Cache por 1 hora
        }
        
        return $species;
    }

    /**
     * Adiciona uma nova espécie
     *
     * @param string $name Nome da espécie
     * @param string $description Descrição da espécie
     * @return int|false ID da espécie inserida ou false em caso de erro
     */
    public function add_species($name, $description = '') {
        global $wpdb;
        $table_name = $wpdb->prefix . 'apwp_pet_species';
        
        $result = $wpdb->insert(
            $table_name,
            array(
                'name' => $name,
                'description' => $description,
                'created_at' => current_time('mysql')
            ),
            array('%s', '%s', '%s')
        );
        
        if ($result === false) {
            return false;
        }
        
        wp_cache_delete('apwp_species_list');
        return $wpdb->insert_id;
    }

    /**
     * Atualiza uma espécie
     *
     * @param int $id ID da espécie
     * @param array $data Dados da espécie
     * @return bool True se atualizado com sucesso
     */
    public function update_species($id, $data) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'apwp_pet_species';
        
        $result = $wpdb->update(
            $table_name,
            array(
                'name' => $data['name'],
                'description' => $data['description'] ?? ''
            ),
            array('id' => $id),
            array('%s', '%s'),
            array('%d')
        );
        
        if ($result !== false) {
            wp_cache_delete('apwp_species_list');
        }
        
        return $result !== false;
    }

    /**
     * Remove uma espécie
     *
     * @param int $id ID da espécie
     * @return bool True se removido com sucesso
     */
    public function delete_species($id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'apwp_pet_species';
        
        $result = $wpdb->delete(
            $table_name,
            array('id' => $id),
            array('%d')
        );
        
        if ($result !== false) {
            wp_cache_delete('apwp_species_list');
        }
        
        return $result !== false;
    }

    /**
     * Lista raças por espécie
     *
     * @param int $species_id ID da espécie
     * @return array Lista de raças
     */
    public function get_breeds_by_species($species_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'apwp_pet_breeds';
        
        $cache_key = 'apwp_breeds_species_' . $species_id;
        $breeds = wp_cache_get($cache_key);
        
        if ($breeds === false) {
            $breeds = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $table_name WHERE species_id = %d ORDER BY name ASC",
                $species_id
            ));
            wp_cache_set($cache_key, $breeds, '', 3600); // Cache por 1 hora
        }
        
        return $breeds;
    }

    /**
     * Adiciona uma nova raça
     *
     * @param int $species_id ID da espécie
     * @param string $name Nome da raça
     * @param string $description Descrição da raça
     * @return int|false ID da raça inserida ou false em caso de erro
     */
    public function add_breed($species_id, $name, $description = '') {
        global $wpdb;
        $table_name = $wpdb->prefix . 'apwp_pet_breeds';
        
        $result = $wpdb->insert(
            $table_name,
            array(
                'species_id' => $species_id,
                'name' => $name,
                'description' => $description,
                'created_at' => current_time('mysql')
            ),
            array('%d', '%s', '%s', '%s')
        );
        
        if ($result === false) {
            return false;
        }
        
        wp_cache_delete('apwp_breeds_species_' . $species_id);
        return $wpdb->insert_id;
    }

    /**
     * Atualiza uma raça
     *
     * @param int $id ID da raça
     * @param array $data Dados da raça
     * @return bool True se atualizado com sucesso
     */
    public function update_breed($id, $data) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'apwp_pet_breeds';
        
        // Obtém a raça atual para limpar o cache correto
        $current_breed = $wpdb->get_row($wpdb->prepare(
            "SELECT species_id FROM $table_name WHERE id = %d",
            $id
        ));
        
        $result = $wpdb->update(
            $table_name,
            array(
                'species_id' => $data['species_id'],
                'name' => $data['name'],
                'description' => $data['description'] ?? ''
            ),
            array('id' => $id),
            array('%d', '%s', '%s'),
            array('%d')
        );
        
        if ($result !== false) {
            // Limpa o cache da espécie antiga e nova se forem diferentes
            wp_cache_delete('apwp_breeds_species_' . $current_breed->species_id);
            if ($current_breed->species_id != $data['species_id']) {
                wp_cache_delete('apwp_breeds_species_' . $data['species_id']);
            }
        }
        
        return $result !== false;
    }

    /**
     * Remove uma raça
     *
     * @param int $id ID da raça
     * @return bool True se removido com sucesso
     */
    public function delete_breed($id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'apwp_pet_breeds';
        
        // Obtém a espécie da raça para limpar o cache
        $breed = $wpdb->get_row($wpdb->prepare(
            "SELECT species_id FROM $table_name WHERE id = %d",
            $id
        ));
        
        $result = $wpdb->delete(
            $table_name,
            array('id' => $id),
            array('%d')
        );
        
        if ($result !== false && $breed) {
            wp_cache_delete('apwp_breeds_species_' . $breed->species_id);
        }
        
        return $result !== false;
    }

    /**
     * Registra uma ação no log
     *
     * @param int $pet_id ID do pet
     * @param string $action Ação realizada
     * @param string $description Descrição da ação
     * @return bool True se registrado com sucesso
     */
    private function log_action($pet_id, $action, $description = '') {
        global $wpdb;
        $table_name = $wpdb->prefix . 'apwp_pet_logs';
        
        return $wpdb->insert(
            $table_name,
            array(
                'pet_id' => $pet_id,
                'user_id' => get_current_user_id(),
                'action' => $action,
                'description' => $description,
                'created_at' => current_time('mysql')
            ),
            array('%d', '%d', '%s', '%s', '%s')
        ) !== false;
    }

    /**
     * Obtém estatísticas dos pets
     *
     * @param array $filters Filtros opcionais
     * @return array Estatísticas
     */
    public function get_stats($filters = array()) {
        global $wpdb;
        $pets_table = $wpdb->prefix . 'apwp_pets';
        $species_table = $wpdb->prefix . 'apwp_pet_species';
        
        $where = array('1=1');
        $values = array();
        
        // Aplica filtros
        if (!empty($filters['organization_id'])) {
            $where[] = 'p.organization_id = %d';
            $values[] = $filters['organization_id'];
        }
        
        if (!empty($filters['advertiser_id'])) {
            $where[] = 'p.advertiser_id = %d';
            $values[] = $filters['advertiser_id'];
        }
        
        if (!empty($filters['start_date'])) {
            $where[] = 'p.created_at >= %s';
            $values[] = $filters['start_date'];
        }
        
        if (!empty($filters['end_date'])) {
            $where[] = 'p.created_at <= %s';
            $values[] = $filters['end_date'];
        }
        
        $where_clause = implode(' AND ', $where);
        
        // Total de pets
        $total = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $pets_table p WHERE $where_clause",
            $values
        ));
        
        // Total por status
        $status_sql = $wpdb->prepare(
            "SELECT status, COUNT(*) as total FROM $pets_table p 
            WHERE $where_clause 
            GROUP BY status",
            $values
        );
        $status_stats = $wpdb->get_results($status_sql, OBJECT_K);
        
        // Total por espécie
        $species_sql = $wpdb->prepare(
            "SELECT s.name, COUNT(*) as total 
            FROM $pets_table p 
            JOIN $species_table s ON p.species_id = s.id 
            WHERE $where_clause 
            GROUP BY s.name",
            $values
        );
        $species_stats = $wpdb->get_results($species_sql, OBJECT_K);
        
        // Total por gênero
        $gender_sql = $wpdb->prepare(
            "SELECT gender, COUNT(*) as total 
            FROM $pets_table p 
            WHERE $where_clause AND gender IS NOT NULL 
            GROUP BY gender",
            $values
        );
        $gender_stats = $wpdb->get_results($gender_sql, OBJECT_K);
        
        // Total por tamanho
        $size_sql = $wpdb->prepare(
            "SELECT size, COUNT(*) as total 
            FROM $pets_table p 
            WHERE $where_clause AND size IS NOT NULL 
            GROUP BY size",
            $values
        );
        $size_stats = $wpdb->get_results($size_sql, OBJECT_K);
        
        return array(
            'total' => (int) $total,
            'by_status' => $status_stats,
            'by_species' => $species_stats,
            'by_gender' => $gender_stats,
            'by_size' => $size_stats
        );
    }

    /**
     * Busca avançada de pets
     *
     * @param array $args Argumentos de busca
     * @return array Lista de pets
     */
    public function search($args = array()) {
        global $wpdb;
        $pets_table = $wpdb->prefix . 'apwp_pets';
        $species_table = $wpdb->prefix . 'apwp_pet_species';
        $breeds_table = $wpdb->prefix . 'apwp_pet_breeds';
        
        $defaults = array(
            'search' => '',
            'species_id' => '',
            'breed_id' => '',
            'status' => '',
            'gender' => '',
            'size' => '',
            'age_min' => '',
            'age_max' => '',
            'weight_min' => '',
            'weight_max' => '',
            'organization_id' => '',
            'advertiser_id' => '',
            'orderby' => 'created_at',
            'order' => 'DESC',
            'limit' => 10,
            'offset' => 0
        );
        
        $args = wp_parse_args($args, $defaults);
        $where = array('1=1');
        $values = array();
        
        // Busca por texto
        if (!empty($args['search'])) {
            $search = '%' . $wpdb->esc_like($args['search']) . '%';
            $where[] = '(
                p.name LIKE %s OR 
                p.description LIKE %s OR
                s.name LIKE %s OR
                b.name LIKE %s
            )';
            $values = array_merge($values, array($search, $search, $search, $search));
        }
        
        // Filtros exatos
        $exact_filters = array(
            'species_id' => 'p.species_id = %d',
            'breed_id' => 'p.breed_id = %d',
            'status' => 'p.status = %s',
            'gender' => 'p.gender = %s',
            'size' => 'p.size = %s',
            'organization_id' => 'p.organization_id = %d',
            'advertiser_id' => 'p.advertiser_id = %d'
        );
        
        foreach ($exact_filters as $field => $condition) {
            if (!empty($args[$field])) {
                $where[] = $condition;
                $values[] = $args[$field];
            }
        }
        
        // Filtros de intervalo
        $range_filters = array(
            'age' => array('min' => 'p.age >= %d', 'max' => 'p.age <= %d'),
            'weight' => array('min' => 'p.weight >= %f', 'max' => 'p.weight <= %f')
        );
        
        foreach ($range_filters as $field => $conditions) {
            if (!empty($args[$field . '_min'])) {
                $where[] = $conditions['min'];
                $values[] = $args[$field . '_min'];
            }
            if (!empty($args[$field . '_max'])) {
                $where[] = $conditions['max'];
                $values[] = $args[$field . '_max'];
            }
        }
        
        // Ordenação
        $allowed_orderby = array(
            'name' => 'p.name',
            'created_at' => 'p.created_at',
            'updated_at' => 'p.updated_at',
            'species' => 's.name',
            'breed' => 'b.name'
        );
        
        $orderby = isset($allowed_orderby[$args['orderby']]) ? $allowed_orderby[$args['orderby']] : 'p.created_at';
        $order = strtoupper($args['order']) === 'ASC' ? 'ASC' : 'DESC';
        
        // Monta a query
        $sql = $wpdb->prepare(
            "SELECT 
                p.*, 
                s.name as species_name, 
                b.name as breed_name
            FROM $pets_table p
            LEFT JOIN $species_table s ON p.species_id = s.id
            LEFT JOIN $breeds_table b ON p.breed_id = b.id
            WHERE " . implode(' AND ', $where) . "
            ORDER BY $orderby $order
            LIMIT %d OFFSET %d",
            array_merge(
                $values,
                array($args['limit'], $args['offset'])
            )
        );
        
        // Executa a query
        $results = $wpdb->get_results($sql);
        
        // Conta o total de resultados sem limit/offset
        $count_sql = $wpdb->prepare(
            "SELECT COUNT(*)
            FROM $pets_table p
            LEFT JOIN $species_table s ON p.species_id = s.id
            LEFT JOIN $breeds_table b ON p.breed_id = b.id
            WHERE " . implode(' AND ', $where),
            $values
        );
        
        $total = $wpdb->get_var($count_sql);
        
        return array(
            'items' => $results,
            'total' => (int) $total,
            'total_pages' => ceil($total / $args['limit'])
        );
    }

    /**
     * Valida os dados de um pet
     *
     * @param array $data Dados do pet
     * @return true|WP_Error True se válido, WP_Error caso contrário
     */
    public function validate_data($data) {
        $errors = new WP_Error();
        
        // Campos obrigatórios
        $required_fields = array(
            'advertiser_id' => __('Advertiser is required', 'amigopet-wp'),
            'name' => __('Name is required', 'amigopet-wp'),
            'species_id' => __('Species is required', 'amigopet-wp')
        );
        
        foreach ($required_fields as $field => $message) {
            if (empty($data[$field])) {
                $errors->add('required_' . $field, $message);
            }
        }
        
        // Validação de campos numéricos
        $numeric_fields = array(
            'advertiser_id', 'organization_id', 'species_id', 'breed_id', 'age'
        );
        
        foreach ($numeric_fields as $field) {
            if (!empty($data[$field]) && !is_numeric($data[$field])) {
                $errors->add(
                    'invalid_' . $field,
                    sprintf(__('%s must be a number', 'amigopet-wp'), ucfirst($field))
                );
            }
        }
        
        // Validação de campos enum
        if (!empty($data['gender']) && !in_array($data['gender'], array('male', 'female'))) {
            $errors->add('invalid_gender', __('Invalid gender', 'amigopet-wp'));
        }
        
        if (!empty($data['size']) && !in_array($data['size'], array('small', 'medium', 'large'))) {
            $errors->add('invalid_size', __('Invalid size', 'amigopet-wp'));
        }
        
        if (!empty($data['status']) && !in_array($data['status'], array('available', 'adopted', 'pending', 'unavailable'))) {
            $errors->add('invalid_status', __('Invalid status', 'amigopet-wp'));
        }
        
        // Validação de peso
        if (!empty($data['weight'])) {
            if (!is_numeric($data['weight']) || $data['weight'] <= 0) {
                $errors->add('invalid_weight', __('Weight must be a positive number', 'amigopet-wp'));
            }
        }
        
        // Validação de existência de espécie e raça
        if (!empty($data['species_id'])) {
            $species_exists = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}apwp_pet_species WHERE id = %d",
                $data['species_id']
            ));
            
            if (!$species_exists) {
                $errors->add('invalid_species', __('Species does not exist', 'amigopet-wp'));
            }
        }
        
        if (!empty($data['breed_id'])) {
            $breed_exists = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}apwp_pet_breeds WHERE id = %d AND species_id = %d",
                $data['breed_id'],
                $data['species_id']
            ));
            
            if (!$breed_exists) {
                $errors->add('invalid_breed', __('Breed does not exist or does not belong to the selected species', 'amigopet-wp'));
            }
        }
        
        if ($errors->has_errors()) {
            return $errors;
        }
        
        return true;
    }

    /**
     * Atualiza múltiplos pets de uma vez
     *
     * @param array $ids IDs dos pets
     * @param array $data Dados a serem atualizados
     * @return int Número de pets atualizados
     */
    public function bulk_update($ids, $data) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'apwp_pets';
        
        if (empty($ids) || empty($data)) {
            return 0;
        }
        
        // Sanitiza os IDs
        $ids = array_map('intval', $ids);
        $placeholders = implode(',', array_fill(0, count($ids), '%d'));
        
        // Prepara os campos e valores para atualização
        $set = array();
        $values = array();
        
        $allowed_fields = array(
            'organization_id', 'status', 'species_id', 'breed_id',
            'age', 'gender', 'size', 'weight'
        );
        
        foreach ($data as $field => $value) {
            if (in_array($field, $allowed_fields)) {
                $set[] = "$field = %s";
                $values[] = $value;
            }
        }
        
        if (empty($set)) {
            return 0;
        }
        
        // Adiciona os IDs aos valores
        $values = array_merge($values, $ids);
        
        // Constrói e executa a query
        $sql = $wpdb->prepare(
            "UPDATE $table_name SET " . implode(', ', $set) . 
            " WHERE id IN ($placeholders)",
            $values
        );
        
        $result = $wpdb->query($sql);
        
        if ($result !== false) {
            // Registra a ação no log para cada pet
            foreach ($ids as $pet_id) {
                $this->log_action(
                    $pet_id,
                    'bulk_update',
                    sprintf(
                        __('Bulk update with data: %s', 'amigopet-wp'),
                        json_encode($data)
                    )
                );
            }
        }
        
        return $result;
    }

    /**
     * Registra os endpoints da API REST
     */
    public function register_rest_routes() {
        register_rest_route('amigopet-wp/v1', '/pets', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'get_pets'),
                'permission_callback' => array($this, 'get_items_permissions_check'),
            ),
            array(
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => array($this, 'create_pet'),
                'permission_callback' => array($this, 'create_item_permissions_check'),
            ),
        ));

        register_rest_route('amigopet-wp/v1', '/pets/(?P<id>\d+)', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'get_pet'),
                'permission_callback' => array($this, 'get_items_permissions_check'),
            ),
            array(
                'methods' => WP_REST_Server::EDITABLE,
                'callback' => array($this, 'update_pet'),
                'permission_callback' => array($this, 'update_item_permissions_check'),
            ),
            array(
                'methods' => WP_REST_Server::DELETABLE,
                'callback' => array($this, 'delete_pet'),
                'permission_callback' => array($this, 'delete_item_permissions_check'),
            ),
        ));

        register_rest_route('amigopet-wp/v1', '/species', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'get_species'),
                'permission_callback' => array($this, 'get_items_permissions_check'),
            ),
            array(
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => array($this, 'create_species'),
                'permission_callback' => array($this, 'create_item_permissions_check'),
            ),
        ));

        register_rest_route('amigopet-wp/v1', '/species/(?P<id>\d+)', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'get_species_item'),
                'permission_callback' => array($this, 'get_items_permissions_check'),
            ),
            array(
                'methods' => WP_REST_Server::EDITABLE,
                'callback' => array($this, 'update_species'),
                'permission_callback' => array($this, 'update_item_permissions_check'),
            ),
            array(
                'methods' => WP_REST_Server::DELETABLE,
                'callback' => array($this, 'delete_species'),
                'permission_callback' => array($this, 'delete_item_permissions_check'),
            ),
        ));

        register_rest_route('amigopet-wp/v1', '/breeds', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'get_breeds'),
                'permission_callback' => array($this, 'get_items_permissions_check'),
            ),
            array(
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => array($this, 'create_breed'),
                'permission_callback' => array($this, 'create_item_permissions_check'),
            ),
        ));

        register_rest_route('amigopet-wp/v1', '/breeds/(?P<id>\d+)', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'get_breed'),
                'permission_callback' => array($this, 'get_items_permissions_check'),
            ),
            array(
                'methods' => WP_REST_Server::EDITABLE,
                'callback' => array($this, 'update_breed_endpoint'),
                'permission_callback' => array($this, 'update_item_permissions_check'),
            ),
            array(
                'methods' => WP_REST_Server::DELETABLE,
                'callback' => array($this, 'delete_breed_endpoint'),
                'permission_callback' => array($this, 'delete_item_permissions_check'),
            ),
        ));
    }

    /**
     * Verifica permissões para leitura
     */
    public function get_items_permissions_check($request) {
        return current_user_can('read');
    }

    /**
     * Verifica permissões para criação
     */
    public function create_item_permissions_check($request) {
        return current_user_can('manage_options');
    }

    /**
     * Verifica permissões para atualização
     */
    public function update_item_permissions_check($request) {
        return current_user_can('manage_options');
    }

    /**
     * Verifica permissões para exclusão
     */
    public function delete_item_permissions_check($request) {
        return current_user_can('manage_options');
    }

    /**
     * Retorna lista de pets
     */
    public function get_pets($request) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'apwp_pets';
        
        $page = isset($request['page']) ? (int) $request['page'] : 1;
        $per_page = isset($request['per_page']) ? (int) $request['per_page'] : 10;
        $offset = ($page - 1) * $per_page;
        
        $where = "WHERE 1=1";
        $params = array();
        
        if (isset($request['species_id'])) {
            $where .= " AND species_id = %d";
            $params[] = $request['species_id'];
        }
        
        if (isset($request['breed_id'])) {
            $where .= " AND breed_id = %d";
            $params[] = $request['breed_id'];
        }
        
        if (isset($request['status'])) {
            $where .= " AND status = %s";
            $params[] = $request['status'];
        }
        
        $query = $wpdb->prepare(
            "SELECT * FROM $table_name $where ORDER BY created_at DESC LIMIT %d OFFSET %d",
            array_merge($params, array($per_page, $offset))
        );
        
        $items = $wpdb->get_results($query);
        $total = $wpdb->get_var("SELECT COUNT(*) FROM $table_name $where");
        
        $response = rest_ensure_response($items);
        $response->header('X-WP-Total', (int) $total);
        $response->header('X-WP-TotalPages', ceil($total / $per_page));
        
        return $response;
    }

    /**
     * Retorna lista de espécies
     */
    public function get_species($request) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'apwp_pet_species';
        
        $items = $wpdb->get_results(
            "SELECT * FROM $table_name ORDER BY name ASC"
        );
        
        return rest_ensure_response($items);
    }

    /**
     * Retorna lista de raças
     */
    public function get_breeds($request) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'apwp_pet_breeds';
        $species_table = $wpdb->prefix . 'apwp_pet_species';
        
        $where = "WHERE 1=1";
        $params = array();
        
        if (isset($request['species_id'])) {
            $where .= " AND b.species_id = %d";
            $params[] = $request['species_id'];
        }
        
        $query = $wpdb->prepare(
            "SELECT b.*, s.name as species_name 
             FROM $table_name b 
             LEFT JOIN $species_table s ON b.species_id = s.id 
             $where 
             ORDER BY b.name ASC",
            $params
        );
        
        $items = $wpdb->get_results($query);
        
        return rest_ensure_response($items);
    }

    /**
     * Retorna uma raça específica
     */
    public function get_breed($request) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'apwp_pet_breeds';
        
        $breed = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE id = %d",
                $request['id']
            )
        );
        
        if (!$breed) {
            return new WP_Error(
                'not_found',
                'Raça não encontrada',
                array('status' => 404)
            );
        }
        
        return rest_ensure_response($breed);
    }

    /**
     * Cria uma nova raça
     */
    public function create_breed($request) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'apwp_pet_breeds';
        
        $result = $wpdb->insert(
            $table_name,
            array(
                'name' => $request['name'],
                'species_id' => $request['species_id'],
                'description' => $request['description'],
            ),
            array('%s', '%d', '%s')
        );
        
        if (!$result) {
            return new WP_Error(
                'db_error',
                'Erro ao criar raça',
                array('status' => 500)
            );
        }
        
        $breed = $this->get_breed(array('id' => $wpdb->insert_id));
        
        return rest_ensure_response($breed);
    }

    /**
     * Atualiza uma raça via API REST
     */
    public function update_breed_endpoint($request) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'apwp_pet_breeds';
        
        $result = $wpdb->update(
            $table_name,
            array(
                'name' => $request['name'],
                'species_id' => $request['species_id'],
                'description' => $request['description'],
            ),
            array('id' => $request['id']),
            array('%s', '%d', '%s'),
            array('%d')
        );
        
        if ($result === false) {
            return new WP_Error(
                'db_error',
                'Erro ao atualizar raça',
                array('status' => 500)
            );
        }
        
        $breed = $this->get_breed($request);
        
        return rest_ensure_response($breed);
    }

    /**
     * Exclui uma raça via API REST
     */
    public function delete_breed_endpoint($request) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'apwp_pet_breeds';
        
        $result = $wpdb->delete(
            $table_name,
            array('id' => $request['id']),
            array('%d')
        );
        
        if ($result === false) {
            return new WP_Error(
                'db_error',
                'Erro ao excluir raça',
                array('status' => 500)
            );
        }
        
        return rest_ensure_response(array(
            'deleted' => true,
            'id' => $request['id']
        ));
    }
}
