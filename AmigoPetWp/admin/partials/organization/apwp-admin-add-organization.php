<?php
/**
 * Template para adicionar uma nova organização
 */

// Verifica permissões
if (!current_user_can('manage_options')) {
    wp_die(__('Você não tem permissão para acessar esta página.', 'amigopet-wp'));
}

// Processa o formulário se foi enviado
if (isset($_POST['submit_organization'])) {
    check_admin_referer('add_organization', 'organization_nonce');
    
    // Upload do logo
    $logo_url = '';
    if (!empty($_FILES['logo']['name'])) {
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        
        $attachment_id = media_handle_upload('logo', 0);
        if (!is_wp_error($attachment_id)) {
            $logo_url = wp_get_attachment_url($attachment_id);
        }
    }
    
    $organization_data = array(
        'name' => sanitize_text_field($_POST['name']),
        'cnpj' => sanitize_text_field($_POST['cnpj']),
        'email' => sanitize_email($_POST['email']),
        'phone' => sanitize_text_field($_POST['phone']),
        'address' => sanitize_textarea_field($_POST['address']),
        'city' => sanitize_text_field($_POST['city']),
        'state' => sanitize_text_field($_POST['state']),
        'zip' => sanitize_text_field($_POST['zip']),
        'website' => esc_url_raw($_POST['website']),
        'logo_url' => $logo_url,
        'description' => wp_kses_post($_POST['description']),
        'status' => 'active',
        'created_at' => current_time('mysql')
    );

    global $wpdb;
    $result = $wpdb->insert(
        $wpdb->prefix . 'apwp_organizations',
        $organization_data,
        array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
    );

    if ($result) {
        echo '<div class="notice notice-success"><p>' . __('Organização adicionada com sucesso!', 'amigopet-wp') . '</p></div>';
    } else {
        echo '<div class="notice notice-error"><p>' . __('Erro ao adicionar organização.', 'amigopet-wp') . '</p></div>';
    }
}
?>

<div class="wrap">
    <h1><?php _e('Adicionar Nova Organização', 'amigopet-wp'); ?></h1>

    <form method="post" action="" enctype="multipart/form-data">
        <?php wp_nonce_field('add_organization', 'organization_nonce'); ?>
        
        <table class="form-table">
            <tr>
                <th scope="row"><label for="name"><?php _e('Nome', 'amigopet-wp'); ?></label></th>
                <td><input type="text" name="name" id="name" class="regular-text" required></td>
            </tr>
            <tr>
                <th scope="row"><label for="cnpj"><?php _e('CNPJ', 'amigopet-wp'); ?></label></th>
                <td><input type="text" name="cnpj" id="cnpj" class="regular-text" required></td>
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
                <th scope="row"><label for="website"><?php _e('Website', 'amigopet-wp'); ?></label></th>
                <td><input type="url" name="website" id="website" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="logo"><?php _e('Logo', 'amigopet-wp'); ?></label></th>
                <td>
                    <input type="file" name="logo" id="logo" accept="image/*">
                    <p class="description"><?php _e('Tamanho recomendado: 300x300 pixels', 'amigopet-wp'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="description"><?php _e('Descrição', 'amigopet-wp'); ?></label></th>
                <td>
                    <?php
                    wp_editor('', 'description', array(
                        'textarea_name' => 'description',
                        'textarea_rows' => 10,
                        'media_buttons' => true,
                        'teeny' => false,
                        'quicktags' => true
                    ));
                    ?>
                </td>
            </tr>
        </table>

        <?php submit_button(__('Adicionar Organização', 'amigopet-wp'), 'primary', 'submit_organization'); ?>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    // Máscara para CNPJ
    $('#cnpj').mask('00.000.000/0000-00');
    
    // Máscara para telefone
    $('#phone').mask('(00) 00000-0000');
    
    // Máscara para CEP
    $('#zip').mask('00000-000');
});
</script>
