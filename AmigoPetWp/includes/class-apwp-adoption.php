<?php
/**
 * Classe para gerenciar as adoções
 */
class APWP_Adoption {
    private $table_name;
    
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'apwp_adoptions';
    }

    /**
     * Lista todas as adoções
     *
     * @param array $args Argumentos para filtrar
     * @return array Lista de adoções
     */
    public function list($args = array()) {
        global $wpdb;
        
        $defaults = array(
            'status' => '',
            'pet_id' => 0,
            'adopter_id' => 0,
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
        
        if (!empty($args['pet_id'])) {
            $where[] = 'pet_id = %d';
            $values[] = $args['pet_id'];
        }
        
        if (!empty($args['adopter_id'])) {
            $where[] = 'adopter_id = %d';
            $values[] = $args['adopter_id'];
        }
        
        $sql = "SELECT a.*, 
                       p.name as pet_name,
                       ad.name as adopter_name
                FROM {$this->table_name} a
                LEFT JOIN {$wpdb->prefix}apwp_pets p ON a.pet_id = p.id
                LEFT JOIN {$wpdb->prefix}apwp_adopters ad ON a.adopter_id = ad.id
                WHERE " . implode(' AND ', $where);
        
        // Adiciona ordenação
        $sql .= sprintf(" ORDER BY a.%s %s",
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
     * Adiciona uma nova adoção
     *
     * @param array $data Dados da adoção
     * @return int|false ID da adoção inserida ou false em caso de erro
     */
    public function add($data) {
        global $wpdb;
        
        // Verifica se o pet está disponível
        $pet = new APWP_Pet();
        $pet_data = $pet->get($data['pet_id']);
        if (!$pet_data || $pet_data->status !== 'available') {
            return false;
        }
        
        // Insere a adoção
        $result = $wpdb->insert(
            $this->table_name,
            array(
                'pet_id' => $data['pet_id'],
                'adopter_id' => $data['adopter_id'],
                'status' => $data['status'],
                'notes' => $data['notes']
            ),
            array(
                '%d',
                '%d',
                '%s',
                '%s'
            )
        );
        
        if ($result) {
            // Se a adoção for aprovada, atualiza o status do pet
            if ($data['status'] === 'approved') {
                $pet->update_status($data['pet_id'], 'adopted');
            }
            return $wpdb->insert_id;
        }
        
        return false;
    }

    /**
     * Atualiza uma adoção
     *
     * @param int $id ID da adoção
     * @param array $data Dados da adoção
     * @return bool True se atualizado com sucesso, false caso contrário
     */
    public function update($id, $data) {
        global $wpdb;
        
        // Obtém o status atual
        $current = $this->get($id);
        if (!$current) {
            return false;
        }
        
        // Atualiza a adoção
        $result = $wpdb->update(
            $this->table_name,
            array(
                'status' => $data['status'],
                'notes' => $data['notes']
            ),
            array('id' => $id),
            array(
                '%s',
                '%s'
            ),
            array('%d')
        );
        
        if ($result) {
            $pet = new APWP_Pet();
            
            // Se o status mudou para aprovado
            if ($data['status'] === 'approved' && $current->status !== 'approved') {
                $pet->update_status($current->pet_id, 'adopted');
            }
            // Se o status mudou de aprovado para outro
            elseif ($data['status'] !== 'approved' && $current->status === 'approved') {
                $pet->update_status($current->pet_id, 'available');
            }
            
            return true;
        }
        
        return false;
    }

    /**
     * Obtém uma adoção pelo ID
     *
     * @param int $id ID da adoção
     * @return object|null Dados da adoção ou null se não encontrada
     */
    public function get($id) {
        global $wpdb;
        
        $sql = $wpdb->prepare(
            "SELECT a.*, 
                    p.name as pet_name,
                    ad.name as adopter_name
             FROM {$this->table_name} a
             LEFT JOIN {$wpdb->prefix}apwp_pets p ON a.pet_id = p.id
             LEFT JOIN {$wpdb->prefix}apwp_adopters ad ON a.adopter_id = ad.id
             WHERE a.id = %d",
            $id
        );
        
        return $wpdb->get_row($sql);
    }

    /**
     * Exclui uma adoção
     *
     * @param int $id ID da adoção
     * @return bool True se excluído com sucesso, false caso contrário
     */
    public function delete($id) {
        global $wpdb;
        
        // Obtém a adoção atual
        $current = $this->get($id);
        if (!$current) {
            return false;
        }
        
        // Se a adoção estava aprovada, volta o status do pet para disponível
        if ($current->status === 'approved') {
            $pet = new APWP_Pet();
            $pet->update_status($current->pet_id, 'available');
        }
        
        return $wpdb->delete(
            $this->table_name,
            array('id' => $id),
            array('%d')
        ) !== false;
    }

    /**
     * Conta adoções por status
     *
     * @param string $status Status para filtrar (opcional)
     * @return int Número de adoções
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
