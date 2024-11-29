<?php
/**
 * Template para configurações gerais
 */

// Verifica permissões
if (!current_user_can('manage_options')) {
    wp_die(__('Você não tem permissão para acessar esta página.', 'amigopet-wp'));
}

// Salva as configurações
if (isset($_POST['submit_general_settings'])) {
    check_admin_referer('save_general_settings', 'general_settings_nonce');
    
    // Atualiza as opções
    update_option('apwp_plugin_enabled', isset($_POST['plugin_enabled']));
    update_option('apwp_default_organization', sanitize_text_field($_POST['default_organization']));
    update_option('apwp_currency', sanitize_text_field($_POST['currency']));
    update_option('apwp_date_format', sanitize_text_field($_POST['date_format']));
    update_option('apwp_timezone', sanitize_text_field($_POST['timezone']));
    update_option('apwp_debug_mode', isset($_POST['debug_mode']));
    
    echo '<div class="notice notice-success"><p>' . __('Configurações salvas com sucesso!', 'amigopet-wp') . '</p></div>';
}

// Verifica se a ação de forçar criação das tabelas foi solicitada
if (isset($_GET['force_create_tables']) && $_GET['force_create_tables'] === '1' && check_admin_referer('apwp_force_create_tables')) {
    require_once AMIGOPET_WP_PLUGIN_DIR . 'includes/class-apwp-database.php';
    APWP_Database::force_create_tables();
    echo '<div class="notice notice-success"><p>' . __('Tabelas do banco de dados foram criadas/atualizadas com sucesso.', 'amigopet-wp') . '</p></div>';
}

// Busca organizações para o select
global $wpdb;
$organizations = $wpdb->get_results("SELECT id, name FROM {$wpdb->prefix}apwp_organizations WHERE status = 'active' ORDER BY name");

// Busca configurações atuais
$plugin_enabled = get_option('apwp_plugin_enabled', true);
$default_organization = get_option('apwp_default_organization', '');
$currency = get_option('apwp_currency', 'BRL');
$date_format = get_option('apwp_date_format', 'd/m/Y');
$timezone = get_option('apwp_timezone', 'America/Sao_Paulo');
$debug_mode = get_option('apwp_debug_mode', false);
?>

<div class="wrap">
    <h2><?php _e('Configurações Gerais', 'amigopet-wp'); ?></h2>
    
    <form method="post" action="">
        <?php wp_nonce_field('save_general_settings', 'general_settings_nonce'); ?>
        
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Status do Plugin', 'amigopet-wp'); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="plugin_enabled" value="1" <?php checked($plugin_enabled); ?>>
                        <?php _e('Ativar plugin', 'amigopet-wp'); ?>
                    </label>
                </td>
            </tr>
            
            <tr>
                <th scope="row"><label for="default_organization"><?php _e('Organização Padrão', 'amigopet-wp'); ?></label></th>
                <td>
                    <select name="default_organization" id="default_organization">
                        <option value=""><?php _e('Selecione...', 'amigopet-wp'); ?></option>
                        <?php foreach ($organizations as $org): ?>
                            <option value="<?php echo esc_attr($org->id); ?>" <?php selected($default_organization, $org->id); ?>>
                                <?php echo esc_html($org->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description"><?php _e('Organização que será usada por padrão ao criar novos registros.', 'amigopet-wp'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row"><label for="currency"><?php _e('Moeda', 'amigopet-wp'); ?></label></th>
                <td>
                    <select name="currency" id="currency">
                        <option value="BRL" <?php selected($currency, 'BRL'); ?>><?php _e('Real Brasileiro (R$)', 'amigopet-wp'); ?></option>
                        <option value="USD" <?php selected($currency, 'USD'); ?>><?php _e('Dólar Americano ($)', 'amigopet-wp'); ?></option>
                        <option value="EUR" <?php selected($currency, 'EUR'); ?>><?php _e('Euro (€)', 'amigopet-wp'); ?></option>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th scope="row"><label for="date_format"><?php _e('Formato de Data', 'amigopet-wp'); ?></label></th>
                <td>
                    <select name="date_format" id="date_format">
                        <option value="d/m/Y" <?php selected($date_format, 'd/m/Y'); ?>><?php _e('DD/MM/AAAA', 'amigopet-wp'); ?></option>
                        <option value="Y-m-d" <?php selected($date_format, 'Y-m-d'); ?>><?php _e('AAAA-MM-DD', 'amigopet-wp'); ?></option>
                        <option value="m/d/Y" <?php selected($date_format, 'm/d/Y'); ?>><?php _e('MM/DD/AAAA', 'amigopet-wp'); ?></option>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th scope="row"><label for="timezone"><?php _e('Fuso Horário', 'amigopet-wp'); ?></label></th>
                <td>
                    <select name="timezone" id="timezone">
                        <?php
                        $timezones = array(
                            'America/Sao_Paulo' => 'Brasília (GMT-3)',
                            'America/Manaus' => 'Manaus (GMT-4)',
                            'America/Belem' => 'Belém (GMT-3)',
                            'America/Fortaleza' => 'Fortaleza (GMT-3)',
                            'America/Recife' => 'Recife (GMT-3)',
                            'America/Noronha' => 'Fernando de Noronha (GMT-2)'
                        );
                        foreach ($timezones as $tz => $label) {
                            echo '<option value="' . esc_attr($tz) . '" ' . selected($timezone, $tz, false) . '>' . esc_html($label) . '</option>';
                        }
                        ?>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th scope="row"><?php _e('Modo Debug', 'amigopet-wp'); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="debug_mode" value="1" <?php checked($debug_mode); ?>>
                        <?php _e('Ativar modo debug', 'amigopet-wp'); ?>
                    </label>
                    <p class="description"><?php _e('Ativa logs detalhados para depuração.', 'amigopet-wp'); ?></p>
                </td>
            </tr>
        </table>
        
        <?php submit_button(__('Salvar Configurações', 'amigopet-wp'), 'primary', 'submit_general_settings'); ?>
    </form>
    
    <h2><?php _e('Manutenção do Banco de Dados', 'amigopet-wp'); ?></h2>
    
    <p><?php _e('Forçar a criação das tabelas do banco de dados:', 'amigopet-wp'); ?></p>
    
    <form method="get" action="">
        <?php wp_nonce_field('apwp_force_create_tables'); ?>
        
        <input type="hidden" name="force_create_tables" value="1">
        
        <?php submit_button(__('Forçar Criação das Tabelas', 'amigopet-wp'), 'secondary', 'force_create_tables'); ?>
    </form>
</div>
