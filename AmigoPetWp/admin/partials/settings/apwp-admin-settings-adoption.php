<?php
/**
 * Template para configurações de adoção
 */

// Verifica permissões
if (!current_user_can('manage_options')) {
    wp_die(__('Você não tem permissão para acessar esta página.', 'amigopet-wp'));
}

// Salva as configurações
if (isset($_POST['submit_adoption_settings'])) {
    check_admin_referer('save_adoption_settings', 'adoption_settings_nonce');
    
    // Atualiza as opções
    update_option('apwp_require_term_signature', isset($_POST['require_term_signature']));
    update_option('apwp_require_document_verification', isset($_POST['require_document_verification']));
    update_option('apwp_require_address_verification', isset($_POST['require_address_verification']));
    update_option('apwp_require_reference_check', isset($_POST['require_reference_check']));
    update_option('apwp_adoption_follow_up_days', sanitize_text_field($_POST['follow_up_days']));
    update_option('apwp_max_pets_per_adopter', sanitize_text_field($_POST['max_pets_per_adopter']));
    update_option('apwp_adoption_notification_emails', sanitize_textarea_field($_POST['notification_emails']));
    update_option('apwp_adoption_status_flow', sanitize_textarea_field($_POST['status_flow']));
    
    echo '<div class="notice notice-success"><p>' . __('Configurações salvas com sucesso!', 'amigopet-wp') . '</p></div>';
}

// Busca configurações atuais
$require_term_signature = get_option('apwp_require_term_signature', true);
$require_document_verification = get_option('apwp_require_document_verification', true);
$require_address_verification = get_option('apwp_require_address_verification', true);
$require_reference_check = get_option('apwp_require_reference_check', false);
$follow_up_days = get_option('apwp_adoption_follow_up_days', '30,60,90');
$max_pets_per_adopter = get_option('apwp_max_pets_per_adopter', '3');
$notification_emails = get_option('apwp_adoption_notification_emails', '');
$status_flow = get_option('apwp_adoption_status_flow', "pending\napproved\nin_progress\ncompleted\ncanceled");
?>

<div class="wrap">
    <h2><?php _e('Configurações de Adoção', 'amigopet-wp'); ?></h2>
    
    <form method="post" action="">
        <?php wp_nonce_field('save_adoption_settings', 'adoption_settings_nonce'); ?>
        
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Requisitos de Adoção', 'amigopet-wp'); ?></th>
                <td>
                    <fieldset>
                        <label>
                            <input type="checkbox" name="require_term_signature" value="1" <?php checked($require_term_signature); ?>>
                            <?php _e('Exigir assinatura do termo de adoção', 'amigopet-wp'); ?>
                        </label>
                        <br>
                        <label>
                            <input type="checkbox" name="require_document_verification" value="1" <?php checked($require_document_verification); ?>>
                            <?php _e('Exigir verificação de documentos', 'amigopet-wp'); ?>
                        </label>
                        <br>
                        <label>
                            <input type="checkbox" name="require_address_verification" value="1" <?php checked($require_address_verification); ?>>
                            <?php _e('Exigir comprovante de residência', 'amigopet-wp'); ?>
                        </label>
                        <br>
                        <label>
                            <input type="checkbox" name="require_reference_check" value="1" <?php checked($require_reference_check); ?>>
                            <?php _e('Exigir verificação de referências', 'amigopet-wp'); ?>
                        </label>
                    </fieldset>
                </td>
            </tr>
            
            <tr>
                <th scope="row"><label for="follow_up_days"><?php _e('Dias para Acompanhamento', 'amigopet-wp'); ?></label></th>
                <td>
                    <input type="text" name="follow_up_days" id="follow_up_days" value="<?php echo esc_attr($follow_up_days); ?>" class="regular-text">
                    <p class="description"><?php _e('Dias após a adoção para fazer acompanhamento (separados por vírgula)', 'amigopet-wp'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row"><label for="max_pets_per_adopter"><?php _e('Máximo de Pets por Adotante', 'amigopet-wp'); ?></label></th>
                <td>
                    <input type="number" name="max_pets_per_adopter" id="max_pets_per_adopter" value="<?php echo esc_attr($max_pets_per_adopter); ?>" class="small-text" min="1">
                    <p class="description"><?php _e('Número máximo de pets que um adotante pode ter simultaneamente', 'amigopet-wp'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row"><label for="notification_emails"><?php _e('E-mails para Notificação', 'amigopet-wp'); ?></label></th>
                <td>
                    <textarea name="notification_emails" id="notification_emails" rows="3" class="large-text"><?php echo esc_textarea($notification_emails); ?></textarea>
                    <p class="description"><?php _e('E-mails que receberão notificações sobre adoções (um por linha)', 'amigopet-wp'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row"><label for="status_flow"><?php _e('Fluxo de Status', 'amigopet-wp'); ?></label></th>
                <td>
                    <textarea name="status_flow" id="status_flow" rows="5" class="large-text"><?php echo esc_textarea($status_flow); ?></textarea>
                    <p class="description"><?php _e('Status possíveis no fluxo de adoção (um por linha)', 'amigopet-wp'); ?></p>
                </td>
            </tr>
        </table>
        
        <?php submit_button(__('Salvar Configurações', 'amigopet-wp'), 'primary', 'submit_adoption_settings'); ?>
    </form>
</div>
