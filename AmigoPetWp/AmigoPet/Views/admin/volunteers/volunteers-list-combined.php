<?php
if (!defined('ABSPATH')) {
    exit;
}

// Carrega a classe WP_List_Table se ainda não estiver disponível
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class APWP_Volunteers_List_Table extends WP_List_Table {
    public function __construct() {
        parent::__construct([
            'singular' => __('Voluntário', 'amigopet-wp'),
            'plural'   => __('Voluntários', 'amigopet-wp'),
            'ajax'     => true
        ]);
    }
    
    public function get_columns() {
        return [
            'cb'             => '<input type="checkbox" />',
            'photo'          => __('Foto', 'amigopet-wp'),
            'name'           => __('Nome', 'amigopet-wp'),
            'contact'        => __('Contato', 'amigopet-wp'),
            'availability'   => __('Disponibilidade', 'amigopet-wp'),
            'skills'         => __('Habilidades', 'amigopet-wp'),
            'status'         => __('Status', 'amigopet-wp')
        ];
    }
    
    public function get_sortable_columns() {
        return [
            'name'         => ['volunteer_name', false],
            'availability' => ['volunteer_availability', false],
            'status'       => ['volunteer_status', false]
        ];
    }
    
    protected function column_default($item, $column_name) {
        switch ($column_name) {
            case 'photo':
                return has_post_thumbnail($item->ID) 
                    ? get_the_post_thumbnail($item->ID, [50, 50]) 
                    : '<div class="volunteer-placeholder-photo"></div>';
                
            case 'name':
                $actions = [
                    'edit' => sprintf(
                        '<a href="%s">%s</a>',
                        add_query_arg(['action' => 'edit', 'id' => $item->ID]),
                        __('Editar', 'amigopet-wp')
                    ),
                    'delete' => sprintf(
                        '<a href="%s" class="delete-volunteer" data-id="%d">%s</a>',
                        wp_nonce_url(admin_url('admin-post.php?action=apwp_delete_volunteer&id=' . $item->ID), 'delete_volunteer_' . $item->ID),
                        $item->ID,
                        __('Excluir', 'amigopet-wp')
                    )
                ];
                
                return sprintf(
                    '<strong>%s</strong> %s',
                    get_post_meta($item->ID, 'volunteer_name', true),
                    $this->row_actions($actions)
                );
                
            case 'contact':
                $email = get_post_meta($item->ID, 'volunteer_email', true);
                $phone = get_post_meta($item->ID, 'volunteer_phone', true);
                
                $contact = [];
                if ($email) $contact[] = sprintf('<a href="mailto:%1$s">%1$s</a>', esc_attr($email));
                if ($phone) $contact[] = esc_html($phone);
                
                return implode('<br>', $contact);
                
            case 'availability':
                $days = get_post_meta($item->ID, 'volunteer_availability_days', true);
                $periods = get_post_meta($item->ID, 'volunteer_availability_periods', true);
                
                $availability = [];
                if ($days) $availability[] = esc_html($days);
                if ($periods) $availability[] = esc_html($periods);
                
                return implode('<br>', $availability);
                
            case 'skills':
                $skills = get_post_meta($item->ID, 'volunteer_skills', true);
                if (is_array($skills)) {
                    return implode(', ', array_map('esc_html', $skills));
                }
                return esc_html($skills);
                
            case 'status':
                $status = get_post_meta($item->ID, 'volunteer_status', true);
                $statuses = [
                    'active'    => ['label' => __('Ativo', 'amigopet-wp'), 'class' => 'active'],
                    'inactive'  => ['label' => __('Inativo', 'amigopet-wp'), 'class' => 'inactive'],
                    'pending'   => ['label' => __('Pendente', 'amigopet-wp'), 'class' => 'pending']
                ];
                
                return isset($statuses[$status]) 
                    ? sprintf('<span class="volunteer-status status-%s">%s</span>', 
                        $statuses[$status]['class'], 
                        $statuses[$status]['label']
                    )
                    : $status;
                
            default:
                return print_r($item, true);
        }
    }
    
    public function prepare_items() {
        $columns = $this->get_columns();
        $hidden = [];
        $sortable = $this->get_sortable_columns();
        
        $this->_column_headers = [$columns, $hidden, $sortable];
        
        $per_page = 20;
        $current_page = $this->get_pagenum();
        
        // Prepara os argumentos da query
        $args = [
            'post_type'      => 'apwp_volunteer',
            'posts_per_page' => $per_page,
            'paged'          => $current_page,
            'orderby'        => !empty($_REQUEST['orderby']) ? $_REQUEST['orderby'] : 'meta_value',
            'order'          => !empty($_REQUEST['order']) ? $_REQUEST['order'] : 'ASC',
            'meta_key'       => 'volunteer_name'
        ];
        
        // Adiciona filtros se existirem
        if (!empty($_REQUEST['volunteer_status'])) {
            $args['meta_query'][] = [
                'key'   => 'volunteer_status',
                'value' => sanitize_text_field($_REQUEST['volunteer_status'])
            ];
        }
        
        if (!empty($_REQUEST['volunteer_skills'])) {
            $args['meta_query'][] = [
                'key'     => 'volunteer_skills',
                'value'   => sanitize_text_field($_REQUEST['volunteer_skills']),
                'compare' => 'LIKE'
            ];
        }
        
        if (!empty($_REQUEST['s'])) {
            $args['meta_query'][] = [
                'relation' => 'OR',
                [
                    'key'     => 'volunteer_name',
                    'value'   => sanitize_text_field($_REQUEST['s']),
                    'compare' => 'LIKE'
                ],
                [
                    'key'     => 'volunteer_email',
                    'value'   => sanitize_text_field($_REQUEST['s']),
                    'compare' => 'LIKE'
                ]
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
            <select name="volunteer_status" id="filter-by-status">
                <option value=""><?php _e('Todos os status', 'amigopet-wp'); ?></option>
                <option value="active" <?php selected(isset($_REQUEST['volunteer_status']) ? $_REQUEST['volunteer_status'] : '', 'active'); ?>>
                    <?php _e('Ativo', 'amigopet-wp'); ?>
                </option>
                <option value="inactive" <?php selected(isset($_REQUEST['volunteer_status']) ? $_REQUEST['volunteer_status'] : '', 'inactive'); ?>>
                    <?php _e('Inativo', 'amigopet-wp'); ?>
                </option>
                <option value="pending" <?php selected(isset($_REQUEST['volunteer_status']) ? $_REQUEST['volunteer_status'] : '', 'pending'); ?>>
                    <?php _e('Pendente', 'amigopet-wp'); ?>
                </option>
            </select>
            
            <select name="volunteer_skills" id="filter-by-skills">
                <option value=""><?php _e('Todas as habilidades', 'amigopet-wp'); ?></option>
                <?php
                $skills = [
                    'veterinary'  => __('Veterinária', 'amigopet-wp'),
                    'grooming'    => __('Banho e Tosa', 'amigopet-wp'),
                    'transport'   => __('Transporte', 'amigopet-wp'),
                    'social'      => __('Mídias Sociais', 'amigopet-wp'),
                    'events'      => __('Eventos', 'amigopet-wp'),
                    'fundraising' => __('Captação de Recursos', 'amigopet-wp'),
                    'other'       => __('Outro', 'amigopet-wp')
                ];
                
                foreach ($skills as $value => $label) {
                    printf(
                        '<option value="%s" %s>%s</option>',
                        esc_attr($value),
                        selected(isset($_REQUEST['volunteer_skills']) ? $_REQUEST['volunteer_skills'] : '', $value, false),
                        esc_html($label)
                    );
                }
                ?>
            </select>
            
            <?php submit_button(__('Filtrar', 'amigopet-wp'), '', 'filter_action', false); ?>
        </div>
        <?php
    }
}

// Cria uma instância da tabela
$volunteers_table = new APWP_Volunteers_List_Table();
$volunteers_table->prepare_items();
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Voluntários', 'amigopet-wp'); ?></h1>
    <a href="<?php echo add_query_arg('action', 'add'); ?>" class="page-title-action">
        <?php _e('Adicionar Novo', 'amigopet-wp'); ?>
    </a>
    
    <?php
    // Mensagens de feedback
    if (isset($_GET['message'])) {
        $message = intval($_GET['message']);
        $messages = [
            1 => __('Voluntário salvo com sucesso.', 'amigopet-wp'),
            2 => __('Voluntário excluído com sucesso.', 'amigopet-wp')
        ];
        
        if (isset($messages[$message])) {
            printf('<div class="notice notice-success is-dismissible"><p>%s</p></div>', $messages[$message]);
        }
    }
    ?>
    
    <form id="volunteers-filter" method="get">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>" />
        <?php
        $volunteers_table->search_box(__('Buscar Voluntários', 'amigopet-wp'), 'volunteer');
        $volunteers_table->display();
        ?>
    </form>
</div>

<style>
.volunteer-placeholder-photo {
    width: 50px;
    height: 50px;
    background: #f0f0f1;
    border: 1px solid #ddd;
    border-radius: 50%;
}

.volunteer-status {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 3px;
    font-size: 12px;
    line-height: 1;
}

.status-active {
    background-color: #c6e1c6;
    color: #5b841b;
}

.status-inactive {
    background-color: #f1adad;
    color: #8b0000;
}

.status-pending {
    background-color: #f0f6fc;
    color: #1d2327;
}

.column-photo {
    width: 60px;
}

.column-name {
    width: 20%;
}

.column-contact,
.column-availability {
    width: 15%;
}

.column-skills {
    width: 20%;
}

.column-status {
    width: 10%;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Confirmação de exclusão
    $('.delete-volunteer').on('click', function(e) {
        if (!confirm('<?php _e('Tem certeza que deseja excluir este voluntário?', 'amigopet-wp'); ?>')) {
            e.preventDefault();
        }
    });
    
    // Atualização em tempo real dos filtros
    $('#filter-by-status, #filter-by-skills').on('change', function() {
        $('#volunteers-filter').submit();
    });
});
</script>
