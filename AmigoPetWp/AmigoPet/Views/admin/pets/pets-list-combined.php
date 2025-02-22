<?php
if (!defined('ABSPATH')) {
    exit;
}

// Carrega a classe WP_List_Table se ainda não estiver disponível
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class APWP_Pets_List_Table extends WP_List_Table {
    private $species;
    
    public function __construct() {
        parent::__construct([
            'singular' => __('Pet', 'amigopet-wp'),
            'plural'   => __('Pets', 'amigopet-wp'),
            'ajax'     => true // Habilitando suporte a AJAX
        ]);
        
        // Carrega as espécies para uso nos filtros
        global $wpdb;
        $this->species = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}amigopet_pet_species");
    }
    
    public function get_columns() {
        return [
            'cb'           => '<input type="checkbox" />',
            'photo'        => __('Foto', 'amigopet-wp'),
            'name'         => __('Nome', 'amigopet-wp'),
            'species'      => __('Espécie', 'amigopet-wp'),
            'breed'        => __('Raça', 'amigopet-wp'),
            'age'          => __('Idade', 'amigopet-wp'),
            'size'         => __('Porte', 'amigopet-wp'),
            'gender'       => __('Sexo', 'amigopet-wp'),
            'status'       => __('Status', 'amigopet-wp'),
            'date'         => __('Data', 'amigopet-wp')
        ];
    }
    
    public function get_sortable_columns() {
        return [
            'name'    => ['name', true],
            'species' => ['species', false],
            'age'     => ['age', false],
            'date'    => ['date', true]
        ];
    }
    
    protected function column_default($item, $column_name) {
        switch ($column_name) {
            case 'photo':
                return has_post_thumbnail($item->ID) 
                    ? get_the_post_thumbnail($item->ID, [50, 50]) 
                    : '<div style="width:50px;height:50px;background:#eee;"></div>';
            case 'name':
                // Adiciona ações de linha
                $actions = [
                    'edit'   => sprintf('<a href="%s">%s</a>', 
                        add_query_arg(['action' => 'edit', 'id' => $item->ID]), 
                        __('Editar', 'amigopet-wp')
                    ),
                    'delete' => sprintf('<a href="%s" class="delete-pet" data-id="%d">%s</a>',
                        wp_nonce_url(admin_url('admin-post.php?action=apwp_delete_pet&id=' . $item->ID), 'delete_pet_' . $item->ID),
                        $item->ID,
                        __('Excluir', 'amigopet-wp')
                    )
                ];
                return sprintf('<strong>%1$s</strong> %2$s', 
                    get_post_meta($item->ID, 'pet_name', true),
                    $this->row_actions($actions)
                );
            case 'species':
                $species_id = get_post_meta($item->ID, 'species_id', true);
                foreach ($this->species as $species) {
                    if ($species->id == $species_id) {
                        return esc_html($species->name);
                    }
                }
                return '';
            case 'breed':
                return get_post_meta($item->ID, 'breed', true);
            case 'age':
                return get_post_meta($item->ID, 'age', true);
            case 'size':
                $sizes = [
                    'small'  => __('Pequeno', 'amigopet-wp'),
                    'medium' => __('Médio', 'amigopet-wp'),
                    'large'  => __('Grande', 'amigopet-wp')
                ];
                $size = get_post_meta($item->ID, 'size', true);
                return isset($sizes[$size]) ? $sizes[$size] : $size;
            case 'gender':
                $genders = [
                    'male'   => __('Macho', 'amigopet-wp'),
                    'female' => __('Fêmea', 'amigopet-wp')
                ];
                $gender = get_post_meta($item->ID, 'gender', true);
                return isset($genders[$gender]) ? $genders[$gender] : $gender;
            case 'status':
                $statuses = [
                    'available'   => __('Disponível', 'amigopet-wp'),
                    'adopted'     => __('Adotado', 'amigopet-wp'),
                    'unavailable' => __('Indisponível', 'amigopet-wp')
                ];
                $status = get_post_meta($item->ID, 'status', true);
                return isset($statuses[$status]) ? $statuses[$status] : $status;
            case 'date':
                return get_the_date('d/m/Y', $item->ID);
            default:
                return print_r($item, true);
        }
    }
    
    public function prepare_items() {
        $per_page = 20;
        $current_page = $this->get_pagenum();
        
        // Prepara os argumentos da query
        $args = [
            'post_type'      => 'apwp_pet',
            'posts_per_page' => $per_page,
            'paged'          => $current_page,
            'orderby'        => !empty($_REQUEST['orderby']) ? $_REQUEST['orderby'] : 'date',
            'order'          => !empty($_REQUEST['order']) ? $_REQUEST['order'] : 'DESC',
            'meta_query'     => []
        ];
        
        // Adiciona filtros se existirem
        if (!empty($_REQUEST['status'])) {
            $args['meta_query'][] = [
                'key'   => 'status',
                'value' => sanitize_text_field($_REQUEST['status'])
            ];
        }
        
        if (!empty($_REQUEST['species'])) {
            $args['meta_query'][] = [
                'key'   => 'species_id',
                'value' => intval($_REQUEST['species'])
            ];
        }
        
        if (!empty($_REQUEST['s'])) {
            $args['meta_query'][] = [
                'key'     => 'pet_name',
                'value'   => sanitize_text_field($_REQUEST['s']),
                'compare' => 'LIKE'
            ];
        }
        
        // Executa a query
        $query = new WP_Query($args);
        
        $this->items = $query->posts;
        
        // Configura a paginação
        $this->set_pagination_args([
            'total_items' => $query->found_posts,
            'per_page'    => $per_page,
            'total_pages' => ceil($query->found_posts / $per_page)
        ]);
    }
    
    public function extra_tablenav($which) {
        if ($which !== 'top') return;
        ?>
        <div class="alignleft actions">
            <select name="status" id="filter-by-status">
                <option value=""><?php _e('Todos os status', 'amigopet-wp'); ?></option>
                <option value="available" <?php selected(isset($_REQUEST['status']) ? $_REQUEST['status'] : '', 'available'); ?>>
                    <?php _e('Disponível', 'amigopet-wp'); ?>
                </option>
                <option value="adopted" <?php selected(isset($_REQUEST['status']) ? $_REQUEST['status'] : '', 'adopted'); ?>>
                    <?php _e('Adotado', 'amigopet-wp'); ?>
                </option>
                <option value="unavailable" <?php selected(isset($_REQUEST['status']) ? $_REQUEST['status'] : '', 'unavailable'); ?>>
                    <?php _e('Indisponível', 'amigopet-wp'); ?>
                </option>
            </select>
            
            <select name="species" id="filter-by-species">
                <option value=""><?php _e('Todas as espécies', 'amigopet-wp'); ?></option>
                <?php foreach ($this->species as $species): ?>
                    <option value="<?php echo esc_attr($species->id); ?>" 
                            <?php selected(isset($_REQUEST['species']) ? $_REQUEST['species'] : '', $species->id); ?>>
                        <?php echo esc_html($species->name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <?php submit_button(__('Filtrar', 'amigopet-wp'), '', 'filter_action', false); ?>
        </div>
        <?php
    }
}

// Cria uma instância da tabela
$pets_table = new APWP_Pets_List_Table();
$pets_table->prepare_items();
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Pets', 'amigopet-wp'); ?></h1>
    <a href="<?php echo add_query_arg('action', 'add'); ?>" class="page-title-action">
        <?php _e('Adicionar Novo', 'amigopet-wp'); ?>
    </a>
    
    <form id="pets-filter" method="get">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>" />
        <?php
        $pets_table->search_box(__('Buscar Pets', 'amigopet-wp'), 'pet');
        $pets_table->display();
        ?>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    // Confirmação de exclusão
    $('.delete-pet').on('click', function(e) {
        if (!confirm('<?php _e('Tem certeza que deseja excluir este pet?', 'amigopet-wp'); ?>')) {
            e.preventDefault();
        }
    });
    
    // Atualização em tempo real dos filtros
    $('#filter-by-status, #filter-by-species').on('change', function() {
        $('#pets-filter').submit();
    });
});
</script>
