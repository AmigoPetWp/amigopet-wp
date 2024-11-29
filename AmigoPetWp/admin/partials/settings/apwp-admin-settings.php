<?php
/**
 * Página de configurações do plugin
 */

// Verifica permissões
if (!current_user_can('manage_options')) {
    wp_die(__('Você não tem permissão para acessar esta página.', 'amigopet-wp'));
}

?>
<div class="wrap">
    <h1 class="wp-heading-inline">
        <span class="dashicons dashicons-admin-settings"></span>
        <?php _e('Configurações', 'amigopet-wp'); ?>
    </h1>
    
    <hr class="wp-header-end">

    <?php settings_errors(); ?>

    <?php
    $current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general';
    $tabs = array(
        'general' => array(
            'title' => __('Geral', 'amigopet-wp'),
            'icon' => 'dashicons-admin-generic',
            'file' => 'apwp-admin-settings-general.php'
        ),
        'adoption' => array(
            'title' => __('Adoção', 'amigopet-wp'),
            'icon' => 'dashicons-heart',
            'file' => 'apwp-admin-settings-adoption.php'
        ),
        'terms' => array(
            'title' => __('Termos', 'amigopet-wp'),
            'icon' => 'dashicons-media-document',
            'file' => 'apwp-admin-settings-terms.php'
        ),
        'flows' => array(
            'title' => __('Fluxos', 'amigopet-wp'),
            'icon' => 'dashicons-randomize',
            'file' => 'apwp-admin-settings-flows.php'
        )
    );
    ?>

    <nav class="nav-tab-wrapper wp-clearfix">
        <?php
        foreach ($tabs as $tab_id => $tab) {
            $class = ($tab_id === $current_tab) ? 'nav-tab nav-tab-active' : 'nav-tab';
            printf(
                '<a href="?page=%s&tab=%s" class="%s"><span class="dashicons %s"></span>%s</a>',
                esc_attr($_REQUEST['page']),
                esc_attr($tab_id),
                esc_attr($class),
                esc_attr($tab['icon']),
                esc_html($tab['title'])
            );
        }
        ?>
    </nav>

    <div class="tab-content">
        <form method="post" action="options.php">
            <?php
            // Carrega o arquivo correspondente à aba atual
            if (isset($tabs[$current_tab]['file'])) {
                require_once plugin_dir_path(__FILE__) . $tabs[$current_tab]['file'];
            }
            
            // Campos do formulário
            do_settings_sections('amigopet-wp-' . $current_tab);
            settings_fields('amigopet-wp-' . $current_tab);
            
            // Botão de salvar
            submit_button();
            ?>
        </form>
    </div>
</div>
