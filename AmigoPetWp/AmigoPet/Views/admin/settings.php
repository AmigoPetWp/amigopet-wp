<?php
/**
 * View de configurações do admin
 */
?>
<div class="wrap">
    <div class="apwp-settings-header">
        <img src="<?php echo AMIGOPET_WP_PLUGIN_URL . 'AmigoPet/assets/images/logo.svg'; ?>" alt="AmigoPet WP" class="apwp-logo">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    </div>

    <div class="apwp-settings notice-info notice is-dismissible" style="display: none;">
        <p></p>
    </div>

    <div class="apwp-settings">
        <nav class="nav-tab-wrapper">
            <a href="#general" class="nav-tab nav-tab-active" data-tab="general">
                <span class="dashicons dashicons-admin-generic"></span>
                <?php _e('Geral', 'amigopet-wp'); ?>
            </a>
            <a href="#api" class="nav-tab" data-tab="api">
                <span class="dashicons dashicons-rest-api"></span>
                <?php _e('API', 'amigopet-wp'); ?>
            </a>
            <a href="#terms" class="nav-tab" data-tab="terms">
                <span class="dashicons dashicons-clipboard"></span>
                <?php _e('Termos', 'amigopet-wp'); ?>
            </a>
            <a href="#workflow" class="nav-tab" data-tab="workflow">
                <span class="dashicons dashicons-workflow"></span>
                <?php _e('Workflow', 'amigopet-wp'); ?>
            </a>
            <a href="#email" class="nav-tab" data-tab="email">
                <span class="dashicons dashicons-email"></span>
                <?php _e('Email', 'amigopet-wp'); ?>
            </a>
            <a href="#qrcode" class="nav-tab" data-tab="qrcode">
                <span class="dashicons dashicons-qr-code"></span>
                <?php _e('QR Code', 'amigopet-wp'); ?>
            </a>
        </nav>

        <form id="apwp-settings-form">
            <div class="apwp-settings-loading" style="display: none;">
                <span class="spinner is-active"></span>
                <p><?php _e('Salvando configurações...', 'amigopet-wp'); ?></p>
            </div>
            <?php wp_nonce_field('apwp_settings_nonce', 'apwp_settings_nonce'); ?>
            <!-- Configurações gerais -->
            <div class="apwp-settings-section active" data-tab="general">
                <div class="apwp-settings-section-header">
                    <span class="dashicons dashicons-admin-generic"></span>
                    <h2><?php _e('Configurações Gerais', 'amigopet-wp'); ?></h2>
                </div>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="organization_name"><?php _e('Nome da Organização', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="organization_name" name="general_organization_name" class="regular-text" value="<?php echo esc_attr(get_option('apwp_organization_name')); ?>" required>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="organization_email"><?php _e('Email da Organização', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <input type="email" id="organization_email" name="general_organization_email" class="regular-text" required>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="organization_phone"><?php _e('Telefone da Organização', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="organization_phone" name="general_organization_phone" class="regular-text" required>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Configurações de API -->
            <div class="apwp-settings-section" data-tab="api">
                <div class="apwp-settings-section-header">
                    <span class="dashicons dashicons-rest-api"></span>
                    <h2><?php _e('Configurações de API', 'amigopet-wp'); ?></h2>
                </div>
                
                <table class="form-table">
                    <!-- Google Maps -->
                    <tr>
                        <th scope="row">
                            <label for="google_maps_key">
                                <?php _e('Chave do Google Maps', 'amigopet-wp'); ?>
                                <span class="apwp-tooltip" data-tooltip="<?php _e('Chave de API do Google Maps necessária para exibir mapas e localização dos pets. Obtenha sua chave em console.cloud.google.com', 'amigopet-wp'); ?>">
                                    <span class="dashicons dashicons-location"></span>
                                </span>
                            </label>
                        </th>
                        <td>
                            <input type="text" id="google_maps_key" name="google_maps_key" class="regular-text" value="<?php echo esc_attr(get_option('apwp_google_maps_key')); ?>">
                            <p class="description">
                                <?php _e('Necessária para funcionalidades de localização.', 'amigopet-wp'); ?>
                            </p>
                        </td>
                    </tr>

                    <!-- PetFinder -->
                    <tr>
                        <th scope="row">
                            <label for="petfinder_key">
                                <?php _e('Chave do PetFinder', 'amigopet-wp'); ?>
                                <span class="apwp-tooltip" data-tooltip="<?php _e('Chave de API do PetFinder para integração com a plataforma. Permite importar/exportar dados de pets. Obtenha em www.petfinder.com/developers', 'amigopet-wp'); ?>">
                                    <span class="dashicons dashicons-pets"></span>
                                </span>
                            </label>
                        </th>
                        <td>
                            <input type="text" id="petfinder_key" name="petfinder_key" class="regular-text" value="<?php echo esc_attr(get_option('apwp_petfinder_key')); ?>">
                            <p class="description">
                                <?php _e('Necessária para integração com o PetFinder.', 'amigopet-wp'); ?>
                            </p>
                        </td>
                    </tr>

                    <!-- Gateway de Pagamento -->
                    <tr>
                        <th scope="row">
                            <label for="payment_gateway_key">
                                <?php _e('Chave do Gateway de Pagamento', 'amigopet-wp'); ?>
                                <span class="apwp-tooltip" data-tooltip="<?php _e('Chave de API do gateway de pagamento para processar doações e pagamentos. Obtenha no painel do seu gateway de pagamento.', 'amigopet-wp'); ?>">
                                    <span class="dashicons dashicons-money-alt"></span>
                                </span>
                            </label>
                        </th>
                        <td>
                            <input type="text" id="payment_gateway_key" name="payment_gateway_key" class="regular-text" value="<?php echo esc_attr(get_option('apwp_payment_gateway_key')); ?>">
                            <p class="description">
                                <?php _e('Chave pública para processar pagamentos.', 'amigopet-wp'); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="payment_gateway_secret">
                                <?php _e('Chave Secreta do Gateway', 'amigopet-wp'); ?>
                                <span class="apwp-tooltip" data-tooltip="<?php _e('Chave secreta do gateway de pagamento para autenticar transações. Mantenha esta chave em segurança e nunca compartilhe.', 'amigopet-wp'); ?>">
                                    <span class="dashicons dashicons-lock"></span>
                                </span>
                            </label>
                        </th>
                        <td>
                            <input type="password" id="payment_gateway_secret" name="payment_gateway_secret" class="regular-text" value="<?php echo esc_attr(get_option('apwp_payment_gateway_secret')); ?>">
                            <p class="description">
                                <?php _e('Chave secreta para processar pagamentos. Nunca compartilhe esta chave.', 'amigopet-wp'); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="payment_gateway_sandbox"><?php _e('Ambiente de Testes', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <input type="checkbox" id="payment_gateway_sandbox" name="payment_gateway_sandbox" value="1" <?php checked(get_option('apwp_payment_gateway_sandbox', true)); ?>>
                            <label for="payment_gateway_sandbox"><?php _e('Usar ambiente de testes (sandbox)', 'amigopet-wp'); ?></label>
                            <p class="description">
                                <?php _e('Ative para testar pagamentos sem cobrar cartões reais.', 'amigopet-wp'); ?>
                            </p>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Termos e Condições -->
            <div class="apwp-settings-section" data-tab="terms">
                <div class="apwp-settings-section-header">
                    <span class="dashicons dashicons-clipboard"></span>
                    <h2><?php _e('Templates de Termos', 'amigopet-wp'); ?></h2>
                </div>

                <div class="apwp-terms-tabs">
                    <nav class="nav-tab-wrapper">
                        <?php foreach ($terms_types as $type => $term) : ?>
                            <a href="#term-<?php echo esc_attr($type); ?>" 
                               class="nav-tab <?php echo $type === 'adoption' ? 'nav-tab-active' : ''; ?>" 
                               data-tab="term-<?php echo esc_attr($type); ?>">
                                <?php echo esc_html($term['title']); ?>
                                <span class="apwp-tooltip" data-tooltip="<?php echo esc_attr($term['description']); ?>">
                                    <span class="dashicons dashicons-info-outline"></span>
                                </span>
                            </a>
                        <?php endforeach; ?>
                    </nav>

                    <?php 
                    $saved_templates = get_option('apwp_terms_templates', []);
                    foreach ($terms_types as $type => $term) : 
                        $template_data = isset($templates[$type][0]) ? $templates[$type][0] : null;
                    ?>
                        <div class="apwp-terms-content" data-tab="<?php echo esc_attr($type); ?>" 
                             style="<?php echo $type === 'adoption' ? '' : 'display: none;'; ?>">
                            <div class="apwp-placeholders-list">
                                <h4><?php _e('Placeholders Disponíveis:', 'amigopet-wp'); ?></h4>
                                <div class="apwp-placeholders-grid">
                                    <?php foreach ($term['placeholders'] as $placeholder => $description) : ?>
                                        <div class="apwp-placeholder-item">
                                            <code><?php echo esc_html($placeholder); ?></code>
                                            <span class="description"><?php echo esc_html($description); ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <?php 
                            $template_content = '';
                            $template_title = '';
                            $template_description = '';
                            if (isset($templates[$type]) && !empty($templates[$type])) {
                                // Pega o primeiro template do tipo (mais recente)
                                $template = $templates[$type][0];
                                $template_content = $template->getContent();
                                $template_title = $template->getTitle();
                                $template_description = $template->getDescription();
                            }
                            ?>

                            <table class="form-table">
                                <tr>
                                    <th scope="row">
                                        <label for="term_title_<?php echo esc_attr($type); ?>">
                                            <?php _e('Título', 'amigopet-wp'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input type="text" 
                                               id="term_title_<?php echo esc_attr($type); ?>" 
                                               name="templates[<?php echo esc_attr($type); ?>][title]" 
                                               class="regular-text" 
                                               value="<?php echo esc_attr($template_title); ?>" 
                                               required>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="term_description_<?php echo esc_attr($type); ?>">
                                            <?php _e('Descrição', 'amigopet-wp'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <textarea id="term_description_<?php echo esc_attr($type); ?>" 
                                                  name="templates[<?php echo esc_attr($type); ?>][description]" 
                                                  class="regular-text" 
                                                  rows="3"><?php echo esc_textarea($template_description); ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="term_content_<?php echo esc_attr($type); ?>">
                                            <?php _e('Conteúdo do Termo', 'amigopet-wp'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <?php 
                                        $editor_id = 'term_content_' . esc_attr($type);
                                        $editor_name = "templates[{$type}][content]";
                                        $editor_content = $template_content;
                                        
                                        wp_editor(
                                            $editor_content,
                                            $editor_id,
                                            [
                                                'textarea_name' => $editor_name,
                                                'textarea_rows' => 10,
                                                'media_buttons' => false,
                                                'teeny' => true,
                                                'quicktags' => true
                                            ]
                                        );
                                        ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Workflow -->
            <div class="apwp-settings-section" data-tab="workflow">
                <div class="apwp-settings-section-header">
                    <span class="dashicons dashicons-workflow"></span>
                    <h2><?php _e('Workflow', 'amigopet-wp'); ?></h2>
                </div>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="adoption_workflow"><?php _e('Processo de Adoção', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <select id="adoption_workflow" name="workflow_adoption[]" multiple class="regular-text">
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
                            <select id="volunteer_workflow" name="workflow_volunteer[]" multiple class="regular-text">
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
                            <select id="donation_workflow" name="workflow_donation[]" multiple class="regular-text">
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
            <div class="apwp-settings-section" data-tab="email">
                <div class="apwp-settings-section-header">
                    <span class="dashicons dashicons-email"></span>
                    <h2><?php _e('Configurações de Email', 'amigopet-wp'); ?></h2>
                </div>
                
                <table class="form-table">
                    <!-- SMTP -->
                    <tr>
                        <th scope="row" colspan="2">
                            <h3><?php _e('Configurações SMTP', 'amigopet-wp'); ?></h3>
                        </th>
                    </tr>
                    <?php
                    $smtp_settings = get_option('apwp_smtp_settings', []);
                    ?>
                    <tr>
                        <th scope="row">
                            <label for="smtp_host"><?php _e('Servidor SMTP', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="smtp_host" name="smtp_settings[host]" class="regular-text" value="<?php echo esc_attr($smtp_settings['host'] ?? ''); ?>">
                            <p class="description"><?php _e('Ex: smtp.gmail.com', 'amigopet-wp'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="smtp_port"><?php _e('Porta SMTP', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <input type="number" id="smtp_port" name="smtp_settings[port]" class="small-text" value="<?php echo esc_attr($smtp_settings['port'] ?? ''); ?>">
                            <p class="description"><?php _e('Ex: 587 para TLS, 465 para SSL', 'amigopet-wp'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="smtp_secure"><?php _e('Segurança', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <select id="smtp_secure" name="smtp_settings[secure]" class="regular-text">
                                <option value="tls" <?php selected($smtp_settings['secure'] ?? 'tls', 'tls'); ?>><?php _e('TLS', 'amigopet-wp'); ?></option>
                                <option value="ssl" <?php selected($smtp_settings['secure'] ?? 'tls', 'ssl'); ?>><?php _e('SSL', 'amigopet-wp'); ?></option>
                                <option value="" <?php selected($smtp_settings['secure'] ?? 'tls', ''); ?>><?php _e('Nenhuma', 'amigopet-wp'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="smtp_auth"><?php _e('Requer Autenticação', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <input type="checkbox" id="smtp_auth" name="smtp_settings[auth]" value="1" <?php checked($smtp_settings['auth'] ?? true); ?>>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="smtp_username"><?php _e('Usuário SMTP', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="smtp_username" name="smtp_settings[username]" class="regular-text" value="<?php echo esc_attr($smtp_settings['username'] ?? ''); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="smtp_password"><?php _e('Senha SMTP', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <input type="password" id="smtp_password" name="smtp_settings[password]" class="regular-text" value="<?php echo esc_attr($smtp_settings['password'] ?? ''); ?>">
                        </td>
                    </tr>

                    <!-- Templates de Email -->
                    <tr>
                        <th scope="row" colspan="2">
                            <h3><?php _e('Templates de Email', 'amigopet-wp'); ?></h3>
                        </th>
                    </tr>
                    <?php
                    $email_templates = get_option('apwp_email_templates', []);
                    $templates = [
                        'adoption_approved' => [
                            'title' => __('Adoção Aprovada', 'amigopet-wp'),
                            'vars' => '{adopter_name}, {pet_name}'
                        ],
                        'adoption_rejected' => [
                            'title' => __('Adoção Rejeitada', 'amigopet-wp'),
                            'vars' => '{adopter_name}, {pet_name}'
                        ],
                        'donation_received' => [
                            'title' => __('Doação Recebida', 'amigopet-wp'),
                            'vars' => '{donor_name}, {donation_amount}'
                        ],
                        'volunteer_application' => [
                            'title' => __('Inscrição de Voluntário', 'amigopet-wp'),
                            'vars' => '{volunteer_name}'
                        ]
                    ];

                    foreach ($templates as $key => $template) :
                        $current = $email_templates[$key] ?? [];
                    ?>
                        <tr>
                            <th scope="row" colspan="2">
                                <h4><?php echo $template['title']; ?></h4>
                                <p class="description"><?php _e('Variáveis disponíveis:', 'amigopet-wp'); ?> <?php echo $template['vars']; ?></p>
                            </th>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="<?php echo $key; ?>_subject"><?php _e('Assunto', 'amigopet-wp'); ?></label>
                            </th>
                            <td>
                                <input type="text" id="<?php echo $key; ?>_subject" name="email_templates[<?php echo $key; ?>][subject]" class="large-text" value="<?php echo esc_attr($current['subject'] ?? ''); ?>">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="<?php echo $key; ?>_body"><?php _e('Conteúdo', 'amigopet-wp'); ?></label>
                            </th>
                            <td>
                                <?php wp_editor($current['body'] ?? '', $key . '_body', [
                                    'textarea_name' => "email_templates[{$key}][body]",
                                    'textarea_rows' => 10,
                                    'media_buttons' => false
                                ]); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>_e('Cabeçalho do Template', 'amigopet-wp'); ?></label>
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

            <!-- Configurações de QR Code -->
            <div class="apwp-settings-section" data-tab="qrcode">
                <div class="apwp-settings-section-header">
                    <span class="dashicons dashicons-qr-code"></span>
                    <h2><?php _e('Configurações de QR Code', 'amigopet-wp'); ?></h2>
                </div>
                
                <table class="form-table">
                    <?php
                    $qrcode_settings = get_option('apwp_qrcode_settings', []);
                    ?>
                    <tr>
                        <th scope="row">
                            <label for="qrcode_size"><?php _e('Tamanho', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <input type="number" id="qrcode_size" name="qrcode_settings[size]" class="small-text" value="<?php echo esc_attr($qrcode_settings['size'] ?? 300); ?>" min="100" max="1000" step="10">
                            <p class="description"><?php _e('Tamanho em pixels do QR Code (100-1000)', 'amigopet-wp'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="qrcode_margin"><?php _e('Margem', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <input type="number" id="qrcode_margin" name="qrcode_settings[margin]" class="small-text" value="<?php echo esc_attr($qrcode_settings['margin'] ?? 10); ?>" min="0" max="50">
                            <p class="description"><?php _e('Margem em pixels ao redor do QR Code (0-50)', 'amigopet-wp'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="qrcode_foreground_color"><?php _e('Cor do QR Code', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <input type="color" id="qrcode_foreground_color" name="qrcode_settings[foreground_color]" value="<?php echo esc_attr($qrcode_settings['foreground_color'] ?? '#000000'); ?>">
                            <p class="description"><?php _e('Cor dos módulos do QR Code', 'amigopet-wp'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="qrcode_background_color"><?php _e('Cor de Fundo', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <input type="color" id="qrcode_background_color" name="qrcode_settings[background_color]" value="<?php echo esc_attr($qrcode_settings['background_color'] ?? '#FFFFFF'); ?>">
                            <p class="description"><?php _e('Cor de fundo do QR Code', 'amigopet-wp'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="qrcode_error_correction"><?php _e('Nível de Correção', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <select id="qrcode_error_correction" name="qrcode_settings[error_correction]" class="regular-text">
                                <option value="L" <?php selected($qrcode_settings['error_correction'] ?? 'M', 'L'); ?>><?php _e('Baixo (7%)', 'amigopet-wp'); ?></option>
                                <option value="M" <?php selected($qrcode_settings['error_correction'] ?? 'M', 'M'); ?>><?php _e('Médio (15%)', 'amigopet-wp'); ?></option>
                                <option value="Q" <?php selected($qrcode_settings['error_correction'] ?? 'M', 'Q'); ?>><?php _e('Alto (25%)', 'amigopet-wp'); ?></option>
                                <option value="H" <?php selected($qrcode_settings['error_correction'] ?? 'M', 'H'); ?>><?php _e('Máximo (30%)', 'amigopet-wp'); ?></option>
                            </select>
                            <p class="description"><?php _e('Nível de correção de erros. Quanto maior, mais resistente a danos.', 'amigopet-wp'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="qrcode_logo_enabled"><?php _e('Incluir Logo', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <input type="checkbox" id="qrcode_logo_enabled" name="qrcode_settings[logo_enabled]" value="1" <?php checked($qrcode_settings['logo_enabled'] ?? true); ?>>
                            <label for="qrcode_logo_enabled"><?php _e('Adicionar logo no centro do QR Code', 'amigopet-wp'); ?></label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="qrcode_logo_size"><?php _e('Tamanho do Logo', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <input type="number" id="qrcode_logo_size" name="qrcode_settings[logo_size]" class="small-text" value="<?php echo esc_attr($qrcode_settings['logo_size'] ?? 50); ?>" min="20" max="200" step="5">
                            <p class="description"><?php _e('Tamanho em pixels do logo (20-200)', 'amigopet-wp'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="qrcode_logo_path"><?php _e('Logo', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <div class="qrcode-logo-preview">
                                <?php if (!empty($qrcode_settings['logo_path'])) : ?>
                                    <img src="<?php echo esc_url($qrcode_settings['logo_path']); ?>" alt="Logo" style="max-width: 100px;">
                                <?php endif; ?>
                            </div>
                            <input type="hidden" id="qrcode_logo_path" name="qrcode_settings[logo_path]" value="<?php echo esc_attr($qrcode_settings['logo_path'] ?? ''); ?>">
                            <button type="button" class="button qrcode-upload-logo"><?php _e('Escolher Logo', 'amigopet-wp'); ?></button>
                            <button type="button" class="button qrcode-remove-logo" <?php echo empty($qrcode_settings['logo_path']) ? 'style="display:none;"' : ''; ?>><?php _e('Remover Logo', 'amigopet-wp'); ?></button>
                            <p class="description"><?php _e('Logo para adicionar no centro do QR Code. Use uma imagem PNG transparente.', 'amigopet-wp'); ?></p>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="apwp-settings-floating-save">
                <button type="submit" class="button button-primary">
                    <span class="dashicons dashicons-saved"></span>
                    <?php _e('Salvar Configurações', 'amigopet-wp'); ?>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.apwp-settings-header {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
    padding: 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.apwp-settings-header .apwp-logo {
    width: 50px;
    height: auto;
    margin-right: 15px;
}

.apwp-settings-header h1 {
    margin: 0;
    padding: 0;
    color: #1d2327;
}

/* Tooltips */
.apwp-tooltip {
    position: relative;
    display: inline-block;
    margin-left: 5px;
    cursor: help;
}

.apwp-tooltip .dashicons {
    color: #646970;
    font-size: 16px;
    width: 16px;
    height: 16px;
    vertical-align: text-bottom;
}

.apwp-tooltip:hover::after {
    content: attr(data-tooltip);
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    bottom: 100%;
    margin-bottom: 5px;
    background: #1d2327;
    color: #fff;
    padding: 8px 12px;
    border-radius: 4px;
    font-size: 12px;
    line-height: 1.4;
    white-space: nowrap;
    z-index: 9999;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.apwp-tooltip:hover::before {
    content: '';
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    bottom: 100%;
    border: 5px solid transparent;
    border-top-color: #1d2327;
    margin-bottom: -5px;
    z-index: 9999;
}

/* Termos e Condições */
.apwp-terms-tabs {
    margin-top: 20px;
}

.apwp-terms-tabs .nav-tab-wrapper {
    margin-bottom: 20px;
    border-bottom: 1px solid #c3c4c7;
}

.apwp-terms-tabs .nav-tab {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    margin-bottom: -1px;
}

.apwp-terms-tabs .nav-tab .apwp-tooltip {
    margin-left: 2px;
}

.apwp-term-content {
    display: none;
}

.apwp-term-content.active {
    display: block;
}

.apwp-term-model {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 20px;
    margin-bottom: 30px;
}

.apwp-term-model h3 {
    margin-top: 0;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.apwp-placeholders-list {
    background: #f9f9f9;
    border: 1px solid #eee;
    border-radius: 4px;
    padding: 15px;
    margin-bottom: 20px;
}

.apwp-placeholders-list h4 {
    margin-top: 0;
    margin-bottom: 15px;
    color: #1d2327;
}

.apwp-placeholders-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 10px;
}

.apwp-placeholder-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.apwp-placeholder-item code {
    background: #e9e9e9;
    padding: 4px 8px;
    border-radius: 3px;
    font-size: 12px;
    display: inline-block;
    width: fit-content;
}

.apwp-placeholder-item .description {
    color: #646970;
    font-size: 12px;
}

/* QR Code */
.qrcode-logo-preview {
    margin-bottom: 10px;
}

.qrcode-logo-preview img {
    border: 1px solid #ddd;
    padding: 5px;
    border-radius: 4px;
}

.qrcode-remove-logo {
    margin-left: 10px;
}

.nav-tab-wrapper {
    margin-bottom: 20px;
    padding-top: 0;
}

.nav-tab {
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 10px 15px;
    font-size: 14px;
    transition: all 0.2s ease;
}

.nav-tab .dashicons {
    font-size: 16px;
    width: 16px;
    height: 16px;
}

.nav-tab-active {
    background: white;
    border-bottom-color: white;
}

.apwp-settings-section {
    display: none;
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

/* Mostra a primeira aba por padrão */
.apwp-settings-section:first-of-type {
    display: block;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.apwp-settings-section-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid #f0f0f1;
}

.apwp-settings-section-header .dashicons {
    font-size: 24px;
    width: 24px;
    height: 24px;
    color: #2271b1;
}

.apwp-settings-section-header h2 {
    margin: 0;
    padding: 0;
    color: #1d2327;
}

.form-table th {
    padding: 20px 10px 20px 0;
    width: 200px;
}

.form-table td {
    padding: 15px 10px;
}

.regular-text {
    width: 100%;
    max-width: 400px;
}

.description {
    margin-top: 5px;
    color: #646970;
}

.apwp-settings-loading {
    position: fixed;
    top: 32px;
    left: 160px;
    right: 0;
    bottom: 0;
    background: rgba(255,255,255,0.8);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.apwp-settings-loading .spinner {
    float: none;
    margin: 0 0 10px;
}

.apwp-settings-floating-save {
    position: fixed;
    bottom: 30px;
    right: 30px;
    z-index: 9998;
    background: white;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transition: transform 0.2s ease;
}

.apwp-terms-tabs .nav-tab-wrapper {
    margin-bottom: 20px;
    border-bottom: 1px solid #c3c4c7;
}

.apwp-terms-tabs .nav-tab {
    margin-bottom: -1px;
    padding: 8px 15px;
    font-size: 14px;
    line-height: 1.71428571;
}

.apwp-terms-tabs .nav-tab-active {
    border-bottom: 1px solid #f0f0f1;
    background: #f0f0f1;
}

.apwp-terms-content {
    background: #fff;
    padding: 20px;
    border: 1px solid #c3c4c7;
    border-radius: 4px;
}

.apwp-placeholders-list {
    margin-bottom: 20px;
    padding: 15px;
    background: #f9f9f9;
    border: 1px solid #e5e5e5;
    border-radius: 4px;
}

.apwp-placeholders-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 10px;
    margin-top: 10px;
}

.apwp-placeholder-item {
    background: #fff;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 3px;
    font-size: 13px;
}

.apwp-placeholder-item code {
    display: block;
    margin-bottom: 4px;
    color: #007cba;
}

.apwp-placeholder-item .description {
    display: block;
    color: #666;
    font-size: 12px;
    line-height: 1.4;
}

/* Estilos para os editores */
.apwp-terms-content .wp-editor-wrap {
    margin-top: 10px;
}

.apwp-terms-content .wp-editor-container {
    border: 1px solid #ddd;
}

.apwp-terms-content .wp-editor-area {
    min-height: 300px;
}

.apwp-terms-content .wp-editor-tools {
    background: #f7f7f7;
    border: 1px solid #ddd;
    border-bottom: none;
    padding: 5px 10px;
}

.apwp-term-editor {
    margin: 20px 0;
}
}

.apwp-settings-floating-save:hover {
    transform: translateY(-2px);
}

.apwp-settings-floating-save .button {
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 5px 15px;
    height: auto;
    font-size: 14px;
}

.apwp-settings-floating-save .dashicons {
    font-size: 16px;
    width: 16px;
    height: 16px;
}

.regular-text.error {
    border-color: #d63638;
    box-shadow: 0 0 0 1px #d63638;
}

.regular-text.success {
    border-color: #00a32a;
    box-shadow: 0 0 0 1px #00a32a;
}
</style>


<script>
jQuery(document).ready(function($) {
    // Navegação das abas de termos
    $('.apwp-terms-tabs .nav-tab').on('click', function(e) {
        e.preventDefault();
        var tab = $(this).attr('href').replace('#', '');

        // Atualiza classes ativas
        $('.apwp-terms-tabs .nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');

        // Mostra conteúdo da aba
        $('.apwp-terms-content').hide();
        $('.apwp-terms-content[data-tab="' + tab + '"]').fadeIn(300);

        // Atualiza o editor TinyMCE se existir
        if (typeof tinymce !== 'undefined') {
            var editorId = 'term_content_' + tab;
            var editor = tinymce.get(editorId);
            if (editor) {
                editor.focus();
            }
        }
    });

    // Upload de logo para QR Code
    var qrcodeMediaFrame;
    $('.qrcode-upload-logo').on('click', function(e) {
        e.preventDefault();

        if (qrcodeMediaFrame) {
            qrcodeMediaFrame.open();
            return;
        }

        qrcodeMediaFrame = wp.media({
            title: '<?php _e('Escolher Logo para QR Code', 'amigopet-wp'); ?>',
            button: {
                text: '<?php _e('Usar como Logo', 'amigopet-wp'); ?>'
            },
            multiple: false,
            library: {
                type: 'image'
            }
        });

        qrcodeMediaFrame.on('select', function() {
            var attachment = qrcodeMediaFrame.state().get('selection').first().toJSON();
            $('#qrcode_logo_path').val(attachment.url);
            $('.qrcode-logo-preview').html('<img src="' + attachment.url + '" alt="Logo" style="max-width: 100px;">');
            $('.qrcode-remove-logo').show();
        });

        qrcodeMediaFrame.open();
    });

    $('.qrcode-remove-logo').on('click', function(e) {
        e.preventDefault();
        $('#qrcode_logo_path').val('');
        $('.qrcode-logo-preview').empty();
        $(this).hide();
    });

    // Navegação por tabs
    function switchTab(tab) {
        // Atualiza as classes das tabs
        $('.nav-tab').removeClass('nav-tab-active');
        $('.nav-tab[data-tab="' + tab + '"]').addClass('nav-tab-active');
        
        // Atualiza a visibilidade das seções
        $('.apwp-settings-section').hide();
        $('.apwp-settings-section[data-tab="' + tab + '"]').fadeIn(300);

        // Salva a tab ativa
        localStorage.setItem('apwp_active_tab', tab);
    }

    // Handler de clique nas tabs
    $('.nav-tab').on('click', function(e) {
        e.preventDefault();
        var tab = $(this).attr('data-tab');
        if (tab) {
            switchTab(tab);
        }
    });

    // Restaura a última tab ativa ou usa a primeira
    var lastTab = localStorage.getItem('apwp_active_tab') || 'general';
    switchTab(lastTab);

    // Inicializa Select2 nos campos de workflow
    if (typeof $.fn.select2 !== 'undefined') {
        $('#adoption_workflow, #volunteer_workflow, #donation_workflow').select2({
            placeholder: '<?php _e("Selecione os passos", "amigopet-wp"); ?>',
            allowClear: true,
            theme: 'default',
            width: '100%',
            language: 'pt-BR'
        });
    }

    // Validação em tempo real
    $('#organization_email').on('input', function() {
        var email = $(this).val();
        var isValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        $(this).toggleClass('error', !isValid);
        $(this).toggleClass('success', isValid && email.length > 0);
    });

    $('#organization_phone').on('input', function() {
        var phone = $(this).val();
        var isValid = /^\(?\d{2}\)?[\s-]?\d{4,5}-?\d{4}$/.test(phone);
        $(this).toggleClass('error', !isValid);
        $(this).toggleClass('success', isValid && phone.length > 0);
    });

    // Validação de campos obrigatórios
    $('input[required]').on('input', function() {
        var value = $(this).val();
        $(this).toggleClass('error', !value);
        $(this).toggleClass('success', value.length > 0);
    });

    // Handler para salvar configurações com feedback visual
    $('#apwp-settings-form').on('submit', function(e) {
        e.preventDefault();

        // Atualiza os editores TinyMCE antes de salvar
        if (typeof tinymce !== 'undefined') {
            tinymce.triggerSave();
        }

        // Valida campos obrigatórios
        var hasErrors = false;
        $('input[required]').each(function() {
            if (!$(this).val()) {
                $(this).addClass('error');
                hasErrors = true;
            }
        });

        if (hasErrors) {
            var notice = $('.apwp-settings.notice');
            notice.removeClass('notice-success').addClass('notice-error')
                .find('p').text('<?php _e("Por favor, preencha todos os campos obrigatórios.", "amigopet-wp"); ?>');
            notice.fadeIn();
            return;
        }

        // Mostra o loading
        $('.apwp-settings-loading').fadeIn();

        // Prepara os dados do formulário
        var formData = $(this).serializeArray();
        var data = {
            action: 'apwp_save_settings'
        };

        // Converte o array em objeto
        $.each(formData, function(i, field) {
            data[field.name] = field.value;
        });

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: data,
            success: function(response) {
                var notice = $('.apwp-settings.notice');
                var message = '';
                
                if (response.success) {
                    notice.removeClass('notice-error').addClass('notice-success');
                    message = response.data || apwp.i18n.saved;
                } else {
                    notice.removeClass('notice-success').addClass('notice-error');
                    message = (response.data && response.data.message) ? response.data.message : apwp.i18n.error;
                }

                notice.find('p').text(message);
                notice.fadeIn();

                if (response.success) {
                    // Atualiza os valores dos campos
                    for (var pair of formData.entries()) {
                        $('#' + pair[0]).val(pair[1]);
                    }

                    setTimeout(function() {
                        notice.fadeOut();
                    }, 3000);
                }
            },
            error: function() {
                var notice = $('.apwp-settings.notice');
                notice.removeClass('notice-success').addClass('notice-error')
                    .find('p').text(apwp.i18n.error);
                notice.fadeIn();
            },
            complete: function() {
                $('.apwp-settings-loading').fadeOut();
            }
        });
    });
});
</script>



        
</script>
