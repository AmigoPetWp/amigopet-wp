<?php
/**
 * Template para a listagem de organizações
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
$page_title = __('Organizações', 'amigopet-wp');

// Ações do cabeçalho
$actions = array(
    array(
        'url' => admin_url('admin.php?page=amigopet-wp-organizations-new'),
        'text' => __('Adicionar Organização', 'amigopet-wp'),
        'class' => 'apwp-admin-button apwp-admin-button-primary',
        'icon' => 'dashicons-plus-alt'
    ),
    array(
        'url' => '#',
        'text' => __('Exportar Lista', 'amigopet-wp'),
        'class' => 'apwp-admin-button apwp-admin-button-secondary',
        'icon' => 'dashicons-download',
        'id' => 'export-organizations'
    )
);

// Abas
$current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'all';
$tabs = array(
    array(
        'url' => admin_url('admin.php?page=amigopet-wp-organizations&tab=all'),
        'text' => __('Todas', 'amigopet-wp'),
        'class' => $current_tab === 'all' ? 'active' : '',
        'icon' => 'dashicons-building'
    ),
    array(
        'url' => admin_url('admin.php?page=amigopet-wp-organizations&tab=active'),
        'text' => __('Ativas', 'amigopet-wp'),
        'class' => $current_tab === 'active' ? 'active' : '',
        'icon' => 'dashicons-yes-alt'
    ),
    array(
        'url' => admin_url('admin.php?page=amigopet-wp-organizations&tab=inactive'),
        'text' => __('Inativas', 'amigopet-wp'),
        'class' => $current_tab === 'inactive' ? 'active' : '',
        'icon' => 'dashicons-marker'
    )
);

// Conteúdo da página
ob_start();
?>

<!-- Filtros -->
<div class="apwp-admin-filters">
    <div class="apwp-admin-search">
        <input type="text" id="organization-search" placeholder="<?php esc_attr_e('Buscar organizações...', 'amigopet-wp'); ?>">
    </div>
    
    <select id="type-filter" class="apwp-admin-select">
        <option value=""><?php esc_html_e('Todos os Tipos', 'amigopet-wp'); ?></option>
        <option value="ong"><?php esc_html_e('ONG', 'amigopet-wp'); ?></option>
        <option value="shelter"><?php esc_html_e('Abrigo', 'amigopet-wp'); ?></option>
        <option value="clinic"><?php esc_html_e('Clínica', 'amigopet-wp'); ?></option>
    </select>
    
    <select id="location-filter" class="apwp-admin-select">
        <option value=""><?php esc_html_e('Todas as Regiões', 'amigopet-wp'); ?></option>
        <option value="north"><?php esc_html_e('Zona Norte', 'amigopet-wp'); ?></option>
        <option value="south"><?php esc_html_e('Zona Sul', 'amigopet-wp'); ?></option>
        <option value="east"><?php esc_html_e('Zona Leste', 'amigopet-wp'); ?></option>
        <option value="west"><?php esc_html_e('Zona Oeste', 'amigopet-wp'); ?></option>
    </select>
</div>

<!-- Lista de Organizações -->
<?php
// Configura os cabeçalhos da tabela
$headers = array(
    array('text' => __('Organização', 'amigopet-wp'), 'class' => 'column-org'),
    array('text' => __('Contato', 'amigopet-wp'), 'class' => 'column-contact'),
    array('text' => __('Tipo', 'amigopet-wp'), 'class' => 'column-type'),
    array('text' => __('Pets', 'amigopet-wp'), 'class' => 'column-pets'),
    array('text' => __('Status', 'amigopet-wp'), 'class' => 'column-status'),
    array('text' => __('Ações', 'amigopet-wp'), 'class' => 'column-actions')
);

// Exemplo de dados (substitua por dados reais do banco)
$organizations = array(
    array(
        'class' => 'organization-row',
        'columns' => array(
            array(
                'content' => '
                    <div class="organization-info">
                        <img src="path/to/logo.jpg" alt="Organization Logo" class="organization-logo">
                        <div class="organization-details">
                            <strong>Amigos dos Pets</strong><br>
                            <small>CNPJ: 12.345.678/0001-90</small>
                        </div>
                    </div>
                ',
                'class' => 'column-org'
            ),
            array(
                'content' => '
                    <div class="contact-info">
                        <div><i class="dashicons dashicons-email"></i> contato@amigodospets.org</div>
                        <div><i class="dashicons dashicons-phone"></i> (11) 3456-7890</div>
                        <div><i class="dashicons dashicons-location"></i> São Paulo, SP - Zona Sul</div>
                    </div>
                ',
                'class' => 'column-contact'
            ),
            array(
                'content' => '
                    <span class="org-type-tag org-type-ong">ONG</span>
                ',
                'class' => 'column-type'
            ),
            array(
                'content' => '
                    <div class="pets-stats">
                        <div class="pets-stat">
                            <strong>45</strong>
                            <small>' . __('Total', 'amigopet-wp') . '</small>
                        </div>
                        <div class="pets-stat">
                            <strong>32</strong>
                            <small>' . __('Disponíveis', 'amigopet-wp') . '</small>
                        </div>
                    </div>
                ',
                'class' => 'column-pets'
            ),
            array(
                'content' => '<span class="apwp-status-tag apwp-status-active">' . __('Ativa', 'amigopet-wp') . '</span>',
                'class' => 'column-status'
            ),
            array(
                'content' => '
                    <div class="apwp-admin-actions">
                        <a href="#" class="apwp-admin-button apwp-admin-button-secondary" title="' . esc_attr__('Editar', 'amigopet-wp') . '">
                            <span class="dashicons dashicons-edit"></span>
                        </a>
                        <a href="#" class="apwp-admin-button apwp-admin-button-info" title="' . esc_attr__('Ver Pets', 'amigopet-wp') . '">
                            <span class="dashicons dashicons-pets"></span>
                        </a>
                        <a href="#" class="apwp-admin-button apwp-admin-button-danger" title="' . esc_attr__('Desativar', 'amigopet-wp') . '">
                            <span class="dashicons dashicons-remove"></span>
                        </a>
                    </div>
                ',
                'class' => 'column-actions'
            )
        )
    )
);

// Renderiza a tabela
apwp_render_admin_table($headers, $organizations, array(
    'class' => 'organizations-table',
    'id' => 'organizations-list',
    'empty_message' => __('Nenhuma organização encontrada.', 'amigopet-wp')
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
