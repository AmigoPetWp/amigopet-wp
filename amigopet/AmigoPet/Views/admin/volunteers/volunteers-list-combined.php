<?php declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

// Carrega a classe WP_List_Table se ainda não estiver disponível
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class APWP_Volunteers_List_Table extends WP_List_Table
{
    public function __construct()
    {
        parent::__construct([
            'singular' => esc_html__('Voluntário', 'amigopet'),
            'plural' => esc_html__('Voluntários', 'amigopet'),
            'ajax' => true
        ]);
    }

    public function get_columns()
    {
        return [
            'cb' => '<input type="checkbox" />',
            'photo' => esc_html__('Foto', 'amigopet'),
            'name' => esc_html__('Nome', 'amigopet'),
            'contact' => esc_html__('Contato', 'amigopet'),
            'availability' => esc_html__('Disponibilidade', 'amigopet'),
            'skills' => esc_html__('Habilidades', 'amigopet'),
            'status' => esc_html__('Status', 'amigopet')
        ];
    }

    public function get_sortable_columns()
    {
        return [
            'name' => ['volunteer_name', false],
            'availability' => ['volunteer_availability', false],
            'status' => ['volunteer_status', false]
        ];
    }

    protected function column_default($item, $column_name)
    {
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
                        esc_html__('Editar', 'amigopet')
                    ),
                    'delete' => sprintf(
                        '<a href="%s" class="delete-volunteer" data-id="%d">%s</a>',
                        wp_nonce_url(admin_url('admin-post.php?action=apwp_delete_volunteer&id=' . $item->ID), 'delete_volunteer_' . $item->ID),
                        $item->ID,
                        esc_html__('Excluir', 'amigopet')
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
                if ($email)
                    /* translators: %1$s, %1$s */
                    $contact[] = sprintf('<a href="mailto:%1$s">%1$s</a>', esc_attr($email));
                if ($phone)
                    $contact[] = esc_html($phone);

                return implode('<br>', $contact);

            case 'availability':
                $days = get_post_meta($item->ID, 'volunteer_availability_days', true);
                $periods = get_post_meta($item->ID, 'volunteer_availability_periods', true);

                $availability = [];
                if ($days)
                    $availability[] = esc_html($days);
                if ($periods)
                    $availability[] = esc_html($periods);

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
                    'active' => ['label' => esc_html__('Ativo', 'amigopet'), 'class' => 'active'],
                    'inactive' => ['label' => esc_html__('Inativo', 'amigopet'), 'class' => 'inactive'],
                    'pending' => ['label' => esc_html__('Pendente', 'amigopet'), 'class' => 'pending']
                ];

                return isset($statuses[$status])
                    ? sprintf(
                        '<span class="volunteer-status status-%s">%s</span>',
                        $statuses[$status]['class'],
                        $statuses[$status]['label']
                    )
                    : $status;

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

        $per_page = 20;
        $current_page = $this->get_pagenum();

        // Prepara os argumentos da query
        $args = [
            'post_type' => 'apwp_volunteer',
            'posts_per_page' => $per_page,
            'paged' => $current_page,
            'orderby' => !empty($_REQUEST['orderby']) ? sanitize_key(wp_unslash($_REQUEST['orderby'])) : 'meta_value',
            'order' => !empty($_REQUEST['order']) ? sanitize_key(wp_unslash($_REQUEST['order'])) : 'ASC',
            'meta_key' => 'volunteer_name'
        ];

        // Adiciona filtros se existirem
        if (!empty($_REQUEST['volunteer_status'])) {
            $args['meta_query'][] = [
                'key' => 'volunteer_status',
                'value' => sanitize_text_field($_REQUEST['volunteer_status'])
            ];
        }

        if (!empty($_REQUEST['volunteer_skills'])) {
            $args['meta_query'][] = [
                'key' => 'volunteer_skills',
                'value' => sanitize_text_field($_REQUEST['volunteer_skills']),
                'compare' => 'LIKE'
            ];
        }

        if (!empty($_REQUEST['s'])) {
            $args['meta_query'][] = [
                'relation' => 'OR',
                [
                    'key' => 'volunteer_name',
                    'value' => sanitize_text_field($_REQUEST['s']),
                    'compare' => 'LIKE'
                ],
                [
                    'key' => 'volunteer_email',
                    'value' => sanitize_text_field($_REQUEST['s']),
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
            'per_page' => $per_page,
            'total_pages' => ceil($query->found_posts / $per_page)
        ]);
    }

    public function extra_tablenav($which)
    {
        if ($which !== 'top')
            return;
        ?>
        <div class="alignleft actions">
            <select name="volunteer_status" id="filter-by-status">
                <option value="">
                    <?php esc_html_e('Todos os status', 'amigopet'); ?>
                </option>
                <option value="active" <?php selected(isset($_REQUEST['volunteer_status']) ? $_REQUEST['volunteer_status'] : '', 'active'); ?>>
                    <?php esc_html_e('Ativo', 'amigopet'); ?>
                </option>
                <option value="inactive" <?php selected(isset($_REQUEST['volunteer_status']) ? $_REQUEST['volunteer_status'] : '', 'inactive'); ?>>
                    <?php esc_html_e('Inativo', 'amigopet'); ?>
                </option>
                <option value="pending" <?php selected(isset($_REQUEST['volunteer_status']) ? $_REQUEST['volunteer_status'] : '', 'pending'); ?>>
                    <?php esc_html_e('Pendente', 'amigopet'); ?>
                </option>
            </select>

            <select name="volunteer_skills" id="filter-by-skills">
                <option value="">
                    <?php esc_html_e('Todas as habilidades', 'amigopet'); ?>
                </option>
                <?php
                $skills = [
                    'veterinary' => esc_html__('Veterinária', 'amigopet'),
                    'grooming' => esc_html__('Banho e Tosa', 'amigopet'),
                    'transport' => esc_html__('Transporte', 'amigopet'),
                    'social' => esc_html__('Mídias Sociais', 'amigopet'),
                    'events' => esc_html__('Eventos', 'amigopet'),
                    'fundraising' => esc_html__('Captação de Recursos', 'amigopet'),
                    'other' => esc_html__('Outro', 'amigopet')
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
            <?php submit_button(esc_html__('Filtrar', 'amigopet'), '', 'filter_action', false); ?>
        </div>
        <?php
    }
}

// Cria uma instância da tabela
$apwp_volunteers_table = new APWP_Volunteers_List_Table();
$apwp_volunteers_table->prepare_items();
?>

<div class="wrap">
    <h1 class="wp-heading-inline">
        <?php esc_html_e('Voluntários', 'amigopet'); ?>
    </h1>
    <a href="<?php echo esc_url(add_query_arg('action', 'add')); ?>" class="page-title-action">
        <?php esc_html_e('Adicionar Novo', 'amigopet'); ?>
    </a>

    <?php
    // Mensagens de feedback
    if (isset($_GET['message'])) {
        $apwp_message = intval($_GET['message']);
        $apwp_messages = [
            1 => esc_html__('Voluntário salvo com sucesso.', 'amigopet'),
            2 => esc_html__('Voluntário excluído com sucesso.', 'amigopet')
        ];

        if (isset($apwp_messages[$apwp_message])) {
            /* translators: %s */
            printf('<div class="notice notice-success is-dismissible"><p>%s</p></div>', esc_html($apwp_messages[$apwp_message]));
        }
    }
    ?>

    <form id="volunteers-filter" method="get">
        <input type="hidden" name="page" value="<?php echo esc_attr(sanitize_text_field(wp_unslash($_REQUEST['page'] ?? ''))); ?>" />
        <?php
        $apwp_volunteers_table->search_box(esc_html__('Buscar Voluntários', 'amigopet'), 'volunteer');
        $apwp_volunteers_table->display();
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
    jQuery(document).ready(function ($) {
        // Confirmação de exclusão
        $('.delete-volunteer').on('click', function (e) {
            if (!confirm('<?php echo esc_js(esc_html__('Tem certeza que deseja excluir este voluntário?', 'amigopet')); ?>')) {
                e.preventDefault();
            }
        });

        // Atualização em tempo real dos filtros
        $('#filter-by-status, #filter-by-skills').on('change', function () {
            $('#volunteers-filter').submit();
        });
    });
</script>