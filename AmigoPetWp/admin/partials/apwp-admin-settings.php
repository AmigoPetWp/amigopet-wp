<?php
/**
 * Template para a página de configurações do plugin
 *
 * @link       https://github.com/wendelmax/amigopet-wp
 * @since      1.0.0
 *
 * @package    AmigoPet_Wp
 * @subpackage AmigoPet_Wp/admin/partials
 */

// Se este arquivo for chamado diretamente, aborte.
if (!defined('WPINC')) {
    die;
}

// Obtém as opções salvas
$options = get_option('apwp_options', array());
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Configurações do AmigoPet WP', 'amigopet-wp'); ?></h1>
    
    <!-- Menu de navegação de Configurações -->
    <div class="nav-tab-wrapper">
        <a href="<?php echo admin_url('admin.php?page=amigopet-wp-settings'); ?>" class="nav-tab <?php echo (!isset($_GET['tab']) || $_GET['tab'] == 'general') ? 'nav-tab-active' : ''; ?>">
            <?php _e('Geral', 'amigopet-wp'); ?>
        </a>
        <a href="<?php echo admin_url('admin.php?page=amigopet-wp-settings&tab=adoption'); ?>" class="nav-tab <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'adoption') ? 'nav-tab-active' : ''; ?>">
            <?php _e('Adoção', 'amigopet-wp'); ?>
        </a>
        <a href="<?php echo admin_url('admin.php?page=amigopet-wp-settings&tab=email'); ?>" class="nav-tab <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'email') ? 'nav-tab-active' : ''; ?>">
            <?php _e('E-mail', 'amigopet-wp'); ?>
        </a>
        <a href="<?php echo admin_url('admin.php?page=amigopet-wp-settings&tab=integrations'); ?>" class="nav-tab <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'integrations') ? 'nav-tab-active' : ''; ?>">
            <?php _e('Integrações', 'amigopet-wp'); ?>
        </a>
    </div>

    <?php
    // Renderiza a página correta baseada na tab selecionada
    $current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'general';
    switch ($current_tab) {
        case 'adoption':
            // Configurações de adoção
            ?>
            <form method="post" action="options.php">
                <?php
                settings_fields('apwp_adoption_settings');
                do_settings_sections('apwp_adoption_settings');
                submit_button();
                ?>
            </form>
            <?php
            break;
        case 'email':
            // Configurações de e-mail
            ?>
            <form method="post" action="options.php">
                <?php
                settings_fields('apwp_email_settings');
                do_settings_sections('apwp_email_settings');
                submit_button();
                ?>
            </form>
            <?php
            break;
        case 'integrations':
            // Configurações de integrações
            ?>
            <form method="post" action="options.php">
                <?php
                settings_fields('apwp_integrations_settings');
                do_settings_sections('apwp_integrations_settings');
                submit_button();
                ?>
            </form>
            <?php
            break;
        default:
            // Configurações gerais
            ?>
            <div class="apwp-settings-wrapper">
                <form method="post" action="options.php">
                    <?php
                    settings_fields('apwp_options');
                    do_settings_sections('apwp_settings');
                    ?>
                    
                    <div class="nav-tab-wrapper">
                        <a href="#general" class="nav-tab nav-tab-active"><?php _e('Geral', 'amigopet-wp'); ?></a>
                        <a href="#adoption" class="nav-tab"><?php _e('Adoção', 'amigopet-wp'); ?></a>
                        <a href="#email" class="nav-tab"><?php _e('Email', 'amigopet-wp'); ?></a>
                        <a href="#contract" class="nav-tab"><?php _e('Contratos', 'amigopet-wp'); ?></a>
                    </div>
                    
                    <!-- Configurações Gerais -->
                    <div id="general" class="apwp-settings-section active">
                        <h2><?php _e('Configurações Gerais', 'amigopet-wp'); ?></h2>
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="apwp_org_name"><?php _e('Nome da Organização', 'amigopet-wp'); ?></label>
                                </th>
                                <td>
                                    <input type="text" id="apwp_org_name" name="apwp_options[org_name]" class="regular-text" 
                                        value="<?php echo esc_attr($options['org_name'] ?? ''); ?>">
                                    <p class="description"><?php _e('Nome completo da sua organização de adoção', 'amigopet-wp'); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="apwp_org_email"><?php _e('Email da Organização', 'amigopet-wp'); ?></label>
                                </th>
                                <td>
                                    <input type="email" id="apwp_org_email" name="apwp_options[org_email]" class="regular-text" 
                                        value="<?php echo esc_attr($options['org_email'] ?? ''); ?>">
                                    <p class="description"><?php _e('Email principal para receber notificações', 'amigopet-wp'); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="apwp_org_phone"><?php _e('Telefone', 'amigopet-wp'); ?></label>
                                </th>
                                <td>
                                    <input type="text" id="apwp_org_phone" name="apwp_options[org_phone]" class="regular-text" 
                                        value="<?php echo esc_attr($options['org_phone'] ?? ''); ?>">
                                    <p class="description"><?php _e('Telefone principal para contato', 'amigopet-wp'); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="apwp_org_address"><?php _e('Endereço', 'amigopet-wp'); ?></label>
                                </th>
                                <td>
                                    <textarea id="apwp_org_address" name="apwp_options[org_address]" class="regular-text" rows="3"><?php 
                                        echo esc_textarea($options['org_address'] ?? ''); 
                                    ?></textarea>
                                    <p class="description"><?php _e('Endereço completo da organização', 'amigopet-wp'); ?></p>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- Configurações de Adoção -->
                    <div id="adoption" class="apwp-settings-section">
                        <h2><?php _e('Configurações de Adoção', 'amigopet-wp'); ?></h2>
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="apwp_adoption_approval"><?php _e('Aprovação de Adoção', 'amigopet-wp'); ?></label>
                                </th>
                                <td>
                                    <select id="apwp_adoption_approval" name="apwp_options[adoption_approval]">
                                        <option value="auto" <?php selected($options['adoption_approval'] ?? '', 'auto'); ?>>
                                            <?php _e('Automática', 'amigopet-wp'); ?>
                                        </option>
                                        <option value="manual" <?php selected($options['adoption_approval'] ?? '', 'manual'); ?>>
                                            <?php _e('Manual', 'amigopet-wp'); ?>
                                        </option>
                                    </select>
                                    <p class="description">
                                        <?php _e('Define como as solicitações de adoção serão aprovadas', 'amigopet-wp'); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="apwp_adoption_requirements"><?php _e('Requisitos para Adoção', 'amigopet-wp'); ?></label>
                                </th>
                                <td>
                                    <textarea id="apwp_adoption_requirements" name="apwp_options[adoption_requirements]" class="large-text" rows="5"><?php 
                                        echo esc_textarea($options['adoption_requirements'] ?? ''); 
                                    ?></textarea>
                                    <p class="description">
                                        <?php _e('Lista de requisitos que os adotantes devem atender', 'amigopet-wp'); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="apwp_adoption_terms"><?php _e('Termos de Adoção', 'amigopet-wp'); ?></label>
                                </th>
                                <td>
                                    <textarea id="apwp_adoption_terms" name="apwp_options[adoption_terms]" class="large-text" rows="5"><?php 
                                        echo esc_textarea($options['adoption_terms'] ?? ''); 
                                    ?></textarea>
                                    <p class="description">
                                        <?php _e('Termos e condições que os adotantes devem aceitar', 'amigopet-wp'); ?>
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- Configurações de Email -->
                    <div id="email" class="apwp-settings-section">
                        <h2><?php _e('Configurações de Email', 'amigopet-wp'); ?></h2>
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="apwp_email_notifications"><?php _e('Notificações por Email', 'amigopet-wp'); ?></label>
                                </th>
                                <td>
                                    <input type="checkbox" id="apwp_email_notifications" name="apwp_options[email_notifications]" value="1"
                                        <?php checked($options['email_notifications'] ?? '', 1); ?>>
                                    <span class="description">
                                        <?php _e('Enviar notificações por email para adotantes e administradores', 'amigopet-wp'); ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="apwp_email_from_name"><?php _e('Nome do Remetente', 'amigopet-wp'); ?></label>
                                </th>
                                <td>
                                    <input type="text" id="apwp_email_from_name" name="apwp_options[email_from_name]" class="regular-text" 
                                        value="<?php echo esc_attr($options['email_from_name'] ?? ''); ?>">
                                    <p class="description">
                                        <?php _e('Nome que aparecerá como remetente dos emails', 'amigopet-wp'); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="apwp_email_footer"><?php _e('Rodapé do Email', 'amigopet-wp'); ?></label>
                                </th>
                                <td>
                                    <textarea id="apwp_email_footer" name="apwp_options[email_footer]" class="large-text" rows="3"><?php 
                                        echo esc_textarea($options['email_footer'] ?? ''); 
                                    ?></textarea>
                                    <p class="description">
                                        <?php _e('Texto que aparecerá no rodapé de todos os emails', 'amigopet-wp'); ?>
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- Configurações de Contrato -->
                    <div id="contract" class="apwp-settings-section">
                        <h2><?php _e('Configurações de Contrato', 'amigopet-wp'); ?></h2>
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="apwp_contract_prefix"><?php _e('Prefixo do Contrato', 'amigopet-wp'); ?></label>
                                </th>
                                <td>
                                    <input type="text" id="apwp_contract_prefix" name="apwp_options[contract_prefix]" class="regular-text" 
                                        value="<?php echo esc_attr($options['contract_prefix'] ?? 'ADOC'); ?>">
                                    <p class="description">
                                        <?php _e('Prefixo usado na numeração dos contratos (ex: ADOC)', 'amigopet-wp'); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="apwp_contract_terms"><?php _e('Termos Padrão do Contrato', 'amigopet-wp'); ?></label>
                                </th>
                                <td>
                                    <textarea id="apwp_contract_terms" name="apwp_options[contract_terms]" class="large-text" rows="10"><?php 
                                        echo esc_textarea($options['contract_terms'] ?? ''); 
                                    ?></textarea>
                                    <p class="description">
                                        <?php _e('Termos padrão que serão incluídos em todos os contratos', 'amigopet-wp'); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="apwp_contract_validity"><?php _e('Validade do Contrato', 'amigopet-wp'); ?></label>
                                </th>
                                <td>
                                    <input type="number" id="apwp_contract_validity" name="apwp_options[contract_validity]" class="small-text" 
                                        value="<?php echo esc_attr($options['contract_validity'] ?? '30'); ?>">
                                    <span class="description">
                                        <?php _e('Dias para assinatura do contrato após a aprovação', 'amigopet-wp'); ?>
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <?php submit_button(); ?>
                </form>
            </div>
            <?php
    }
    ?>
</div>

<style>
.apwp-settings-wrapper {
    margin-top: 1em;
}

.apwp-settings-section {
    display: none;
    margin-top: 1em;
    padding: 1em;
    background: #fff;
    border: 1px solid #ccd0d4;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}

.apwp-settings-section.active {
    display: block;
}

.nav-tab-wrapper {
    margin-bottom: 1em;
}

.form-table th {
    width: 200px;
}

.form-table td {
    position: relative;
}

.form-table .description {
    color: #666;
    font-style: italic;
    margin-top: 4px;
    display: block;
}

input[type="text"],
input[type="email"],
input[type="number"],
textarea {
    width: 100%;
    max-width: 500px;
}

textarea.large-text {
    max-width: 800px;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Tabs functionality
    $('.nav-tab').on('click', function(e) {
        e.preventDefault();
        
        // Update active tab
        $('.nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');
        
        // Show corresponding section
        var target = $(this).attr('href').substring(1);
        $('.apwp-settings-section').removeClass('active');
        $('#' + target).addClass('active');
    });

    // Save active tab in session storage
    var activeTab = sessionStorage.getItem('apwpActiveSettingsTab');
    if (activeTab) {
        $('.nav-tab[href="#' + activeTab + '"]').trigger('click');
    }

    $('.nav-tab').on('click', function() {
        var tabId = $(this).attr('href').substring(1);
        sessionStorage.setItem('apwpActiveSettingsTab', tabId);
    });
});
</script>
