<?php
/**
 * Template para configurações de termos
 */

// Verifica permissões
if (!current_user_can('manage_options')) {
    wp_die(__('Você não tem permissão para acessar esta página.', 'amigopet-wp'));
}

// Salva as configurações
if (isset($_POST['submit_term_settings'])) {
    check_admin_referer('save_term_settings', 'term_settings_nonce');
    
    // Atualiza as opções
    update_option('apwp_require_digital_signature', isset($_POST['require_digital_signature']));
    update_option('apwp_enable_term_versioning', isset($_POST['enable_term_versioning']));
    update_option('apwp_term_signature_type', sanitize_text_field($_POST['signature_type']));
    update_option('apwp_term_expiration_days', sanitize_text_field($_POST['expiration_days']));
    update_option('apwp_term_reminder_days', sanitize_text_field($_POST['reminder_days']));
    update_option('apwp_term_footer_text', wp_kses_post($_POST['footer_text']));
    update_option('apwp_term_header_image', esc_url_raw($_POST['header_image']));
    update_option('apwp_term_watermark_text', sanitize_text_field($_POST['watermark_text']));
    
    echo '<div class="notice notice-success"><p>' . __('Configurações salvas com sucesso!', 'amigopet-wp') . '</p></div>';
}

// Busca configurações atuais
$require_digital_signature = get_option('apwp_require_digital_signature', true);
$enable_term_versioning = get_option('apwp_enable_term_versioning', true);
$signature_type = get_option('apwp_term_signature_type', 'digital');
$expiration_days = get_option('apwp_term_expiration_days', '365');
$reminder_days = get_option('apwp_term_reminder_days', '30,15,7');
$footer_text = get_option('apwp_term_footer_text', '');
$header_image = get_option('apwp_term_header_image', '');
$watermark_text = get_option('apwp_term_watermark_text', 'AMIGOPET');
?>

<div class="wrap">
    <h2><?php _e('Configurações de Termos', 'amigopet-wp'); ?></h2>
    
    <form method="post" action="">
        <?php wp_nonce_field('save_term_settings', 'term_settings_nonce'); ?>
        
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Assinatura Digital', 'amigopet-wp'); ?></th>
                <td>
                    <fieldset>
                        <label>
                            <input type="checkbox" name="require_digital_signature" value="1" <?php checked($require_digital_signature); ?>>
                            <?php _e('Exigir assinatura digital', 'amigopet-wp'); ?>
                        </label>
                        <br>
                        <label>
                            <input type="checkbox" name="enable_term_versioning" value="1" <?php checked($enable_term_versioning); ?>>
                            <?php _e('Habilitar versionamento de termos', 'amigopet-wp'); ?>
                        </label>
                    </fieldset>
                </td>
            </tr>
            
            <tr>
                <th scope="row"><label for="signature_type"><?php _e('Tipo de Assinatura', 'amigopet-wp'); ?></label></th>
                <td>
                    <select name="signature_type" id="signature_type">
                        <option value="digital" <?php selected($signature_type, 'digital'); ?>><?php _e('Assinatura Digital', 'amigopet-wp'); ?></option>
                        <option value="electronic" <?php selected($signature_type, 'electronic'); ?>><?php _e('Assinatura Eletrônica', 'amigopet-wp'); ?></option>
                        <option value="manual" <?php selected($signature_type, 'manual'); ?>><?php _e('Assinatura Manual', 'amigopet-wp'); ?></option>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th scope="row"><label for="expiration_days"><?php _e('Validade dos Termos', 'amigopet-wp'); ?></label></th>
                <td>
                    <input type="number" name="expiration_days" id="expiration_days" value="<?php echo esc_attr($expiration_days); ?>" class="small-text" min="1">
                    <p class="description"><?php _e('Dias até a expiração do termo (0 para nunca expirar)', 'amigopet-wp'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row"><label for="reminder_days"><?php _e('Lembretes de Renovação', 'amigopet-wp'); ?></label></th>
                <td>
                    <input type="text" name="reminder_days" id="reminder_days" value="<?php echo esc_attr($reminder_days); ?>" class="regular-text">
                    <p class="description"><?php _e('Dias antes da expiração para enviar lembretes (separados por vírgula)', 'amigopet-wp'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row"><label for="header_image"><?php _e('Imagem do Cabeçalho', 'amigopet-wp'); ?></label></th>
                <td>
                    <input type="url" name="header_image" id="header_image" value="<?php echo esc_url($header_image); ?>" class="regular-text">
                    <button type="button" class="button-secondary" id="upload_header_image"><?php _e('Upload', 'amigopet-wp'); ?></button>
                    <p class="description"><?php _e('URL da imagem que aparecerá no cabeçalho dos termos', 'amigopet-wp'); ?></p>
                    <div id="header_image_preview">
                        <?php if ($header_image): ?>
                            <img src="<?php echo esc_url($header_image); ?>" style="max-width: 300px; margin-top: 10px;">
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            
            <tr>
                <th scope="row"><label for="watermark_text"><?php _e('Texto da Marca d\'água', 'amigopet-wp'); ?></label></th>
                <td>
                    <input type="text" name="watermark_text" id="watermark_text" value="<?php echo esc_attr($watermark_text); ?>" class="regular-text">
                    <p class="description"><?php _e('Texto que aparecerá como marca d\'água nos termos', 'amigopet-wp'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row"><label for="footer_text"><?php _e('Texto do Rodapé', 'amigopet-wp'); ?></label></th>
                <td>
                    <?php
                    wp_editor($footer_text, 'footer_text', array(
                        'textarea_name' => 'footer_text',
                        'textarea_rows' => 5,
                        'media_buttons' => false,
                        'teeny' => true,
                        'quicktags' => true
                    ));
                    ?>
                    <p class="description"><?php _e('Texto que aparecerá no rodapé de todos os termos', 'amigopet-wp'); ?></p>
                </td>
            </tr>
        </table>
        
        <?php submit_button(__('Salvar Configurações', 'amigopet-wp'), 'primary', 'submit_term_settings'); ?>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    // Upload de imagem
    $('#upload_header_image').click(function(e) {
        e.preventDefault();
        
        var image = wp.media({
            title: '<?php _e('Selecionar Imagem de Cabeçalho', 'amigopet-wp'); ?>',
            multiple: false
        }).open().on('select', function() {
            var uploaded_image = image.state().get('selection').first();
            var image_url = uploaded_image.toJSON().url;
            
            $('#header_image').val(image_url);
            $('#header_image_preview').html('<img src="' + image_url + '" style="max-width: 300px; margin-top: 10px;">');
        });
    });
});</script>
