<?php
/**
 * Template para a página de contratos do plugin
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
$adoption_id = isset($_GET['adoption_id']) ? intval($_GET['adoption_id']) : 0;
$organization_id = isset($_GET['organization_id']) ? intval($_GET['organization_id']) : 0;

// Verifica se há um contrato específico para visualizar
$contract_id = isset($_GET['contract_id']) ? intval($_GET['contract_id']) : 0;
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Gerenciar Contratos', 'amigopet-wp'); ?></h1>
    
    <!-- Menu de navegação de Contratos -->
    <div class="nav-tab-wrapper">
        <a href="<?php echo admin_url('admin.php?page=amigopet-wp-contracts'); ?>" class="nav-tab <?php echo (!isset($_GET['tab']) || $_GET['tab'] == 'list') ? 'nav-tab-active' : ''; ?>">
            <?php _e('Listar', 'amigopet-wp'); ?>
        </a>
        <a href="<?php echo admin_url('admin.php?page=amigopet-wp-contracts&tab=add'); ?>" class="nav-tab <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'add') ? 'nav-tab-active' : ''; ?>">
            <?php _e('Adicionar', 'amigopet-wp'); ?>
        </a>
        <a href="<?php echo admin_url('admin.php?page=amigopet-wp-contracts&tab=reports'); ?>" class="nav-tab <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'reports') ? 'nav-tab-active' : ''; ?>">
            <?php _e('Relatórios', 'amigopet-wp'); ?>
        </a>
    </div>

    <?php
    // Renderiza a página correta baseada na tab selecionada
    $current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'list';
    switch ($current_tab) {
        case 'add':
            // Incluir página de adicionar contrato
            echo '<p>Formulário de adição de contrato em desenvolvimento</p>';
            break;
        case 'reports':
            // Criar arquivo de relatórios de contratos se necessário
            echo '<p>Relatórios de contratos em desenvolvimento</p>';
            break;
        default:
            // Conteúdo padrão da lista de contratos
    ?>
    <a href="<?php echo admin_url('admin.php?page=amigopet-wp-add-contract'); ?>" class="page-title-action"><?php _e('Adicionar Novo Contrato', 'amigopet-wp'); ?></a>
    
    <?php settings_errors('apwp_messages'); ?>

    <!-- Conteúdo da lista de contratos -->
    <div class="apwp-contracts-wrapper">
        <!-- Botões de Ação -->
        <div class="apwp-action-buttons">
            <?php
            $new_contract_url = add_query_arg(
                array(
                    'page' => $page,
                    'action' => 'new'
                ),
                admin_url('admin.php')
            );

            $manage_templates_url = add_query_arg(
                array(
                    'page' => $page,
                    'view' => 'templates'
                ),
                admin_url('admin.php')
            );
            ?>
            <a href="<?php echo esc_url($new_contract_url); ?>" class="page-title-action"><?php _e('Novo Contrato', 'amigopet-wp'); ?></a>
            <a href="<?php echo esc_url($manage_templates_url); ?>" class="page-title-action"><?php _e('Gerenciar Templates', 'amigopet-wp'); ?></a>
        </div>

        <!-- Filtros -->
        <div class="apwp-filters">
            <form method="get" action="<?php echo admin_url('admin.php'); ?>">
                <input type="hidden" name="page" value="<?php echo esc_attr($page); ?>">
                
                <select name="status">
                    <option value=""><?php _e('Todos os Status', 'amigopet-wp'); ?></option>
                    <option value="draft" <?php selected($status, 'draft'); ?>><?php _e('Rascunho', 'amigopet-wp'); ?></option>
                    <option value="pending" <?php selected($status, 'pending'); ?>><?php _e('Pendente', 'amigopet-wp'); ?></option>
                    <option value="signed" <?php selected($status, 'signed'); ?>><?php _e('Assinado', 'amigopet-wp'); ?></option>
                    <option value="cancelled" <?php selected($status, 'cancelled'); ?>><?php _e('Cancelado', 'amigopet-wp'); ?></option>
                </select>

                <?php submit_button(__('Filtrar', 'amigopet-wp'), 'secondary', 'submit', false); ?>
            </form>
        </div>

        <!-- Lista de Contratos -->
        <div class="apwp-list-section">
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th scope="col" class="manage-column column-id"><?php _e('ID', 'amigopet-wp'); ?></th>
                        <th scope="col" class="manage-column column-number"><?php _e('Número', 'amigopet-wp'); ?></th>
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
                    $contract = new APWP_Contract();
                    $contracts = $contract->list(array(
                        'status' => $status,
                        'adoption_id' => $adoption_id,
                        'organization_id' => $organization_id
                    ));

                    if (!empty($contracts)) {
                        foreach ($contracts as $contract) {
                            $edit_url = add_query_arg(
                                array(
                                    'page' => $page,
                                    'action' => 'edit',
                                    'id' => $contract->id
                                ),
                                admin_url('admin.php')
                            );

                            $view_url = add_query_arg(
                                array(
                                    'page' => $page,
                                    'action' => 'view',
                                    'id' => $contract->id
                                ),
                                admin_url('admin.php')
                            );

                            $download_url = add_query_arg(
                                array(
                                    'page' => $page,
                                    'action' => 'download',
                                    'id' => $contract->id,
                                    'nonce' => wp_create_nonce('download_contract_' . $contract->id)
                                ),
                                admin_url('admin.php')
                            );

                            $status_class = 'status-' . $contract->status;
                            ?>
                            <tr>
                                <td class="column-id"><?php echo esc_html($contract->id); ?></td>
                                <td class="column-number">
                                    <strong>
                                        <a href="<?php echo esc_url($view_url); ?>"><?php echo esc_html($contract->number); ?></a>
                                    </strong>
                                </td>
                                <td class="column-pet"><?php echo esc_html($contract->pet_name); ?></td>
                                <td class="column-adopter"><?php echo esc_html($contract->adopter_name); ?></td>
                                <td class="column-organization"><?php echo esc_html($contract->organization_name); ?></td>
                                <td class="column-date">
                                    <?php echo esc_html(date_i18n(get_option('date_format'), strtotime($contract->created_at))); ?>
                                </td>
                                <td class="column-status">
                                    <span class="<?php echo esc_attr($status_class); ?>">
                                        <?php echo esc_html($contract->status); ?>
                                    </span>
                                </td>
                                <td class="column-actions">
                                    <?php if ($contract->status !== 'signed') : ?>
                                        <a href="<?php echo esc_url($edit_url); ?>" class="button button-small">
                                            <span class="dashicons dashicons-edit"></span>
                                            <?php _e('Editar', 'amigopet-wp'); ?>
                                        </a>
                                    <?php endif; ?>
                                    <a href="<?php echo esc_url($view_url); ?>" class="button button-small">
                                        <span class="dashicons dashicons-visibility"></span>
                                        <?php _e('Visualizar', 'amigopet-wp'); ?>
                                    </a>
                                    <a href="<?php echo esc_url($download_url); ?>" class="button button-small">
                                        <span class="dashicons dashicons-download"></span>
                                        <?php _e('Download', 'amigopet-wp'); ?>
                                    </a>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="8" style="text-align: center;">
                                <?php _e('Nenhum contrato encontrado.', 'amigopet-wp'); ?>
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

<style>
.status-draft { color: #646970; }
.status-pending { color: #ffb900; }
.status-signed { color: #46b450; }
.status-cancelled { color: #dc3232; }

.apwp-filters {
    margin: 1em 0;
    padding: 1em;
    background: #fff;
    border: 1px solid #ccd0d4;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}

.apwp-filters select {
    margin-right: 6px;
}

.column-status span {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 3px;
    background: rgba(0,0,0,.05);
}

.column-actions .button {
    margin-right: 4px;
}

.column-actions .button:last-child {
    margin-right: 0;
}

.column-actions .dashicons {
    width: 16px;
    height: 16px;
    font-size: 16px;
    vertical-align: text-top;
    margin-right: 2px;
}
</style>
