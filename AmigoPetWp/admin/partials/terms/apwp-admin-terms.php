<?php
/**
 * Template para gerenciamento de termos
 */

// Verifica permissões
if (!current_user_can('manage_options')) {
    wp_die(__('Você não tem permissão para acessar esta página.', 'amigopet-wp'));
}

// Define as abas
$tabs = array(
    'list' => array(
        'title' => __('Termos', 'amigopet-wp'),
        'icon' => 'dashicons-media-document'
    ),
    'add' => array(
        'title' => __('Adicionar Termo', 'amigopet-wp'),
        'icon' => 'dashicons-plus'
    ),
    'types' => array(
        'title' => __('Tipos', 'amigopet-wp'),
        'icon' => 'dashicons-forms'
    ),
    'templates' => array(
        'title' => __('Modelos', 'amigopet-wp'),
        'icon' => 'dashicons-text'
    ),
    'signed' => array(
        'title' => __('Assinados', 'amigopet-wp'),
        'icon' => 'dashicons-clipboard'
    ),
    'reports' => array(
        'title' => __('Relatórios', 'amigopet-wp'),
        'icon' => 'dashicons-analytics'
    )
);

// Obtém a aba atual
$current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'list';
?>

<div class="wrap">
    <h1 class="wp-heading-inline">
        <span class="dashicons dashicons-media-document"></span>
        <?php _e('Termos', 'amigopet-wp'); ?>
    </h1>
    
    <hr class="wp-header-end">

    <nav class="nav-tab-wrapper wp-clearfix">
        <?php foreach ($tabs as $tab_id => $tab) : ?>
            <a href="<?php echo add_query_arg('tab', $tab_id); ?>" 
               class="nav-tab <?php echo $current_tab === $tab_id ? 'nav-tab-active' : ''; ?>">
                <span class="dashicons <?php echo $tab['icon']; ?>"></span>
                <?php echo $tab['title']; ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="apwp-admin-content">
        <?php
        // Carrega o conteúdo da aba atual
        switch ($current_tab) {
            case 'add':
                require_once plugin_dir_path(__FILE__) . 'apwp-admin-add-term.php';
                break;
            case 'types':
                require_once plugin_dir_path(__FILE__) . 'apwp-admin-term-types.php';
                break;
            case 'templates':
                require_once plugin_dir_path(__FILE__) . 'apwp-admin-term-templates.php';
                break;
            case 'signed':
                require_once plugin_dir_path(__FILE__) . 'apwp-admin-signed-terms.php';
                break;
            case 'reports':
                require_once plugin_dir_path(__FILE__) . 'apwp-admin-term-reports.php';
                break;
            default:
                require_once plugin_dir_path(__FILE__) . 'apwp-admin-term-list.php';
                break;
        }
        ?>
    </div>
</div>
