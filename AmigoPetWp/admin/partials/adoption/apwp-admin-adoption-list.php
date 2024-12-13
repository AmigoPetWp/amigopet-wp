<?php
/**
 * Template para a listagem de adoções
 *
 * @package AmigoPet_Wp
 */

// Se este arquivo for chamado diretamente, aborte.
if (!defined('WPINC')) {
    die;
}

// Inclui o template base
require_once APWP_PLUGIN_DIR . 'admin/partials/apwp-admin-template.php';

// Configuração da página
$page_title = __('Adoções', 'amigopet-wp');

// Ações do cabeçalho
$actions = array(
    array(
        'url' => '#',
        'text' => __('Exportar', 'amigopet-wp'),
        'class' => 'apwp-admin-button apwp-admin-button-secondary',
        'icon' => 'dashicons-download',
        'id' => 'export-adoptions'
    )
);

// Abas
$current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'all';
$tabs = array(
    array(
        'url' => admin_url('admin.php?page=amigopet-wp-adoptions&tab=all'),
        'text' => __('Todas', 'amigopet-wp'),
        'class' => $current_tab === 'all' ? 'active' : '',
        'icon' => 'dashicons-list-view'
    ),
    array(
        'url' => admin_url('admin.php?page=amigopet-wp-adoptions&tab=pending'),
        'text' => __('Pendentes', 'amigopet-wp'),
        'class' => $current_tab === 'pending' ? 'active' : '',
        'icon' => 'dashicons-clock'
    ),
    array(
        'url' => admin_url('admin.php?page=amigopet-wp-adoptions&tab=approved'),
        'text' => __('Aprovadas', 'amigopet-wp'),
        'class' => $current_tab === 'approved' ? 'active' : '',
        'icon' => 'dashicons-yes-alt'
    ),
    array(
        'url' => admin_url('admin.php?page=amigopet-wp-adoptions&tab=rejected'),
        'text' => __('Rejeitadas', 'amigopet-wp'),
        'class' => $current_tab === 'rejected' ? 'active' : '',
        'icon' => 'dashicons-dismiss'
    )
);

// Conteúdo da página
ob_start();
?>

<!-- Filtros -->
<div class="apwp-admin-filters">
    <div class="apwp-admin-search">
        <input type="text" id="adoption-search" placeholder="<?php esc_attr_e('Buscar adoções...', 'amigopet-wp'); ?>">
    </div>
    
    <div class="apwp-admin-date-range">
        <input type="date" id="date-from" class="apwp-admin-date" placeholder="<?php esc_attr_e('De', 'amigopet-wp'); ?>">
        <input type="date" id="date-to" class="apwp-admin-date" placeholder="<?php esc_attr_e('Até', 'amigopet-wp'); ?>">
    </div>
</div>

<!-- Lista de Adoções -->
<?php
// Configura os cabeçalhos da tabela
$headers = array(
    array('text' => __('ID', 'amigopet-wp'), 'class' => 'column-id'),
    array('text' => __('Pet', 'amigopet-wp'), 'class' => 'column-pet'),
    array('text' => __('Adotante', 'amigopet-wp'), 'class' => 'column-adopter'),
    array('text' => __('Data', 'amigopet-wp'), 'class' => 'column-date'),
    array('text' => __('Status', 'amigopet-wp'), 'class' => 'column-status'),
    array('text' => __('Ações', 'amigopet-wp'), 'class' => 'column-actions')
);

// Exemplo de dados (substitua por dados reais do banco)
$adoptions = array(
    array(
        'class' => 'adoption-row',
        'columns' => array(
            array(
                'content' => '#1234',
                'class' => 'column-id'
            ),
            array(
                'content' => '
                    <div class="pet-info">
                        <img src="path/to/photo.jpg" alt="Pet Photo" class="pet-mini-photo">
                        <span>Rex</span>
                    </div>
                ',
                'class' => 'column-pet'
            ),
            array(
                'content' => '
                    <div class="adopter-info">
                        <strong>João Silva</strong><br>
                        <small>joao@email.com</small>
                    </div>
                ',
                'class' => 'column-adopter'
            ),
            array(
                'content' => '
                    <div class="date-info">
                        <strong>10/01/2024</strong><br>
                        <small>14:30</small>
                    </div>
                ',
                'class' => 'column-date'
            ),
            array(
                'content' => '<span class="apwp-status-tag apwp-status-pending">' . __('Pendente', 'amigopet-wp') . '</span>',
                'class' => 'column-status'
            ),
            array(
                'content' => '
                    <div class="apwp-admin-actions">
                        <a href="#" class="apwp-admin-button apwp-admin-button-success" title="' . esc_attr__('Aprovar', 'amigopet-wp') . '">
                            <span class="dashicons dashicons-yes"></span>
                        </a>
                        <a href="#" class="apwp-admin-button apwp-admin-button-danger" title="' . esc_attr__('Rejeitar', 'amigopet-wp') . '">
                            <span class="dashicons dashicons-no"></span>
                        </a>
                        <a href="#" class="apwp-admin-button apwp-admin-button-secondary" title="' . esc_attr__('Detalhes', 'amigopet-wp') . '">
                            <span class="dashicons dashicons-visibility"></span>
                        </a>
                    </div>
                ',
                'class' => 'column-actions'
            )
        )
    )
);

// Renderiza a tabela
apwp_render_admin_table($headers, $adoptions, array(
    'class' => 'adoptions-table',
    'id' => 'adoptions-list',
    'empty_message' => __('Nenhuma adoção encontrada.', 'amigopet-wp')
));

// Paginação
?>
<div class="apwp-admin-pagination">
    <a href="#" class="apwp-admin-page-link">&laquo;</a>
    <a href="#" class="apwp-admin-page-link active">1</a>
    <a href="#" class="apwp-admin-page-link">2</a>
    <a href="#" class="apwp-admin-page-link">3</a>
    <a href="#" class="apwp-admin-page-link">&raquo;</a>
</div>

<?php
$content = ob_get_clean();

// Renderiza o template
apwp_render_admin_template($page_title, $content, $actions, $tabs);
?>
