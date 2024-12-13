<?php
/**
 * Template para a listagem de termos
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
$page_title = __('Termos e Condições', 'amigopet-wp');

// Ações do cabeçalho
$actions = array(
    array(
        'url' => admin_url('admin.php?page=amigopet-wp-terms-new'),
        'text' => __('Adicionar Termo', 'amigopet-wp'),
        'class' => 'apwp-admin-button apwp-admin-button-primary',
        'icon' => 'dashicons-plus-alt'
    )
);

// Abas
$current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'all';
$tabs = array(
    array(
        'url' => admin_url('admin.php?page=amigopet-wp-terms&tab=all'),
        'text' => __('Todos', 'amigopet-wp'),
        'class' => $current_tab === 'all' ? 'active' : '',
        'icon' => 'dashicons-text-page'
    ),
    array(
        'url' => admin_url('admin.php?page=amigopet-wp-terms&tab=active'),
        'text' => __('Ativos', 'amigopet-wp'),
        'class' => $current_tab === 'active' ? 'active' : '',
        'icon' => 'dashicons-yes-alt'
    ),
    array(
        'url' => admin_url('admin.php?page=amigopet-wp-terms&tab=archived'),
        'text' => __('Arquivados', 'amigopet-wp'),
        'class' => $current_tab === 'archived' ? 'active' : '',
        'icon' => 'dashicons-archive'
    )
);

// Conteúdo da página
ob_start();
?>

<!-- Filtros -->
<div class="apwp-admin-filters">
    <div class="apwp-admin-search">
        <input type="text" id="terms-search" placeholder="<?php esc_attr_e('Buscar termos...', 'amigopet-wp'); ?>">
    </div>
    
    <select id="type-filter" class="apwp-admin-select">
        <option value=""><?php esc_html_e('Todos os Tipos', 'amigopet-wp'); ?></option>
        <option value="adoption"><?php esc_html_e('Adoção', 'amigopet-wp'); ?></option>
        <option value="volunteer"><?php esc_html_e('Voluntariado', 'amigopet-wp'); ?></option>
        <option value="privacy"><?php esc_html_e('Privacidade', 'amigopet-wp'); ?></option>
    </select>
    
    <select id="version-filter" class="apwp-admin-select">
        <option value=""><?php esc_html_e('Todas as Versões', 'amigopet-wp'); ?></option>
        <option value="current"><?php esc_html_e('Versão Atual', 'amigopet-wp'); ?></option>
        <option value="previous"><?php esc_html_e('Versões Anteriores', 'amigopet-wp'); ?></option>
    </select>
</div>

<!-- Lista de Termos -->
<?php
// Configura os cabeçalhos da tabela
$headers = array(
    array('text' => __('Título', 'amigopet-wp'), 'class' => 'column-title'),
    array('text' => __('Tipo', 'amigopet-wp'), 'class' => 'column-type'),
    array('text' => __('Versão', 'amigopet-wp'), 'class' => 'column-version'),
    array('text' => __('Última Atualização', 'amigopet-wp'), 'class' => 'column-updated'),
    array('text' => __('Status', 'amigopet-wp'), 'class' => 'column-status'),
    array('text' => __('Ações', 'amigopet-wp'), 'class' => 'column-actions')
);

// Exemplo de dados (substitua por dados reais do banco)
$terms = array(
    array(
        'class' => 'terms-row',
        'columns' => array(
            array(
                'content' => '
                    <div class="terms-info">
                        <div class="terms-icon">
                            <span class="dashicons dashicons-media-text"></span>
                        </div>
                        <div class="terms-details">
                            <strong>Termos de Adoção</strong><br>
                            <small>Última revisão por: Admin</small>
                        </div>
                    </div>
                ',
                'class' => 'column-title'
            ),
            array(
                'content' => '<span class="terms-type-tag terms-type-adoption">' . __('Adoção', 'amigopet-wp') . '</span>',
                'class' => 'column-type'
            ),
            array(
                'content' => '
                    <div class="version-info">
                        <strong>v2.1</strong><br>
                        <small>' . __('Principal', 'amigopet-wp') . '</small>
                    </div>
                ',
                'class' => 'column-version'
            ),
            array(
                'content' => '
                    <div class="date-info">
                        <strong>10/01/2024</strong><br>
                        <small>14:30</small>
                    </div>
                ',
                'class' => 'column-updated'
            ),
            array(
                'content' => '<span class="apwp-status-tag apwp-status-active">' . __('Ativo', 'amigopet-wp') . '</span>',
                'class' => 'column-status'
            ),
            array(
                'content' => '
                    <div class="apwp-admin-actions">
                        <a href="#" class="apwp-admin-button apwp-admin-button-secondary" title="' . esc_attr__('Editar', 'amigopet-wp') . '">
                            <span class="dashicons dashicons-edit"></span>
                        </a>
                        <a href="#" class="apwp-admin-button apwp-admin-button-info" title="' . esc_attr__('Histórico', 'amigopet-wp') . '">
                            <span class="dashicons dashicons-backup"></span>
                        </a>
                        <a href="#" class="apwp-admin-button apwp-admin-button-danger" title="' . esc_attr__('Arquivar', 'amigopet-wp') . '">
                            <span class="dashicons dashicons-archive"></span>
                        </a>
                    </div>
                ',
                'class' => 'column-actions'
            )
        )
    )
);

// Renderiza a tabela
apwp_render_admin_table($headers, $terms, array(
    'class' => 'terms-table',
    'id' => 'terms-list',
    'empty_message' => __('Nenhum termo encontrado.', 'amigopet-wp')
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
