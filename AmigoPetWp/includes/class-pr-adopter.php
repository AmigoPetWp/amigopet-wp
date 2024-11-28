<?php

/**
 * Classe responsável pelo gerenciamento de adotantes.
 *
 * @since      1.0.0
 * @package    AmigoPetWp
 * @subpackage AmigoPetWp/includes
 */
class APWP_Adopter {
    /**
     * Propriedades do adotante
     *
     * @since    1.0.0
     */
    private $id;
    private $name;
    private $cpf;
    private $rg;
    private $email;
    private $phone;
    private $address;
    private $created_at;
    private $updated_at;

    /**
     * Construtor da classe
     *
     * @since    1.0.0
     * @param    array    $data    Dados do adotante
     */
    public function __construct($data = []) {
        if (!empty($data)) {
            $this->set_data($data);
        }
    }

    /**
     * Define os dados do adotante
     *
     * @since    1.0.0
     * @param    array    $data    Dados do adotante
     */
    public function set_data($data) {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? '';
        $this->cpf = $data['cpf'] ?? '';
        $this->rg = $data['rg'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->phone = $data['phone'] ?? '';
        $this->address = $data['address'] ?? '';
        $this->created_at = $data['created_at'] ?? current_time('mysql');
        $this->updated_at = $data['updated_at'] ?? current_time('mysql');
    }

    /**
     * Obtém os dados do adotante
     *
     * @since    1.0.0
     * @return   array    Dados do adotante
     */
    public function get_data() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'cpf' => $this->cpf,
            'rg' => $this->rg,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }

    /**
     * Métodos mágicos para acessar propriedades
     *
     * @since    1.0.0
     */
    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    /**
     * Métodos mágicos para definir propriedades
     *
     * @since    1.0.0
     */
    public function __set($property, $value) {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }
    }

    /**
     * Valida os dados do adotante
     *
     * @since    1.0.0
     * @return   array|bool   Array de erros ou true se válido
     */
    public function validate() {
        $errors = [];

        // Validação de nome
        if (empty($this->name)) {
            $errors[] = 'Nome do adotante é obrigatório';
        }

        // Validação de CPF
        if (!empty($this->cpf) && !$this->validate_cpf($this->cpf)) {
            $errors[] = 'CPF inválido';
        }

        // Validação de RG
        if (!empty($this->rg) && !$this->validate_rg($this->rg)) {
            $errors[] = 'RG inválido';
        }

        // Validação de email (opcional)
        if (!empty($this->email) && !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email inválido';
        }

        return empty($errors) ? true : $errors;
    }

    /**
     * Salva o adotante no banco de dados
     *
     * @since    1.0.0
     * @return   int|WP_Error   ID do adotante ou objeto de erro
     */
    public function save() {
        global $wpdb;

        // Valida os dados antes de salvar
        $validation = $this->validate();
        if ($validation !== true) {
            return new WP_Error('invalid_adopter', 'Dados do adotante inválidos', $validation);
        }

        $table_name = $wpdb->prefix . 'apwp_adopters';
        
        $data = [
            'name' => sanitize_text_field($this->name),
            'cpf' => sanitize_text_field($this->cpf),
            'rg' => sanitize_text_field($this->rg),
            'email' => sanitize_email($this->email),
            'phone' => sanitize_text_field($this->phone),
            'address' => sanitize_textarea_field($this->address),
            'created_at' => $this->created_at,
            'updated_at' => current_time('mysql')
        ];

        if (empty($this->id)) {
            // Novo adotante
            $result = $wpdb->insert($table_name, $data);
            if ($result === false) {
                return new WP_Error('db_insert_error', 'Não foi possível cadastrar o adotante');
            }
            $this->id = $wpdb->insert_id;
        } else {
            // Atualiza adotante existente
            $result = $wpdb->update($table_name, $data, ['id' => $this->id]);
            if ($result === false) {
                return new WP_Error('db_update_error', 'Não foi possível atualizar o adotante');
            }
        }

        return $this->id;
    }

    /**
     * Recupera um adotante pelo ID
     *
     * @since    1.0.0
     * @param    int    $id    ID do adotante
     * @return   APWP_Adopter|WP_Error   Objeto do adotante ou erro
     */
    public static function get($id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'apwp_adopters';

        $adopter_data = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d", 
            $id
        ), ARRAY_A);

        if (empty($adopter_data)) {
            return new WP_Error('adopter_not_found', 'Adotante não encontrado');
        }

        return new self($adopter_data);
    }

    /**
     * Valida CPF
     *
     * @since    1.0.0
     * @param    string   $cpf    CPF a ser validado
     * @return   bool             Verdadeiro se CPF for válido
     */
    private function validate_cpf($cpf) {
        // Remove caracteres não numéricos
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        // Deve ter 11 dígitos
        if (strlen($cpf) != 11) {
            return false;
        }

        // Verifica CPFs conhecidos como inválidos
        if (preg_match('/^([0-9])\1+$/', $cpf)) {
            return false;
        }

        // Calcula os dígitos verificadores
        $soma = 0;
        for ($i = 0; $i < 9; $i++) {
            $soma += intval($cpf[$i]) * (10 - $i);
        }
        $resto = 11 - ($soma % 11);
        $resto = ($resto == 10 || $resto == 11) ? 0 : $resto;

        if ($resto != intval($cpf[9])) {
            return false;
        }

        $soma = 0;
        for ($i = 0; $i < 10; $i++) {
            $soma += intval($cpf[$i]) * (11 - $i);
        }
        $resto = 11 - ($soma % 11);
        $resto = ($resto == 10 || $resto == 11) ? 0 : $resto;

        return $resto == intval($cpf[10]);
    }

    /**
     * Valida RG
     *
     * @since    1.0.0
     * @param    string   $rg    RG a ser validado
     * @return   bool            Verdadeiro se RG for válido
     */
    private function validate_rg($rg) {
        // Remove caracteres não numéricos
        $rg = preg_replace('/[^0-9]/', '', $rg);

        // Deve ter entre 7 e 9 dígitos
        return (strlen($rg) >= 7 && strlen($rg) <= 9);
    }
}
