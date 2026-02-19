<?php declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class APWP_Pets_List_Table extends WP_List_Table
{
    private array $species = [];

    public function __construct()
    {
        parent::__construct([
            'singular' => esc_html__('Pet', 'amigopet'),
            'plural' => esc_html__('Pets', 'amigopet'),
            'ajax' => false
        ]);

        global $wpdb;
        $table = preg_replace('/[^a-zA-Z0-9_]/', '', $wpdb->prefix . 'amigopet_pet_species');
        if (!is_string($table) || $table === '') {
            $table = 'wp_amigopet_pet_species';
        }
        $rows = $wpdb->get_results(
            "SELECT id, name FROM `{$table}` ORDER BY name ASC"
        );
        $this->species = is_array($rows) ? $rows : [];
    }

    public function get_columns()
    {
        return [
            'cb' => '<input type="checkbox" />',
            'photo' => esc_html__('Foto', 'amigopet'),
            'name' => esc_html__('Nome', 'amigopet'),
            'species' => esc_html__('Espécie', 'amigopet'),
            'breed' => esc_html__('Raça', 'amigopet'),
            'age' => esc_html__('Idade', 'amigopet'),
            'size' => esc_html__('Porte', 'amigopet'),
            'gender' => esc_html__('Sexo', 'amigopet'),
            'status' => esc_html__('Status', 'amigopet'),
            'date' => esc_html__('Data', 'amigopet')
        ];
    }

    public function get_sortable_columns()
    {
        return [
            'name' => ['title', true],
            'date' => ['date', true]
        ];
    }

    protected function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="pet_ids[]" value="%s" />',
            esc_attr((string) $item->ID)
        );
    }

    protected function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'photo':
                return has_post_thumbnail($item->ID)
                    ? (string) get_the_post_thumbnail($item->ID, [50, 50])
                    : '<div style="width:50px;height:50px;background:#eee;"></div>';
            case 'name':
                $editUrl = add_query_arg(
                    ['action' => 'edit', 'id' => (int) $item->ID],
                    menu_page_url('amigopet-pets', false)
                );
                $deleteUrl = wp_nonce_url(
                    admin_url('admin-post.php?action=apwp_delete_pet&id=' . (int) $item->ID),
                    'apwp_delete_pet'
                );
                $actions = [
                    'edit' => sprintf(
                        '<a href="%s">%s</a>',
                        esc_url($editUrl),
                        esc_html__('Editar', 'amigopet')
                    ),
                    'delete' => sprintf(
                        '<a href="%s" class="delete-pet" data-id="%d">%s</a>',
                        esc_url($deleteUrl),
                        (int) $item->ID,
                        esc_html__('Excluir', 'amigopet')
                    )
                ];
                $name = get_post_meta($item->ID, 'pet_name', true);
                return sprintf(
                    '<strong>%s</strong> %s',
                    esc_html((string) $name),
                    $this->row_actions($actions)
                );
            case 'species':
                $speciesId = (int) get_post_meta($item->ID, 'species_id', true);
                foreach ($this->species as $species) {
                    if ((int) $species->id === $speciesId) {
                        return esc_html((string) $species->name);
                    }
                }
                return '';
            case 'breed':
                return esc_html((string) get_post_meta($item->ID, 'breed', true));
            case 'age':
                return esc_html((string) get_post_meta($item->ID, 'age', true));
            case 'size':
                $size = (string) get_post_meta($item->ID, 'size', true);
                $sizes = [
                    'small' => esc_html__('Pequeno', 'amigopet'),
                    'medium' => esc_html__('Médio', 'amigopet'),
                    'large' => esc_html__('Grande', 'amigopet')
                ];
                return esc_html($sizes[$size] ?? $size);
            case 'gender':
                $gender = (string) get_post_meta($item->ID, 'gender', true);
                $genders = [
                    'male' => esc_html__('Macho', 'amigopet'),
                    'female' => esc_html__('Fêmea', 'amigopet')
                ];
                return esc_html($genders[$gender] ?? $gender);
            case 'status':
                $status = (string) get_post_meta($item->ID, 'status', true);
                $statuses = [
                    'available' => esc_html__('Disponível', 'amigopet'),
                    'adopted' => esc_html__('Adotado', 'amigopet'),
                    'unavailable' => esc_html__('Indisponível', 'amigopet')
                ];
                return esc_html($statuses[$status] ?? $status);
            case 'date':
                return esc_html((string) get_the_date('d/m/Y', $item->ID));
            default:
                return '';
        }
    }

    public function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = [];
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = [$columns, $hidden, $sortable];

        $perPage = 20;
        $paged = $this->get_pagenum();
        $orderbyInput = isset($_REQUEST['orderby']) ? sanitize_text_field(wp_unslash((string) $_REQUEST['orderby'])) : 'date';
        $orderInput = isset($_REQUEST['order']) ? strtoupper(sanitize_text_field(wp_unslash((string) $_REQUEST['order']))) : 'DESC';
        $orderby = in_array($orderbyInput, ['title', 'date'], true) ? $orderbyInput : 'date';
        $order = in_array($orderInput, ['ASC', 'DESC'], true) ? $orderInput : 'DESC';

        $args = [
            'post_type' => 'amigopetwp_pet',
            'posts_per_page' => $perPage,
            'paged' => $paged,
            'orderby' => $orderby,
            'order' => $order,
            'meta_query' => []
        ];

        $status = isset($_REQUEST['status']) ? sanitize_text_field(wp_unslash((string) $_REQUEST['status'])) : '';
        if ($status !== '') {
            $args['meta_query'][] = [
                'key' => 'status',
                'value' => $status
            ];
        }

        $species = isset($_REQUEST['species']) ? (int) wp_unslash((string) $_REQUEST['species']) : 0;
        if ($species > 0) {
            $args['meta_query'][] = [
                'key' => 'species_id',
                'value' => $species
            ];
        }

        $search = isset($_REQUEST['s']) ? sanitize_text_field(wp_unslash((string) $_REQUEST['s'])) : '';
        if ($search !== '') {
            $args['meta_query'][] = [
                'key' => 'pet_name',
                'value' => $search,
                'compare' => 'LIKE'
            ];
        }

        $query = new WP_Query($args);
        $this->items = is_array($query->posts) ? $query->posts : [];
        $this->set_pagination_args([
            'total_items' => (int) $query->found_posts,
            'per_page' => $perPage,
            'total_pages' => (int) max(1, ceil((int) $query->found_posts / $perPage))
        ]);
    }

    public function extra_tablenav($which)
    {
        if ($which !== 'top') {
            return;
        }
        $statusValue = isset($_REQUEST['status']) ? sanitize_text_field(wp_unslash((string) $_REQUEST['status'])) : '';
        $speciesValue = isset($_REQUEST['species']) ? (int) wp_unslash((string) $_REQUEST['species']) : 0;
        ?>
        <div class="alignleft actions">
            <select name="status" id="filter-by-status">
                <option value=""><?php esc_html_e('Todos os status', 'amigopet'); ?></option>
                <option value="available" <?php selected($statusValue, 'available'); ?>><?php esc_html_e('Disponível', 'amigopet'); ?></option>
                <option value="adopted" <?php selected($statusValue, 'adopted'); ?>><?php esc_html_e('Adotado', 'amigopet'); ?></option>
                <option value="unavailable" <?php selected($statusValue, 'unavailable'); ?>><?php esc_html_e('Indisponível', 'amigopet'); ?></option>
            </select>

            <select name="species" id="filter-by-species">
                <option value=""><?php esc_html_e('Todas as espécies', 'amigopet'); ?></option>
                <?php foreach ($this->species as $species): ?>
                    <option value="<?php echo esc_attr((string) $species->id); ?>" <?php selected($speciesValue, (int) $species->id); ?>>
                        <?php echo esc_html((string) $species->name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php submit_button(esc_html__('Filtrar', 'amigopet'), '', 'filter_action', false); ?>
        </div>
        <?php
    }
}

$apwp_pets_table = new APWP_Pets_List_Table();
$apwp_pets_table->prepare_items();
$message = isset($_GET['message']) ? sanitize_text_field(wp_unslash((string) $_GET['message'])) : '';
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e('Pets', 'amigopet'); ?></h1>
    <a href="<?php echo esc_url(add_query_arg('action', 'add', menu_page_url('amigopet-pets', false))); ?>" class="page-title-action">
        <?php esc_html_e('Adicionar Novo', 'amigopet'); ?>
    </a>

    <?php if ($message !== ''): ?>
        <div class="notice notice-info is-dismissible"><p><?php echo esc_html($message); ?></p></div>
    <?php endif; ?>

    <form id="pets-filter" method="get">
        <input type="hidden" name="page" value="<?php echo esc_attr(isset($_REQUEST['page']) ? sanitize_text_field(wp_unslash($_REQUEST['page'])) : ''); ?>" />
        <?php
        $apwp_pets_table->search_box(esc_html__('Buscar Pets', 'amigopet'), 'pet');
        $apwp_pets_table->display();
        ?>
    </form>
</div>

<script>
jQuery(document).ready(function ($) {
    $('.delete-pet').on('click', function (e) {
        if (!window.confirm('<?php echo esc_js(__('Tem certeza que deseja excluir este pet?', 'amigopet')); ?>')) {
            e.preventDefault();
        }
    });

    $('#filter-by-status, #filter-by-species').on('change', function () {
        $('#pets-filter').submit();
    });
});
</script>