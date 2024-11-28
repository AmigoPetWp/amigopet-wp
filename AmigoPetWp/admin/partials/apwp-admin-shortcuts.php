<?php
/**
 * Template para a página de shortcuts
 */

// Verifica permissões
if (!current_user_can('manage_options')) {
    wp_die(__('Você não tem permissão para acessar esta página.', 'amigopet-wp'));
}
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <div class="apwp-shortcuts-grid">
        <!-- Seção de Ajuda -->
        <div class="apwp-shortcut-card">
            <h2><span class="dashicons dashicons-editor-help"></span> <?php _e('Ajuda', 'amigopet-wp'); ?></h2>
            <p><?php _e('Documentação completa e guias de uso do plugin.', 'amigopet-wp'); ?></p>
            <?php require_once 'apwp-admin-help.php'; ?>
        </div>

        <!-- Seção de Shortcodes -->
        <div class="apwp-shortcut-card">
            <h2><span class="dashicons dashicons-editor-code"></span> <?php _e('Shortcodes', 'amigopet-wp'); ?></h2>
            <p><?php _e('Lista de todos os shortcodes disponíveis.', 'amigopet-wp'); ?></p>
            <?php require_once 'apwp-admin-shortcodes.php'; ?>
        </div>

        <!-- Links Rápidos -->
        <div class="apwp-shortcut-card">
            <h2><span class="dashicons dashicons-admin-links"></span> <?php _e('Links Rápidos', 'amigopet-wp'); ?></h2>
            <ul>
                <li><a href="<?php echo admin_url('admin.php?page=amigopet-wp-pets'); ?>"><?php _e('Gerenciar Pets', 'amigopet-wp'); ?></a></li>
                <li><a href="<?php echo admin_url('admin.php?page=amigopet-wp-adoptions'); ?>"><?php _e('Gerenciar Adoções', 'amigopet-wp'); ?></a></li>
                <li><a href="<?php echo admin_url('admin.php?page=amigopet-wp-adopters'); ?>"><?php _e('Gerenciar Adotantes', 'amigopet-wp'); ?></a></li>
                <li><a href="<?php echo admin_url('admin.php?page=amigopet-wp-terms'); ?>"><?php _e('Gerenciar Termos', 'amigopet-wp'); ?></a></li>
            </ul>
        </div>

        <!-- Estatísticas Rápidas -->
        <div class="apwp-shortcut-card">
            <h2><span class="dashicons dashicons-chart-bar"></span> <?php _e('Estatísticas', 'amigopet-wp'); ?></h2>
            <?php
            global $wpdb;
            $pets_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}apwp_pets");
            $adoptions_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}apwp_adoptions");
            $adopters_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}apwp_adopters");
            ?>
            <ul>
                <li><?php printf(__('Total de Pets: %d', 'amigopet-wp'), $pets_count); ?></li>
                <li><?php printf(__('Total de Adoções: %d', 'amigopet-wp'), $adoptions_count); ?></li>
                <li><?php printf(__('Total de Adotantes: %d', 'amigopet-wp'), $adopters_count); ?></li>
            </ul>
        </div>
    </div>
</div>

<style>
.apwp-shortcuts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.apwp-shortcut-card {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    padding: 20px;
}

.apwp-shortcut-card h2 {
    margin-top: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.apwp-shortcut-card .dashicons {
    font-size: 24px;
    width: 24px;
    height: 24px;
}

.apwp-shortcut-card ul {
    margin: 0;
    padding: 0;
    list-style: none;
}

.apwp-shortcut-card ul li {
    margin-bottom: 10px;
}

.apwp-shortcut-card ul li:last-child {
    margin-bottom: 0;
}
</style>
