<?php
/**
 * Template de paginação para a listagem de organizações
 *
 * @package AmigoPet_Wp
 */

// Se este arquivo for chamado diretamente, aborte.
if (!defined('WPINC')) {
    die;
}

// Obtém o total de registros
$organization = new APWP_Organization();
$args = array(
    'search' => sanitize_text_field($_GET['filter_name'] ?? ''),
    'city' => sanitize_text_field($_GET['filter_city'] ?? '')
);

$total_items = $organization->count($args);
$per_page = 10;
$total_pages = ceil($total_items / $per_page);
$current_page = max(1, get_query_var('paged', 1));
?>

<?php if ($total_pages > 1) : ?>
    <div class="row">
        <div class="col-sm-12 col-md-5">
            <div class="dataTables_info" role="status" aria-live="polite">
                <?php
                $start = (($current_page - 1) * $per_page) + 1;
                $end = min($start + $per_page - 1, $total_items);
                printf(
                    esc_html__('Mostrando %1$d a %2$d de %3$d registros', 'amigopet-wp'),
                    $start,
                    $end,
                    $total_items
                );
                ?>
            </div>
        </div>
        <div class="col-sm-12 col-md-7">
            <div class="dataTables_paginate paging_simple_numbers">
                <?php
                echo paginate_links(array(
                    'base' => add_query_arg('paged', '%#%'),
                    'format' => '',
                    'prev_text' => __('&laquo;', 'amigopet-wp'),
                    'next_text' => __('&raquo;', 'amigopet-wp'),
                    'total' => $total_pages,
                    'current' => $current_page,
                    'type' => 'list',
                    'before_page_number' => '<span class="screen-reader-text">' . __('Page', 'amigopet-wp') . ' </span>'
                ));
                ?>
            </div>
        </div>
    </div>
<?php endif; ?>
