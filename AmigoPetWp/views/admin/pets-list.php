<?php
if (!defined('ABSPATH')) {
    exit;
}

// Carrega a classe WP_List_Table se ainda não estiver disponível
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class APWP_Pets_List_Table extends WP_List_Table {
    public function __construct() {
        parent::__construct([
            'singular' => __('Pet', 'amigopet-wp'),
            'plural'   => __('Pets', 'amigopet-wp'),
            'ajax'     => false
        ]);
    }
    
    public function get_columns() {
        return [
            'cb'           => '<input type="checkbox" />',
            'photo'        => __('Foto', 'amigopet-wp'),
            'name'         => __('Nome', 'amigopet-wp'),
            'type'         => __('Tipo', 'amigopet-wp'),
            'breed'        => __('Raça', 'amigopet-wp'),
            'age'          => __('Idade', 'amigopet-wp'),
            'size'         => __('Porte', 'amigopet-wp'),
            'gender'       => __('Sexo', 'amigopet-wp'),
            'vaccinated'   => __('Vacinado', 'amigopet-wp'),
            'neutered'     => __('Castrado', 'amigopet-wp'),
            'date'         => __('Data', 'amigopet-wp')
        ];
    }
    
    protected function column_default($item, $column_name) {
        switch ($column_name) {
            case 'photo':
                return has_post_thumbnail($item->ID) 
                    ? get_the_post_thumbnail($item->ID, [50, 50]) 
                    : '<div style="width:50px;height:50px;background:#eee;"></div>';
            case 'name':
                return get_post_meta($item->ID, 'pet_name', true);
            case 'type':
                $types = [
                    'dog' => __('Cachorro', 'amigopet-wp'),
                    'cat' => __('Gato', 'amigopet-wp')
                ];
                $type = get_post_meta($item->ID, 'pet_type', true);
                return isset($types[$type]) ? $types[$type] : $type;
            case 'breed':
                return get_post_meta($item->ID, 'pet_breed', true);
            case 'age':
                $age = get_post_meta($item->ID, 'pet_age', true);
                return $age ? sprintf(__('%d anos', 'amigopet-wp'), $age) : '';
            case 'size':
                $sizes = [
                    'small'  => __('Pequeno', 'amigopet-wp'),
                    'medium' => __('Médio', 'amigopet-wp'),
                    'large'  => __('Grande', 'amigopet-wp')
                ];
                $size = get_post_meta($item->ID, 'pet_size', true);
                return isset($sizes[$size]) ? $sizes[$size] : $size;
            case 'gender':
                $genders = [
                    'male'   => __('Macho', 'amigopet-wp'),
                    'female' => __('Fêmea', 'amigopet-wp')
                ];
                $gender = get_post_meta($item->ID, 'pet_gender', true);
                return isset($genders[$gender]) ? $genders[$gender] : $gender;
            case 'vaccinated':
                return get_post_meta($item->ID, 'pet_vaccinated', true) ? '✓' : '✗';
            case 'neutered':
                return get_post_meta($item->ID, 'pet_neutered', true) ? '✓' : '✗';
            case 'date':
                return get_the_date('d/m/Y', $item->ID);
            default:
                return print_r($item, true);
        }
    }
    
    protected function column_name($item) {
        $actions = [
            'edit'   => sprintf(
                '<a href="%s">%s</a>',
                admin_url('admin.php?page=apwp-pet-form&pet_id=' . $item->ID),
                __('Editar', 'amigopet-wp')
            ),
            'delete' => sprintf(
                '<a href="%s" onclick="return confirm(\'%s\');">%s</a>',
                wp_nonce_url(admin_url('admin-post.php?action=apwp_delete_pet&pet_id=' . $item->ID), 'delete_pet_' . $item->ID),
                __('Tem certeza que deseja excluir este pet?', 'amigopet-wp'),
                __('Excluir', 'amigopet-wp')
            )
        ];
        
        $name = get_post_meta($item->ID, 'pet_name', true);
        return sprintf('%1$s %2$s', $name, $this->row_actions($actions));
    }
    
    protected function get_sortable_columns() {
        return [
            'name'  => ['name', true],
            'type'  => ['type', true],
            'date'  => ['date', true]
        ];
    }
    
    protected function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="pets[]" value="%s" />',
            $item->ID
        );
    }
    
    public function prepare_items() {
        $per_page = 20;
        $current_page = $this->get_pagenum();
        
        $args = [
            'post_type'      => 'apwp_pet',
            'posts_per_page' => $per_page,
            'paged'          => $current_page,
            'orderby'        => isset($_REQUEST['orderby']) ? $_REQUEST['orderby'] : 'date',
            'order'          => isset($_REQUEST['order']) ? $_REQUEST['order'] : 'DESC'
        ];
        
        // Adiciona filtros se existirem
        if (!empty($_REQUEST['pet_type'])) {
            $args['meta_query'][] = [
                'key'   => 'pet_type',
                'value' => sanitize_text_field($_REQUEST['pet_type'])
            ];
        }
        
        $query = new WP_Query($args);
        
        $this->items = $query->posts;
        
        $this->set_pagination_args([
            'total_items' => $query->found_posts,
            'per_page'    => $per_page,
            'total_pages' => ceil($query->found_posts / $per_page)
        ]);
        
        $this->_column_headers = [
            $this->get_columns(),
            [],
            $this->get_sortable_columns()
        ];
    }
}

// Cria uma instância da tabela
$pets_table = new APWP_Pets_List_Table();
$pets_table->prepare_items();
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Pets', 'amigopet-wp'); ?></h1>
    <a href="<?php echo admin_url('admin.php?page=apwp-pet-form'); ?>" class="page-title-action"><?php _e('Adicionar Novo', 'amigopet-wp'); ?></a>
    
    <?php if (isset($_GET['message'])): ?>
        <div class="updated notice is-dismissible">
            <p>
                <?php
                switch ($_GET['message']) {
                    case '1':
                        _e('Pet salvo com sucesso.', 'amigopet-wp');
                        break;
                    case '2':
                        _e('Pet excluído com sucesso.', 'amigopet-wp');
                        break;
                }
                ?>
            </p>
        </div>
    <?php endif; ?>
    
    <form method="get">
        <input type="hidden" name="page" value="apwp-pets">
        <?php $pets_table->display(); ?>
    </form>
</div>
