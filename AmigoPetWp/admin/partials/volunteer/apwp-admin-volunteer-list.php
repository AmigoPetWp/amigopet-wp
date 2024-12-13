<?php
/**
 * Template para a listagem de voluntários
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
$page_title = __('Voluntários', 'amigopet-wp');

// Ações do cabeçalho
$actions = array(
    array(
        'url' => admin_url('admin.php?page=amigopet-wp-volunteers-new'),
        'text' => __('Adicionar Voluntário', 'amigopet-wp'),
        'class' => 'apwp-admin-button apwp-admin-button-primary',
        'icon' => 'dashicons-plus-alt'
    ),
    array(
        'url' => '#',
        'text' => __('Exportar Lista', 'amigopet-wp'),
        'class' => 'apwp-admin-button apwp-admin-button-secondary',
        'icon' => 'dashicons-download',
        'id' => 'export-volunteers'
    )
);

// Abas
$current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'all';
$tabs = array(
    array(
        'url' => admin_url('admin.php?page=amigopet-wp-volunteers&tab=all'),
        'text' => __('Todos', 'amigopet-wp'),
        'class' => $current_tab === 'all' ? 'active' : '',
        'icon' => 'dashicons-groups'
    ),
    array(
        'url' => admin_url('admin.php?page=amigopet-wp-volunteers&tab=active'),
        'text' => __('Ativos', 'amigopet-wp'),
        'class' => $current_tab === 'active' ? 'active' : '',
        'icon' => 'dashicons-yes-alt'
    ),
    array(
        'url' => admin_url('admin.php?page=amigopet-wp-volunteers&tab=inactive'),
        'text' => __('Inativos', 'amigopet-wp'),
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
        <input type="text" id="volunteer-search" placeholder="<?php esc_attr_e('Buscar voluntários...', 'amigopet-wp'); ?>">
    </div>
    
    <select id="role-filter" class="apwp-admin-select">
        <option value=""><?php esc_html_e('Todas as Funções', 'amigopet-wp'); ?></option>
        <option value="caregiver"><?php esc_html_e('Cuidador', 'amigopet-wp'); ?></option>
        <option value="driver"><?php esc_html_e('Motorista', 'amigopet-wp'); ?></option>
        <option value="vet"><?php esc_html_e('Veterinário', 'amigopet-wp'); ?></option>
    </select>
    
    <select id="availability-filter" class="apwp-admin-select">
        <option value=""><?php esc_html_e('Disponibilidade', 'amigopet-wp'); ?></option>
        <option value="weekday"><?php esc_html_e('Dias de Semana', 'amigopet-wp'); ?></option>
        <option value="weekend"><?php esc_html_e('Fins de Semana', 'amigopet-wp'); ?></option>
        <option value="flexible"><?php esc_html_e('Flexível', 'amigopet-wp'); ?></option>
    </select>
</div>

<!-- Lista de Voluntários -->
<?php
// Configura os cabeçalhos da tabela
$headers = array(
    array('text' => __('Nome', 'amigopet-wp'), 'class' => 'column-name'),
    array('text' => __('Contato', 'amigopet-wp'), 'class' => 'column-contact'),
    array('text' => __('Função', 'amigopet-wp'), 'class' => 'column-role'),
    array('text' => __('Disponibilidade', 'amigopet-wp'), 'class' => 'column-availability'),
    array('text' => __('Status', 'amigopet-wp'), 'class' => 'column-status'),
    array('text' => __('Ações', 'amigopet-wp'), 'class' => 'column-actions')
);

// Exemplo de dados (substitua por dados reais do banco)
$volunteers = array(
    array(
        'class' => 'volunteer-row',
        'columns' => array(
            array(
                'content' => '
                    <div class="volunteer-info">
                        <img src="path/to/photo.jpg" alt="Volunteer Photo" class="volunteer-photo">
                        <div class="volunteer-details">
                            <strong>Maria Santos</strong><br>
                            <small>Desde Jan/2024</small>
                        </div>
                    </div>
                ',
                'class' => 'column-name'
            ),
            array(
                'content' => '
                    <div class="contact-info">
                        <div><i class="dashicons dashicons-email"></i> maria@email.com</div>
                        <div><i class="dashicons dashicons-phone"></i> (11) 98765-4321</div>
                    </div>
                ',
                'class' => 'column-contact'
            ),
            array(
                'content' => '
                    <div class="role-tags">
                        <span class="apwp-role-tag">Cuidador</span>
                        <span class="apwp-role-tag">Motorista</span>
                    </div>
                ',
                'class' => 'column-role'
            ),
            array(
                'content' => __('Fins de Semana', 'amigopet-wp'),
                'class' => 'column-availability'
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
                            <span class="dashicons dashicons-calendar-alt"></span>
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
apwp_render_admin_table($headers, $volunteers, array(
    'class' => 'volunteers-table',
    'id' => 'volunteers-list',
    'empty_message' => __('Nenhum voluntário encontrado.', 'amigopet-wp')
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
