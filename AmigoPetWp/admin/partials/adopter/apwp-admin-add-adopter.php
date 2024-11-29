<?php
/**
 * Template para adicionar um novo adotante
 */

// Verifica permissões
if (!current_user_can('manage_options')) {
    wp_die(__('Você não tem permissão para acessar esta página.', 'amigopet-wp'));
}

// Processa o formulário se foi enviado
if (isset($_POST['submit_adopter'])) {
    check_admin_referer('add_adopter', 'adopter_nonce');
    
    $adopter_data = array(
        'name' => sanitize_text_field($_POST['name']),
        'email' => sanitize_email($_POST['email']),
        'phone' => sanitize_text_field($_POST['phone']),
        'address' => sanitize_textarea_field($_POST['address']),
        'city' => sanitize_text_field($_POST['city']),
        'state' => sanitize_text_field($_POST['state']),
        'zip' => sanitize_text_field($_POST['zip']),
        'notes' => sanitize_textarea_field($_POST['notes']),
        'status' => 'active',
        'created_at' => current_time('mysql')
    );

    global $wpdb;
    $result = $wpdb->insert(
        $wpdb->prefix . 'apwp_adopters',
        $adopter_data,
        array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
    );

    if ($result) {
        echo '<div class="notice notice-success"><p>' . __('Adotante adicionado com sucesso!', 'amigopet-wp') . '</p></div>';
    } else {
        echo '<div class="notice notice-error"><p>' . __('Erro ao adicionar adotante.', 'amigopet-wp') . '</p></div>';
    }
}
?>

<div class="wrap">
    <h1><?php _e('Adicionar Novo Adotante', 'amigopet-wp'); ?></h1>

    <form method="post" action="">
        <?php wp_nonce_field('add_adopter', 'adopter_nonce'); ?>
        
        <table class="form-table">
            <tr>
                <th scope="row"><label for="name"><?php _e('Nome Completo', 'amigopet-wp'); ?></label></th>
                <td><input type="text" name="name" id="name" class="regular-text" required></td>
            </tr>
            <tr>
                <th scope="row"><label for="email"><?php _e('E-mail', 'amigopet-wp'); ?></label></th>
                <td><input type="email" name="email" id="email" class="regular-text" required></td>
            </tr>
            <tr>
                <th scope="row"><label for="phone"><?php _e('Telefone', 'amigopet-wp'); ?></label></th>
                <td><input type="tel" name="phone" id="phone" class="regular-text" required></td>
            </tr>
            <tr>
                <th scope="row"><label for="address"><?php _e('Endereço', 'amigopet-wp'); ?></label></th>
                <td><textarea name="address" id="address" class="large-text" rows="3" required></textarea></td>
            </tr>
            <tr>
                <th scope="row"><label for="city"><?php _e('Cidade', 'amigopet-wp'); ?></label></th>
                <td><input type="text" name="city" id="city" class="regular-text" required></td>
            </tr>
            <tr>
                <th scope="row"><label for="state"><?php _e('Estado', 'amigopet-wp'); ?></label></th>
                <td>
                    <select name="state" id="state" required>
                        <option value=""><?php _e('Selecione...', 'amigopet-wp'); ?></option>
                        <?php
                        $states = array(
                            'AC' => 'Acre', 'AL' => 'Alagoas', 'AP' => 'Amapá',
                            'AM' => 'Amazonas', 'BA' => 'Bahia', 'CE' => 'Ceará',
                            'DF' => 'Distrito Federal', 'ES' => 'Espírito Santo',
                            'GO' => 'Goiás', 'MA' => 'Maranhão', 'MT' => 'Mato Grosso',
                            'MS' => 'Mato Grosso do Sul', 'MG' => 'Minas Gerais',
                            'PA' => 'Pará', 'PB' => 'Paraíba', 'PR' => 'Paraná',
                            'PE' => 'Pernambuco', 'PI' => 'Piauí', 'RJ' => 'Rio de Janeiro',
                            'RN' => 'Rio Grande do Norte', 'RS' => 'Rio Grande do Sul',
                            'RO' => 'Rondônia', 'RR' => 'Roraima', 'SC' => 'Santa Catarina',
                            'SP' => 'São Paulo', 'SE' => 'Sergipe', 'TO' => 'Tocantins'
                        );
                        foreach ($states as $uf => $name) {
                            echo '<option value="' . esc_attr($uf) . '">' . esc_html($name) . '</option>';
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="zip"><?php _e('CEP', 'amigopet-wp'); ?></label></th>
                <td><input type="text" name="zip" id="zip" class="regular-text" required></td>
            </tr>
            <tr>
                <th scope="row"><label for="notes"><?php _e('Observações', 'amigopet-wp'); ?></label></th>
                <td><textarea name="notes" id="notes" class="large-text" rows="5"></textarea></td>
            </tr>
        </table>

        <?php submit_button(__('Adicionar Adotante', 'amigopet-wp'), 'primary', 'submit_adopter'); ?>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    // Máscara para telefone
    $('#phone').mask('(00) 00000-0000');
    
    // Máscara para CEP
    $('#zip').mask('00000-000');
});
</script>
