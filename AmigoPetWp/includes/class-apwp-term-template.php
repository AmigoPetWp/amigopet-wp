<?php
/**
 * Classe para gerenciar templates de termos
 */
class APWP_Term_Template {
    private $id;
    private $type_id;
    private $title;
    private $content;
    private $status;
    private $created_at;
    private $updated_at;

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
     * Lista todos os templates
     */
    public function list($args = array()) {
        global $wpdb;
        
        $defaults = array(
            'type_id' => 0,
            'status' => 'active',
            'orderby' => 'title',
            'order' => 'ASC'
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $where = array('1=1');
        $values = array();
        
        if (!empty($args['type_id'])) {
            $where[] = 'type_id = %d';
            $values[] = $args['type_id'];
        }
        
        if (!empty($args['status'])) {
            $where[] = 'status = %s';
            $values[] = $args['status'];
        }
        
        $sql = sprintf(
            "SELECT * FROM {$wpdb->prefix}apwp_term_templates WHERE %s ORDER BY %s %s",
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
     * Obtém um template pelo ID
     */
    public function get($id) {
        global $wpdb;
        
        $sql = $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}apwp_term_templates WHERE id = %d",
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
     * Salva o template
     */
    public function save() {
        global $wpdb;
        
        $data = array(
            'type_id' => $this->type_id,
            'title' => $this->title,
            'content' => $this->content,
            'status' => $this->status
        );
        
        $format = array('%d', '%s', '%s', '%s');
        
        if ($this->id) {
            $result = $wpdb->update(
                $wpdb->prefix . 'apwp_term_templates',
                $data,
                array('id' => $this->id),
                $format,
                array('%d')
            );
            
            return $result !== false ? $this->id : false;
        } else {
            $result = $wpdb->insert(
                $wpdb->prefix . 'apwp_term_templates',
                $data,
                $format
            );
            
            return $result ? $wpdb->insert_id : false;
        }
    }

    /**
     * Deleta um template
     */
    public function delete($id = null) {
        global $wpdb;
        
        $id = $id ?? $this->id;
        
        if (!$id) {
            return false;
        }
        
        return $wpdb->delete(
            $wpdb->prefix . 'apwp_term_templates',
            array('id' => $id),
            array('%d')
        );
    }

    /**
     * Processa os shortcodes do template
     */
    public function process_shortcodes($content, $data) {
        $shortcodes = array(
            // Dados do adotante
            '[adopter_name]' => $data['adopter']['name'] ?? '',
            '[adopter_email]' => $data['adopter']['email'] ?? '',
            '[adopter_phone]' => $data['adopter']['phone'] ?? '',
            '[adopter_address]' => $data['adopter']['address'] ?? '',
            '[adopter_city]' => $data['adopter']['city'] ?? '',
            '[adopter_state]' => $data['adopter']['state'] ?? '',
            '[adopter_zip]' => $data['adopter']['zip'] ?? '',
            '[adopter_cpf]' => $data['adopter']['cpf'] ?? '',
            '[adopter_rg]' => $data['adopter']['rg'] ?? '',
            
            // Dados do pet
            '[pet_name]' => $data['pet']['name'] ?? '',
            '[pet_species]' => $data['pet']['species'] ?? '',
            '[pet_breed]' => $data['pet']['breed'] ?? '',
            '[pet_age]' => $data['pet']['age'] ?? '',
            '[pet_gender]' => $data['pet']['gender'] ?? '',
            '[pet_size]' => $data['pet']['size'] ?? '',
            
            // Dados da organização
            '[org_name]' => $data['organization']['name'] ?? '',
            '[org_email]' => $data['organization']['email'] ?? '',
            '[org_phone]' => $data['organization']['phone'] ?? '',
            '[org_address]' => $data['organization']['address'] ?? '',
            '[org_city]' => $data['organization']['city'] ?? '',
            '[org_state]' => $data['organization']['state'] ?? '',
            '[org_zip]' => $data['organization']['zip'] ?? '',
            
            // Dados da adoção
            '[adoption_date]' => $data['adoption']['date'] ?? date('d/m/Y'),
            '[adoption_id]' => $data['adoption']['id'] ?? '',
            
            // Dados do termo
            '[term_date]' => date('d/m/Y'),
            '[term_time]' => date('H:i'),
            '[term_title]' => $this->title ?? '',
        );
        
        return str_replace(array_keys($shortcodes), array_values($shortcodes), $content);
    }

    /**
     * Retorna a lista de shortcodes disponíveis com suas descrições
     */
    public static function get_available_shortcodes() {
        return array(
            'Dados do Adotante' => array(
                '[adopter_name]' => 'Nome do adotante',
                '[adopter_email]' => 'Email do adotante',
                '[adopter_phone]' => 'Telefone do adotante',
                '[adopter_address]' => 'Endereço do adotante',
                '[adopter_city]' => 'Cidade do adotante',
                '[adopter_state]' => 'Estado do adotante',
                '[adopter_zip]' => 'CEP do adotante',
                '[adopter_cpf]' => 'CPF do adotante',
                '[adopter_rg]' => 'RG do adotante'
            ),
            'Dados do Pet' => array(
                '[pet_name]' => 'Nome do pet',
                '[pet_species]' => 'Espécie do pet',
                '[pet_breed]' => 'Raça do pet',
                '[pet_age]' => 'Idade do pet',
                '[pet_gender]' => 'Gênero do pet',
                '[pet_size]' => 'Porte do pet'
            ),
            'Dados da Organização' => array(
                '[org_name]' => 'Nome da organização',
                '[org_email]' => 'Email da organização',
                '[org_phone]' => 'Telefone da organização',
                '[org_address]' => 'Endereço da organização',
                '[org_city]' => 'Cidade da organização',
                '[org_state]' => 'Estado da organização',
                '[org_zip]' => 'CEP da organização'
            ),
            'Dados da Adoção' => array(
                '[adoption_date]' => 'Data da adoção',
                '[adoption_id]' => 'ID da adoção'
            ),
            'Dados do Termo' => array(
                '[term_date]' => 'Data atual',
                '[term_time]' => 'Hora atual',
                '[term_title]' => 'Título do termo'
            )
        );
    }
}
