<?php
/**
 * View de configurações do admin
 */
?>
<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <div class="apwp-settings">
        <form id="apwp-settings-form">
            <!-- Configurações gerais -->
            <div class="apwp-settings-section">
                <h2><?php _e('Configurações Gerais', 'amigopet-wp'); ?></h2>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="organization_name"><?php _e('Nome da Organização', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="organization_name" name="organization_name" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="organization_email"><?php _e('Email da Organização', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <input type="email" id="organization_email" name="organization_email" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="organization_phone"><?php _e('Telefone da Organização', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="organization_phone" name="organization_phone" class="regular-text">
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Configurações de API -->
            <div class="apwp-settings-section">
                <h2><?php _e('Configurações de API', 'amigopet-wp'); ?></h2>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="google_maps_key"><?php _e('Chave do Google Maps', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="google_maps_key" name="google_maps_key" class="regular-text">
                            <p class="description">
                                <?php _e('Necessária para funcionalidades de localização.', 'amigopet-wp'); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="petfinder_key"><?php _e('Chave do PetFinder', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="petfinder_key" name="petfinder_key" class="regular-text">
                            <p class="description">
                                <?php _e('Necessária para integração com o PetFinder.', 'amigopet-wp'); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="payment_gateway_key"><?php _e('Chave do Gateway de Pagamento', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="payment_gateway_key" name="payment_gateway_key" class="regular-text">
                            <p class="description">
                                <?php _e('Necessária para processar pagamentos.', 'amigopet-wp'); ?>
                            </p>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Termos e Condições -->
            <div class="apwp-settings-section">
                <h2><?php _e('Termos e Condições', 'amigopet-wp'); ?></h2>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="adoption_terms"><?php _e('Termos de Adoção', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <?php wp_editor('', 'adoption_terms', [
                                'textarea_name' => 'adoption_terms',
                                'textarea_rows' => 10,
                                'media_buttons' => false
                            ]); ?>
                            <p class="description">
                                <?php _e('Termos que o adotante deve aceitar antes de enviar o pedido de adoção.', 'amigopet-wp'); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="volunteer_terms"><?php _e('Termos de Voluntariado', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <?php wp_editor('', 'volunteer_terms', [
                                'textarea_name' => 'volunteer_terms',
                                'textarea_rows' => 10,
                                'media_buttons' => false
                            ]); ?>
                            <p class="description">
                                <?php _e('Termos que o voluntário deve aceitar antes de se cadastrar.', 'amigopet-wp'); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="donation_terms"><?php _e('Termos de Doação', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <?php wp_editor('', 'donation_terms', [
                                'textarea_name' => 'donation_terms',
                                'textarea_rows' => 10,
                                'media_buttons' => false
                            ]); ?>
                            <p class="description">
                                <?php _e('Termos que o doador deve aceitar antes de fazer uma doação.', 'amigopet-wp'); ?>
                            </p>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Workflow -->
            <div class="apwp-settings-section">
                <h2><?php _e('Workflow', 'amigopet-wp'); ?></h2>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="adoption_workflow"><?php _e('Processo de Adoção', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <select id="adoption_workflow" name="adoption_workflow[]" multiple class="regular-text">
                                <option value="form"><?php _e('Formulário de Adoção', 'amigopet-wp'); ?></option>
                                <option value="review"><?php _e('Revisão do Pedido', 'amigopet-wp'); ?></option>
                                <option value="interview"><?php _e('Entrevista', 'amigopet-wp'); ?></option>
                                <option value="home_visit"><?php _e('Visita Domiciliar', 'amigopet-wp'); ?></option>
                                <option value="contract"><?php _e('Contrato de Adoção', 'amigopet-wp'); ?></option>
                                <option value="followup"><?php _e('Acompanhamento', 'amigopet-wp'); ?></option>
                            </select>
                            <p class="description">
                                <?php _e('Selecione as etapas necessárias no processo de adoção.', 'amigopet-wp'); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="volunteer_workflow"><?php _e('Processo de Voluntariado', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <select id="volunteer_workflow" name="volunteer_workflow[]" multiple class="regular-text">
                                <option value="form"><?php _e('Formulário de Inscrição', 'amigopet-wp'); ?></option>
                                <option value="interview"><?php _e('Entrevista', 'amigopet-wp'); ?></option>
                                <option value="training"><?php _e('Treinamento', 'amigopet-wp'); ?></option>
                                <option value="trial"><?php _e('Período de Experiência', 'amigopet-wp'); ?></option>
                                <option value="evaluation"><?php _e('Avaliação', 'amigopet-wp'); ?></option>
                            </select>
                            <p class="description">
                                <?php _e('Selecione as etapas necessárias no processo de voluntariado.', 'amigopet-wp'); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="donation_workflow"><?php _e('Processo de Doação', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <select id="donation_workflow" name="donation_workflow[]" multiple class="regular-text">
                                <option value="form"><?php _e('Formulário de Doação', 'amigopet-wp'); ?></option>
                                <option value="payment"><?php _e('Pagamento', 'amigopet-wp'); ?></option>
                                <option value="receipt"><?php _e('Recibo', 'amigopet-wp'); ?></option>
                                <option value="thanks"><?php _e('Agradecimento', 'amigopet-wp'); ?></option>
                                <option value="report"><?php _e('Relatório de Uso', 'amigopet-wp'); ?></option>
                            </select>
                            <p class="description">
                                <?php _e('Selecione as etapas necessárias no processo de doação.', 'amigopet-wp'); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="auto_status_update"><?php _e('Atualização Automática de Status', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" id="auto_status_update" name="auto_status_update" value="1">
                                <?php _e('Atualizar automaticamente o status conforme as etapas são concluídas', 'amigopet-wp'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="notification_workflow"><?php _e('Notificações', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><?php _e('Notificações', 'amigopet-wp'); ?></legend>
                                <label>
                                    <input type="checkbox" name="notify_admin_new_adoption" value="1">
                                    <?php _e('Notificar administradores sobre novos pedidos de adoção', 'amigopet-wp'); ?>
                                </label><br>
                                <label>
                                    <input type="checkbox" name="notify_admin_new_volunteer" value="1">
                                    <?php _e('Notificar administradores sobre novos voluntários', 'amigopet-wp'); ?>
                                </label><br>
                                <label>
                                    <input type="checkbox" name="notify_admin_new_donation" value="1">
                                    <?php _e('Notificar administradores sobre novas doações', 'amigopet-wp'); ?>
                                </label><br>
                                <label>
                                    <input type="checkbox" name="notify_user_status_change" value="1">
                                    <?php _e('Notificar usuários sobre mudanças de status', 'amigopet-wp'); ?>
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Configurações de email -->
            <div class="apwp-settings-section">
                <h2><?php _e('Configurações de Email', 'amigopet-wp'); ?></h2>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="email_from_name"><?php _e('Nome do Remetente', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="email_from_name" name="email_from_name" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="email_from_email"><?php _e('Email do Remetente', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <input type="email" id="email_from_email" name="email_from_email" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="email_template_header"><?php _e('Cabeçalho do Template', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <?php wp_editor('', 'email_template_header', [
                                'textarea_name' => 'email_template_header',
                                'textarea_rows' => 10,
                                'media_buttons' => true
                            ]); ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="email_template_footer"><?php _e('Rodapé do Template', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <?php wp_editor('', 'email_template_footer', [
                                'textarea_name' => 'email_template_footer',
                                'textarea_rows' => 10,
                                'media_buttons' => true
                            ]); ?>
                        </td>
                    </tr>
                </table>
            </div>

            <p class="submit">
                <button type="submit" class="button button-primary">
                    <?php _e('Salvar Configurações', 'amigopet-wp'); ?>
                </button>
            </p>
        </form>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Carrega as configurações
    $.ajax({
        url: ajaxurl,
        type: 'GET',
        data: {
            action: 'apwp_get_settings',
            _ajax_nonce: apwp.nonce
        },
        success: function(response) {
            if (response.success) {
                const settings = response.data;
                
                // Preenche os campos com os valores salvos
                for (const section in settings) {
                    for (const key in settings[section]) {
                        const value = settings[section][key];
                        const field = $(`#${key}`);
                        
                        if (field.length) {
                            if (field.is('textarea')) {
                                // Se for um editor do WordPress
                                if (typeof tinymce !== 'undefined' && tinymce.get(key)) {
                                    tinymce.get(key).setContent(value);
                                } else {
                                    field.val(value);
                                }
                            } else {
                                field.val(value);
                            }
                        }
                    }
                }
            }
        }
    });
    
    // Salva as configurações
    $('#apwp-settings-form').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const button = form.find('button[type="submit"]');
        const originalText = button.text();
        
        button.prop('disabled', true).text('Salvando...');
        
        const settings = {};
        form.find('input, textarea').each(function() {
            const field = $(this);
            let value = field.val();
            
            // Se for um editor do WordPress
            if (field.is('textarea') && typeof tinymce !== 'undefined' && tinymce.get(field.attr('id'))) {
                value = tinymce.get(field.attr('id')).getContent();
            }
            
            settings[field.attr('name')] = value;
        });
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'apwp_save_settings',
                _ajax_nonce: apwp.nonce,
                settings: settings
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data);
                } else {
                    alert(response.data);
                }
            },
            error: function() {
                alert('Erro ao salvar configurações.');
            },
            complete: function() {
                button.prop('disabled', false).text(originalText);
            }
        });
    });
});
</script>
