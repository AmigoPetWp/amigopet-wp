<?php
/**
 * Template de paginação para a listagem de voluntários
 *
 * @package AmigoPet_Wp
 */

// Se este arquivo for chamado diretamente, aborte.
if (!defined('WPINC')) {
    die;
}

// Obtém o total de páginas
$total_pages = APWP_Volunteer_Model::get_total_pages(array(
    'name' => $_GET['filter_name'] ?? '',
    'skills' => $_GET['filter_skills'] ?? '',
    'status' => $_GET['filter_status'] ?? '',
    'per_page' => 10
));

// Página atual
$current_page = $_GET['paged'] ?? 1;

// Se não houver mais de uma página, não exibe a paginação
if ($total_pages <= 1) {
    return;
}
?>

<div class="row">
    <div class="col-sm-12 col-md-5">
        <div class="dataTables_info" role="status" aria-live="polite">
            <?php
            $start = (($current_page - 1) * 10) + 1;
            $end = min($start + 9, APWP_Volunteer_Model::get_total_volunteers());
            printf(
                esc_html__('Mostrando %1$d até %2$d de %3$d registros', 'amigopet-wp'),
                $start,
                $end,
                APWP_Volunteer_Model::get_total_volunteers()
            );
            ?>
        </div>
    </div>
    <div class="col-sm-12 col-md-7">
        <div class="dataTables_paginate paging_simple_numbers">
            <ul class="pagination">
                <?php
                // Link para primeira página
                $first_url = add_query_arg('paged', 1);
                if ($current_page > 1) :
                ?>
                    <li class="paginate_button page-item">
                        <a href="<?php echo esc_url($first_url); ?>" class="page-link">
                            <i class="fas fa-angle-double-left"></i>
                        </a>
                    </li>
                <?php endif; ?>
                
                <?php
                // Link para página anterior
                if ($current_page > 1) :
                    $prev_url = add_query_arg('paged', $current_page - 1);
                ?>
                    <li class="paginate_button page-item">
                        <a href="<?php echo esc_url($prev_url); ?>" class="page-link">
                            <i class="fas fa-angle-left"></i>
                        </a>
                    </li>
                <?php endif; ?>
                
                <?php
                // Links numéricos
                for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++) :
                    $page_url = add_query_arg('paged', $i);
                ?>
                    <li class="paginate_button page-item <?php echo $i == $current_page ? 'active' : ''; ?>">
                        <a href="<?php echo esc_url($page_url); ?>" class="page-link"><?php echo esc_html($i); ?></a>
                    </li>
                <?php endfor; ?>
                
                <?php
                // Link para próxima página
                if ($current_page < $total_pages) :
                    $next_url = add_query_arg('paged', $current_page + 1);
                ?>
                    <li class="paginate_button page-item">
                        <a href="<?php echo esc_url($next_url); ?>" class="page-link">
                            <i class="fas fa-angle-right"></i>
                        </a>
                    </li>
                <?php endif; ?>
                
                <?php
                // Link para última página
                $last_url = add_query_arg('paged', $total_pages);
                if ($current_page < $total_pages) :
                ?>
                    <li class="paginate_button page-item">
                        <a href="<?php echo esc_url($last_url); ?>" class="page-link">
                            <i class="fas fa-angle-double-right"></i>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>
