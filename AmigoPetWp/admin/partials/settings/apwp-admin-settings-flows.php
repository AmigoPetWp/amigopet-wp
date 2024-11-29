<?php
/**
 * Template para configurações de fluxos
 */

// Verifica permissões
if (!current_user_can('manage_options')) {
    wp_die(__('Você não tem permissão para acessar esta página.', 'amigopet-wp'));
}

// Salva as configurações
if (isset($_POST['submit_flow_settings'])) {
    check_admin_referer('save_flow_settings', 'flow_settings_nonce');
    
    // Atualiza as opções
    update_option('apwp_enable_adoption_workflow', isset($_POST['enable_adoption_workflow']));
    update_option('apwp_enable_term_workflow', isset($_POST['enable_term_workflow']));
    update_option('apwp_enable_organization_workflow', isset($_POST['enable_organization_workflow']));
    update_option('apwp_adoption_workflow_steps', sanitize_textarea_field($_POST['adoption_workflow_steps']));
    update_option('apwp_term_workflow_steps', sanitize_textarea_field($_POST['term_workflow_steps']));
    update_option('apwp_organization_workflow_steps', sanitize_textarea_field($_POST['organization_workflow_steps']));
    update_option('apwp_workflow_notifications', isset($_POST['workflow_notifications']));
    update_option('apwp_workflow_auto_assignment', isset($_POST['workflow_auto_assignment']));
    
    echo '<div class="notice notice-success"><p>' . __('Configurações salvas com sucesso!', 'amigopet-wp') . '</p></div>';
}

// Busca configurações atuais
$enable_adoption_workflow = get_option('apwp_enable_adoption_workflow', true);
$enable_term_workflow = get_option('apwp_enable_term_workflow', true);
$enable_organization_workflow = get_option('apwp_enable_organization_workflow', true);
$adoption_workflow_steps = get_option('apwp_adoption_workflow_steps', 
"1. Solicitação Recebida
2. Análise de Documentos
3. Entrevista
4. Visita Domiciliar
5. Aprovação Final
6. Assinatura de Termo
7. Entrega do Pet
8. Acompanhamento");
$term_workflow_steps = get_option('apwp_term_workflow_steps',
"1. Criação
2. Revisão
3. Aprovação Jurídica
4. Publicação");
$organization_workflow_steps = get_option('apwp_organization_workflow_steps',
"1. Cadastro Inicial
2. Verificação de Documentos
3. Visita Técnica
4. Aprovação
5. Ativação");
$workflow_notifications = get_option('apwp_workflow_notifications', true);
$workflow_auto_assignment = get_option('apwp_workflow_auto_assignment', false);
?>

<div class="apwp-wrap">
    <div class="apwp-header">
        <h1><?php _e('Configurações de Fluxos', 'amigopet-wp'); ?></h1>
    </div>
    
    <div class="apwp-content">
        <form method="post" action="" class="apwp-form">
            <?php wp_nonce_field('save_flow_settings', 'flow_settings_nonce'); ?>
            
            <div class="apwp-form-section">
                <h2><?php _e('Ativar Fluxos', 'amigopet-wp'); ?></h2>
                <div class="apwp-form-field">
                    <fieldset>
                        <label class="apwp-checkbox-label">
                            <input type="checkbox" name="enable_adoption_workflow" value="1" <?php checked($enable_adoption_workflow); ?>>
                            <?php _e('Fluxo de Adoção', 'amigopet-wp'); ?>
                        </label>
                        <br>
                        <label class="apwp-checkbox-label">
                            <input type="checkbox" name="enable_term_workflow" value="1" <?php checked($enable_term_workflow); ?>>
                            <?php _e('Fluxo de Termos', 'amigopet-wp'); ?>
                        </label>
                        <br>
                        <label class="apwp-checkbox-label">
                            <input type="checkbox" name="enable_organization_workflow" value="1" <?php checked($enable_organization_workflow); ?>>
                            <?php _e('Fluxo de Organizações', 'amigopet-wp'); ?>
                        </label>
                    </fieldset>
                </div>
            </div>
            
            <div class="apwp-form-section">
                <h2><?php _e('Etapas dos Fluxos', 'amigopet-wp'); ?></h2>
                
                <div class="apwp-form-field">
                    <label for="adoption_workflow_steps"><?php _e('Etapas do Fluxo de Adoção', 'amigopet-wp'); ?></label>
                    <textarea name="adoption_workflow_steps" id="adoption_workflow_steps" rows="8" class="large-text"><?php echo esc_textarea($adoption_workflow_steps); ?></textarea>
                    <p class="description"><?php _e('Lista de etapas do fluxo de adoção (uma por linha)', 'amigopet-wp'); ?></p>
                </div>
                
                <div class="apwp-form-field">
                    <label for="term_workflow_steps"><?php _e('Etapas do Fluxo de Termos', 'amigopet-wp'); ?></label>
                    <textarea name="term_workflow_steps" id="term_workflow_steps" rows="4" class="large-text"><?php echo esc_textarea($term_workflow_steps); ?></textarea>
                    <p class="description"><?php _e('Lista de etapas do fluxo de termos (uma por linha)', 'amigopet-wp'); ?></p>
                </div>
                
                <div class="apwp-form-field">
                    <label for="organization_workflow_steps"><?php _e('Etapas do Fluxo de Organizações', 'amigopet-wp'); ?></label>
                    <textarea name="organization_workflow_steps" id="organization_workflow_steps" rows="5" class="large-text"><?php echo esc_textarea($organization_workflow_steps); ?></textarea>
                    <p class="description"><?php _e('Lista de etapas do fluxo de organizações (uma por linha)', 'amigopet-wp'); ?></p>
                </div>
            </div>
            
            <div class="apwp-form-section">
                <h2><?php _e('Configurações Adicionais', 'amigopet-wp'); ?></h2>
                <div class="apwp-form-field">
                    <fieldset>
                        <label class="apwp-checkbox-label">
                            <input type="checkbox" name="workflow_notifications" value="1" <?php checked($workflow_notifications); ?>>
                            <?php _e('Enviar notificações de mudança de etapa', 'amigopet-wp'); ?>
                        </label>
                        <br>
                        <label class="apwp-checkbox-label">
                            <input type="checkbox" name="workflow_auto_assignment" value="1" <?php checked($workflow_auto_assignment); ?>>
                            <?php _e('Atribuir responsáveis automaticamente', 'amigopet-wp'); ?>
                        </label>
                    </fieldset>
                </div>
            </div>
            
            <div class="apwp-form-actions">
                <?php submit_button(__('Salvar Configurações', 'amigopet-wp'), 'primary', 'submit_flow_settings'); ?>
            </div>
        </form>
    </div>
</div>
