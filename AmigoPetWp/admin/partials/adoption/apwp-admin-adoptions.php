<?php
/**
 * Template para a página de adoções do plugin
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

// Obtém a página atual
$page = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : '';

// Filtros
$status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';
$adopter_id = isset($_GET['adopter_id']) ? intval($_GET['adopter_id']) : 0;
$organization_id = isset($_GET['organization_id']) ? intval($_GET['organization_id']) : 0;
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Gerenciar Adoções', 'amigopet-wp'); ?></h1>
    
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
            include_once plugin_dir_path(__FILE__) . 'apwp-admin-add-adoption.php';
            break;
        case 'reports':
            // Criar arquivo de relatórios de adoções se necessário
            // include_once plugin_dir_path(__FILE__) . 'apwp-admin-adoptions-reports.php';
            echo '<p>Relatórios de adoções em desenvolvimento</p>';
            break;
        default:
            // Conteúdo padrão da lista de adoções
    ?>
    <a href="<?php echo admin_url('admin.php?page=amigopet-wp-add-adoption'); ?>" class="page-title-action"><?php _e('Adicionar Nova Adoção', 'amigopet-wp'); ?></a>
    
    <?php settings_errors('apwp_messages'); ?>

    <!-- Filtros -->
    <div class="apwp-filters">
        <form method="get" action="<?php echo admin_url('admin.php'); ?>">
            <input type="hidden" name="page" value="<?php echo esc_attr($page); ?>">
            
            <select name="status">
                <option value=""><?php _e('Todos os Status', 'amigopet-wp'); ?></option>
                <option value="pending" <?php selected($status, 'pending'); ?>><?php _e('Pendente', 'amigopet-wp'); ?></option>
                <option value="approved" <?php selected($status, 'approved'); ?>><?php _e('Aprovada', 'amigopet-wp'); ?></option>
                <option value="rejected" <?php selected($status, 'rejected'); ?>><?php _e('Rejeitada', 'amigopet-wp'); ?></option>
                <option value="cancelled" <?php selected($status, 'cancelled'); ?>><?php _e('Cancelada', 'amigopet-wp'); ?></option>
            </select>

            <?php submit_button(__('Filtrar', 'amigopet-wp'), 'secondary', 'submit', false); ?>
        </form>
    </div>

    <!-- Lista de Adoções -->
    <div class="apwp-list-section">
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th scope="col" class="manage-column column-id"><?php _e('ID', 'amigopet-wp'); ?></th>
                    <th scope="col" class="manage-column column-pet"><?php _e('Pet', 'amigopet-wp'); ?></th>
                    <th scope="col" class="manage-column column-adopter"><?php _e('Adotante', 'amigopet-wp'); ?></th>
                    <th scope="col" class="manage-column column-organization"><?php _e('Organização', 'amigopet-wp'); ?></th>
                    <th scope="col" class="manage-column column-date"><?php _e('Data', 'amigopet-wp'); ?></th>
                    <th scope="col" class="manage-column column-status"><?php _e('Status', 'amigopet-wp'); ?></th>
                    <th scope="col" class="manage-column column-actions"><?php _e('Ações', 'amigopet-wp'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $adoption = new APWP_Adoption();
                $adoptions = $adoption->list(array(
                    'status' => $status,
                    'adopter_id' => $adopter_id,
                    'organization_id' => $organization_id
                ));

                if (!empty($adoptions)) {
                    foreach ($adoptions as $adoption) {
                        $edit_url = add_query_arg(
                            array(
                                'page' => $page,
                                'action' => 'edit',
                                'id' => $adoption->id
                            ),
                            admin_url('admin.php')
                        );

                        $view_contract_url = add_query_arg(
                            array(
                                'page' => 'amigopet-wp-contracts',
                                'adoption_id' => $adoption->id
                            ),
                            admin_url('admin.php')
                        );

                        $status_class = 'status-' . $adoption->status;
                        ?>
                        <tr>
                            <td class="column-id"><?php echo esc_html($adoption->id); ?></td>
                            <td class="column-pet">
                                <strong>
                                    <a href="<?php echo esc_url($edit_url); ?>"><?php echo esc_html($adoption->pet_name); ?></a>
                                </strong>
                            </td>
                            <td class="column-adopter"><?php echo esc_html($adoption->adopter_name); ?></td>
                            <td class="column-organization"><?php echo esc_html($adoption->organization_name); ?></td>
                            <td class="column-date">
                                <?php echo esc_html(date_i18n(get_option('date_format'), strtotime($adoption->created_at))); ?>
                            </td>
                            <td class="column-status">
                                <span class="<?php echo esc_attr($status_class); ?>">
                                    <?php echo esc_html($adoption->status); ?>
                                </span>
                            </td>
                            <td class="column-actions">
                                <a href="<?php echo esc_url($edit_url); ?>" class="button button-small">
                                    <span class="dashicons dashicons-edit"></span>
                                    <?php _e('Editar', 'amigopet-wp'); ?>
                                </a>
                                <?php if ($adoption->status === 'approved') : ?>
                                    <a href="<?php echo esc_url($view_contract_url); ?>" class="button button-small">
                                        <span class="dashicons dashicons-media-document"></span>
                                        <?php _e('Contrato', 'amigopet-wp'); ?>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="7" style="text-align: center;">
                            <?php _e('Nenhuma adoção encontrada.', 'amigopet-wp'); ?>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php
    }
    ?>
</div>
