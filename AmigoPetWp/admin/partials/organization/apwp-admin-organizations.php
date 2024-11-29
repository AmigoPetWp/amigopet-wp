<?php
/**
 * Template para a página de organizações do plugin
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

// Verifica permissões
if (!current_user_can('manage_options')) {
    wp_die(__('Você não tem permissão para acessar esta página.', 'amigopet-wp'));
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Gerenciar Organizações', 'amigopet-wp'); ?></h1>
    
    <?php
    $current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'list';
    $tabs = array(
        'list' => array(
            'title' => __('Listar', 'amigopet-wp'),
            'icon' => 'dashicons-list-view'
        ),
        'add' => array(
            'title' => __('Adicionar', 'amigopet-wp'),
            'icon' => 'dashicons-plus'
        ),
        'reports' => array(
            'title' => __('Relatórios', 'amigopet-wp'),
            'icon' => 'dashicons-analytics'
        ),
        'settings' => array(
            'title' => __('Configurações', 'amigopet-wp'),
            'icon' => 'dashicons-admin-settings'
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

    <?php
    // Renderiza a página correta baseada na tab selecionada
    switch ($current_tab) {
        case 'add':
            // Incluir página de adicionar organização
            echo '<p>Formulário de adição de organização em desenvolvimento</p>';
            break;
        case 'reports':
            // Criar arquivo de relatórios de organizações se necessário
            echo '<p>Relatórios de organizações em desenvolvimento</p>';
            break;
        case 'settings':
            // Incluir página de configurações de organizações
            echo '<p>Configurações de organizações em desenvolvimento</p>';
            break;
        default:
            // Conteúdo padrão da lista de organizações
    ?>
    <a href="<?php echo admin_url('admin.php?page=amigopet-wp-add-organization'); ?>" class="page-title-action"><?php _e('Adicionar Nova Organização', 'amigopet-wp'); ?></a>
    
    <?php settings_errors('apwp_messages'); ?>

    <!-- Conteúdo da lista de organizações -->
    <div class="apwp-organizations-wrapper">
        <!-- Botões de Ação -->
        <div class="apwp-action-buttons">
            <?php
            $new_org_url = add_query_arg(
                array(
                    'page' => 'amigopet-wp-organizations',
                    'action' => 'new'
                ),
                admin_url('admin.php')
            );
            ?>
            <a href="<?php echo esc_url($new_org_url); ?>" class="page-title-action"><?php _e('Nova Organização', 'amigopet-wp'); ?></a>
        </div>

        <!-- Lista de Organizações -->
        <div class="apwp-list-section">
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th scope="col" class="manage-column column-logo"><?php _e('Logo', 'amigopet-wp'); ?></th>
                        <th scope="col" class="manage-column column-name"><?php _e('Nome', 'amigopet-wp'); ?></th>
                        <th scope="col" class="manage-column column-cnpj"><?php _e('CNPJ', 'amigopet-wp'); ?></th>
                        <th scope="col" class="manage-column column-email"><?php _e('Email', 'amigopet-wp'); ?></th>
                        <th scope="col" class="manage-column column-phone"><?php _e('Telefone', 'amigopet-wp'); ?></th>
                        <th scope="col" class="manage-column column-city"><?php _e('Cidade', 'amigopet-wp'); ?></th>
                        <th scope="col" class="manage-column column-state"><?php _e('Estado', 'amigopet-wp'); ?></th>
                        <th scope="col" class="manage-column column-actions"><?php _e('Ações', 'amigopet-wp'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $organization = new APWP_Organization();
                    $organizations = $organization->list();

                    if (!empty($organizations)) {
                        foreach ($organizations as $org) {
                            $edit_url = add_query_arg(
                                array(
                                    'page' => 'amigopet-wp-organizations',
                                    'action' => 'edit',
                                    'id' => $org->id
                                ),
                                admin_url('admin.php')
                            );

                            $delete_url = add_query_arg(
                                array(
                                    'page' => 'amigopet-wp-organizations',
                                    'action' => 'delete',
                                    'id' => $org->id,
                                    'nonce' => wp_create_nonce('apwp_delete_organization')
                                ),
                                admin_url('admin.php')
                            );

                            $view_pets_url = add_query_arg(
                                array(
                                    'page' => 'amigopet-wp-pets',
                                    'organization_id' => $org->id
                                ),
                                admin_url('admin.php')
                            );
                            ?>
                            <tr>
                                <td class="column-logo">
                                    <?php if (!empty($org->logo_url)) : ?>
                                        <img src="<?php echo esc_url($org->logo_url); ?>" alt="<?php echo esc_attr($org->name); ?>" style="max-width: 50px; height: auto;">
                                    <?php endif; ?>
                                </td>
                                <td class="column-name">
                                    <strong>
                                        <a href="<?php echo esc_url($edit_url); ?>"><?php echo esc_html($org->name); ?></a>
                                    </strong>
                                </td>
                                <td class="column-cnpj"><?php echo esc_html($org->cnpj); ?></td>
                                <td class="column-email"><?php echo esc_html($org->email); ?></td>
                                <td class="column-phone"><?php echo esc_html($org->phone); ?></td>
                                <td class="column-city"><?php echo esc_html($org->city); ?></td>
                                <td class="column-state"><?php echo esc_html($org->state); ?></td>
                                <td class="column-actions">
                                    <a href="<?php echo esc_url($edit_url); ?>" class="button button-small">
                                        <span class="dashicons dashicons-edit"></span>
                                        <?php _e('Editar', 'amigopet-wp'); ?>
                                    </a>
                                    <a href="<?php echo esc_url($view_pets_url); ?>" class="button button-small">
                                        <span class="dashicons dashicons-pets"></span>
                                        <?php _e('Pets', 'amigopet-wp'); ?>
                                    </a>
                                    <a href="<?php echo esc_url($delete_url); ?>" class="button button-small delete" onclick="return confirm('<?php esc_attr_e('Tem certeza que deseja excluir esta organização?', 'amigopet-wp'); ?>');">
                                        <span class="dashicons dashicons-trash"></span>
                                        <?php _e('Excluir', 'amigopet-wp'); ?>
                                    </a>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="8" style="text-align: center;">
                                <?php _e('Nenhuma organização encontrada.', 'amigopet-wp'); ?>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
        }
    ?>
</div>
