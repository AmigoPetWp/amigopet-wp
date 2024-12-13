<?php
/**
 * Template para a listagem de adotantes
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
$page_title = __('Adotantes', 'amigopet-wp');

// Ações do cabeçalho
$actions = array(
    array(
        'url' => '#',
        'text' => __('Exportar Lista', 'amigopet-wp'),
        'class' => 'apwp-admin-button apwp-admin-button-secondary',
        'icon' => 'dashicons-download',
        'id' => 'export-adopters'
    )
);

// Abas
$current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'all';
$tabs = array(
    array(
        'url' => admin_url('admin.php?page=amigopet-wp-adopters&tab=all'),
        'text' => __('Todos', 'amigopet-wp'),
        'class' => $current_tab === 'all' ? 'active' : '',
        'icon' => 'dashicons-groups'
    ),
    array(
        'url' => admin_url('admin.php?page=amigopet-wp-adopters&tab=approved'),
        'text' => __('Aprovados', 'amigopet-wp'),
        'class' => $current_tab === 'approved' ? 'active' : '',
        'icon' => 'dashicons-yes-alt'
    ),
    array(
        'url' => admin_url('admin.php?page=amigopet-wp-adopters&tab=pending'),
        'text' => __('Pendentes', 'amigopet-wp'),
        'class' => $current_tab === 'pending' ? 'active' : '',
        'icon' => 'dashicons-clock'
    ),
    array(
        'url' => admin_url('admin.php?page=amigopet-wp-adopters&tab=blocked'),
        'text' => __('Bloqueados', 'amigopet-wp'),
        'class' => $current_tab === 'blocked' ? 'active' : '',
        'icon' => 'dashicons-dismiss'
    )
);

// Conteúdo da página
ob_start();
?>

<!-- Filtros -->
<div class="apwp-admin-filters">
    <div class="apwp-admin-search">
        <input type="text" id="adopter-search" placeholder="<?php esc_attr_e('Buscar adotantes...', 'amigopet-wp'); ?>">
    </div>
    
    <select id="pets-filter" class="apwp-admin-select">
        <option value=""><?php esc_html_e('Todos os Pets', 'amigopet-wp'); ?></option>
        <option value="with_pets"><?php esc_html_e('Com Pets', 'amigopet-wp'); ?></option>
        <option value="without_pets"><?php esc_html_e('Sem Pets', 'amigopet-wp'); ?></option>
    </select>
    
    <div class="apwp-admin-date-range">
        <input type="date" id="date-from" class="apwp-admin-date" placeholder="<?php esc_attr_e('De', 'amigopet-wp'); ?>">
        <input type="date" id="date-to" class="apwp-admin-date" placeholder="<?php esc_attr_e('Até', 'amigopet-wp'); ?>">
    </div>
</div>

<!-- Lista de Adotantes -->
<?php
// Configura os cabeçalhos da tabela
$headers = array(
    array('text' => __('Nome', 'amigopet-wp'), 'class' => 'column-name'),
    array('text' => __('Contato', 'amigopet-wp'), 'class' => 'column-contact'),
    array('text' => __('Pets Adotados', 'amigopet-wp'), 'class' => 'column-pets'),
    array('text' => __('Última Adoção', 'amigopet-wp'), 'class' => 'column-last-adoption'),
    array('text' => __('Status', 'amigopet-wp'), 'class' => 'column-status'),
    array('text' => __('Ações', 'amigopet-wp'), 'class' => 'column-actions')
);

// Exemplo de dados (substitua por dados reais do banco)
$adopters = array(
    array(
        'class' => 'adopter-row',
        'columns' => array(
            array(
                'content' => '
                    <div class="adopter-info">
                        <div class="adopter-details">
                            <strong>João Silva</strong><br>
                            <small>CPF: ***.456.789-**</small>
                        </div>
                    </div>
                ',
                'class' => 'column-name'
            ),
            array(
                'content' => '
                    <div class="contact-info">
                        <div><i class="dashicons dashicons-email"></i> joao@email.com</div>
                        <div><i class="dashicons dashicons-phone"></i> (11) 98765-4321</div>
                        <div><i class="dashicons dashicons-location"></i> São Paulo, SP</div>
                    </div>
                ',
                'class' => 'column-contact'
            ),
            array(
                'content' => '
                    <div class="pets-preview">
                        <div class="pets-count">2 pets</div>
                        <div class="pets-thumbnails">
                            <img src="path/to/pet1.jpg" alt="Pet 1" class="pet-thumbnail" title="Rex">
                            <img src="path/to/pet2.jpg" alt="Pet 2" class="pet-thumbnail" title="Luna">
                        </div>
                    </div>
                ',
                'class' => 'column-pets'
            ),
            array(
                'content' => '
                    <div class="date-info">
                        <strong>10/01/2024</strong><br>
                        <small>14:30</small>
                    </div>
                ',
                'class' => 'column-last-adoption'
            ),
            array(
                'content' => '<span class="apwp-status-tag apwp-status-approved">' . __('Aprovado', 'amigopet-wp') . '</span>',
                'class' => 'column-status'
            ),
            array(
                'content' => '
                    <div class="apwp-admin-actions">
                        <a href="#" class="apwp-admin-button apwp-admin-button-secondary" title="' . esc_attr__('Ver Perfil', 'amigopet-wp') . '">
                            <span class="dashicons dashicons-visibility"></span>
                        </a>
                        <a href="#" class="apwp-admin-button apwp-admin-button-info" title="' . esc_attr__('Histórico', 'amigopet-wp') . '">
                            <span class="dashicons dashicons-calendar-alt"></span>
                        </a>
                        <a href="#" class="apwp-admin-button apwp-admin-button-danger" title="' . esc_attr__('Bloquear', 'amigopet-wp') . '">
                            <span class="dashicons dashicons-dismiss"></span>
                        </a>
                    </div>
                ',
                'class' => 'column-actions'
            )
        )
    )
);

// Renderiza a tabela
apwp_render_admin_table($headers, $adopters, array(
    'class' => 'adopters-table',
    'id' => 'adopters-list',
    'empty_message' => __('Nenhum adotante encontrado.', 'amigopet-wp')
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
