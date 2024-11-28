<?php
/**
 * Classe para gerenciar tipos de termos
 */
class APWP_Term_Type {
    private $id;
    private $name;
    private $slug;
    private $description;
    private $roles; // array de roles que podem assinar este tipo de termo
    private $status;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->set_data($data);
        }
    }

    private function set_data($data) {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * Lista todos os tipos de termos
     */
    public function list($args = array()) {
        global $wpdb;
        
        $defaults = array(
            'status' => 'active',
            'orderby' => 'name',
            'order' => 'ASC'
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $where = array('1=1');
        $values = array();
        
        if (!empty($args['status'])) {
            $where[] = 'status = %s';
            $values[] = $args['status'];
        }
        
        $sql = sprintf(
            "SELECT * FROM {$wpdb->prefix}apwp_term_types WHERE %s ORDER BY %s %s",
            implode(' AND ', $where),
            esc_sql($args['orderby']),
            esc_sql($args['order'])
        );
        
        if (!empty($values)) {
            $sql = $wpdb->prepare($sql, $values);
        }
        
        return $wpdb->get_results($sql, ARRAY_A);
    }

    /**
     * Obtém um tipo de termo pelo ID
     */
    public function get($id) {
        global $wpdb;
        
        $sql = $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}apwp_term_types WHERE id = %d",
            $id
        );
        
        $result = $wpdb->get_row($sql, ARRAY_A);
        
        if ($result) {
            $this->set_data($result);
            return $result;
        }
        
        return false;
    }

    /**
     * Salva o tipo de termo
     */
    public function save() {
        global $wpdb;
        
        $data = array(
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'roles' => maybe_serialize($this->roles),
            'status' => $this->status
        );
        
        $format = array('%s', '%s', '%s', '%s', '%s');
        
        if ($this->id) {
            $result = $wpdb->update(
                $wpdb->prefix . 'apwp_term_types',
                $data,
                array('id' => $this->id),
                $format,
                array('%d')
            );
            
            return $result !== false ? $this->id : false;
        } else {
            $result = $wpdb->insert(
                $wpdb->prefix . 'apwp_term_types',
                $data,
                $format
            );
            
            return $result ? $wpdb->insert_id : false;
        }
    }

    /**
     * Deleta um tipo de termo
     */
    public function delete($id = null) {
        global $wpdb;
        
        $id = $id ?? $this->id;
        
        if (!$id) {
            return false;
        }
        
        return $wpdb->delete(
            $wpdb->prefix . 'apwp_term_types',
            array('id' => $id),
            array('%d')
        );
    }

    /**
     * Verifica se um usuário pode assinar um tipo de termo
     */
    public function can_user_sign($user_id, $type_id = null) {
        $type_id = $type_id ?? $this->id;
        
        if (!$type_id) {
            return false;
        }
        
        $type = $this->get($type_id);
        if (!$type) {
            return false;
        }
        
        $user = get_userdata($user_id);
        if (!$user) {
            return false;
        }
        
        $allowed_roles = maybe_unserialize($type['roles']);
        
        return array_intersect($allowed_roles, $user->roles);
    }
}
