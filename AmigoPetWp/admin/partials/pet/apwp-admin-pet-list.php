<?php
/**
 * Template para a listagem de pets
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
$page_title = __('Pets', 'amigopet-wp');

// Ações do cabeçalho
$actions = array(
    array(
        'url' => admin_url('admin.php?page=amigopet-wp-pets-new'),
        'text' => __('Adicionar Pet', 'amigopet-wp'),
        'class' => 'apwp-admin-button apwp-admin-button-primary',
        'icon' => 'dashicons-plus-alt'
    ),
    array(
        'url' => '#',
        'text' => __('Importar', 'amigopet-wp'),
        'class' => 'apwp-admin-button apwp-admin-button-secondary',
        'icon' => 'dashicons-upload',
        'id' => 'import-pets'
    )
);

// Abas
$current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'all';
$tabs = array(
    array(
        'url' => admin_url('admin.php?page=amigopet-wp-pets&tab=all'),
        'text' => __('Todos', 'amigopet-wp'),
        'class' => $current_tab === 'all' ? 'active' : '',
        'icon' => 'dashicons-pets'
    ),
    array(
        'url' => admin_url('admin.php?page=amigopet-wp-pets&tab=available'),
        'text' => __('Disponíveis', 'amigopet-wp'),
        'class' => $current_tab === 'available' ? 'active' : '',
        'icon' => 'dashicons-yes-alt'
    ),
    array(
        'url' => admin_url('admin.php?page=amigopet-wp-pets&tab=adopted'),
        'text' => __('Adotados', 'amigopet-wp'),
        'class' => $current_tab === 'adopted' ? 'active' : '',
        'icon' => 'dashicons-heart'
    )
);

// Conteúdo da página
ob_start();
?>

<!-- Filtros -->
<div class="apwp-admin-filters">
    <div class="apwp-admin-search">
        <input type="text" id="pet-search" placeholder="<?php esc_attr_e('Buscar pets...', 'amigopet-wp'); ?>">
    </div>
    
    <select id="species-filter" class="apwp-admin-select">
        <option value=""><?php esc_html_e('Todas as Espécies', 'amigopet-wp'); ?></option>
        <option value="dog"><?php esc_html_e('Cachorro', 'amigopet-wp'); ?></option>
        <option value="cat"><?php esc_html_e('Gato', 'amigopet-wp'); ?></option>
    </select>
    
    <select id="size-filter" class="apwp-admin-select">
        <option value=""><?php esc_html_e('Todos os Portes', 'amigopet-wp'); ?></option>
        <option value="small"><?php esc_html_e('Pequeno', 'amigopet-wp'); ?></option>
        <option value="medium"><?php esc_html_e('Médio', 'amigopet-wp'); ?></option>
        <option value="large"><?php esc_html_e('Grande', 'amigopet-wp'); ?></option>
    </select>
</div>

<!-- Lista de Pets -->
<?php
// Configura os cabeçalhos da tabela
$headers = array(
    array('text' => __('Foto', 'amigopet-wp'), 'class' => 'column-photo'),
    array('text' => __('Nome', 'amigopet-wp'), 'class' => 'column-name'),
    array('text' => __('Espécie', 'amigopet-wp'), 'class' => 'column-species'),
    array('text' => __('Raça', 'amigopet-wp'), 'class' => 'column-breed'),
    array('text' => __('Idade', 'amigopet-wp'), 'class' => 'column-age'),
    array('text' => __('Status', 'amigopet-wp'), 'class' => 'column-status'),
    array('text' => __('Ações', 'amigopet-wp'), 'class' => 'column-actions')
);

// Exemplo de dados (substitua por dados reais do banco)
$pets = array(
    array(
        'class' => 'pet-row',
        'columns' => array(
            array(
                'content' => '<img src="path/to/photo.jpg" alt="Pet Photo" class="pet-photo">',
                'class' => 'column-photo'
            ),
            array(
                'content' => 'Rex',
                'class' => 'column-name'
            ),
            array(
                'content' => __('Cachorro', 'amigopet-wp'),
                'class' => 'column-species'
            ),
            array(
                'content' => __('Vira-lata', 'amigopet-wp'),
                'class' => 'column-breed'
            ),
            array(
                'content' => '2 anos',
                'class' => 'column-age'
            ),
            array(
                'content' => '<span class="apwp-status-tag apwp-status-available">' . __('Disponível', 'amigopet-wp') . '</span>',
                'class' => 'column-status'
            ),
            array(
                'content' => '
                    <div class="apwp-admin-actions">
                        <a href="#" class="apwp-admin-button apwp-admin-button-secondary" title="' . esc_attr__('Editar', 'amigopet-wp') . '">
                            <span class="dashicons dashicons-edit"></span>
                        </a>
                        <a href="#" class="apwp-admin-button apwp-admin-button-danger" data-confirm="' . esc_attr__('Tem certeza que deseja excluir este pet?', 'amigopet-wp') . '" title="' . esc_attr__('Excluir', 'amigopet-wp') . '">
                            <span class="dashicons dashicons-trash"></span>
                        </a>
                    </div>
                ',
                'class' => 'column-actions'
            )
        )
    )
);

// Renderiza a tabela
apwp_render_admin_table($headers, $pets, array(
    'class' => 'pets-table',
    'id' => 'pets-list',
    'empty_message' => __('Nenhum pet encontrado.', 'amigopet-wp')
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
