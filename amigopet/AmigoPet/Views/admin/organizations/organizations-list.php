<?php declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

/**
 * @var array $report
 * @var \AmigoPetWp\Admin\Tables\APWP_Organizations_List_Table $list_table
 */
?>

<div class="wrap">
    <h1 class="wp-heading-inline"> esc_html_e('Organizações', 'amigopet'); ?></h1>
    <a href=" echo esc_url(admin_url('admin.php?page=amigopet-organizations&action=new'); ?>" class="page-title-action">
         esc_html_e('Adicionar Nova', 'amigopet'); ?>
    </a>

    <div class="apwp-dashboard-stats">
        <div class="apwp-stat-card">
            <h3> esc_html_e('Total de Organizações', 'amigopet'); ?></h3>
            <p class="apwp-stat-number"> echo esc_html($report['total']); ?></p>
        </div>
        <div class="apwp-stat-card">
            <h3> esc_html_e('Organizações Ativas', 'amigopet'); ?></h3>
            <p class="apwp-stat-number"> echo esc_html($report['active']); ?></p>
        </div>
        <div class="apwp-stat-card">
            <h3> esc_html_e('Organizações Inativas', 'amigopet'); ?></h3>
            <p class="apwp-stat-number"> echo esc_html($report['inactive']); ?></p>
        </div>
        <div class="apwp-stat-card">
            <h3> esc_html_e('Organizações Pendentes', 'amigopet'); ?></h3>
            <p class="apwp-stat-number"> echo esc_html($report['pending']); ?></p>
        </div>
    </div>

    <form method="post">
        
        $list_table->search_box(esc_html__('Buscar', 'amigopet'), 'search_id');
        $list_table->display();
        ?>
    </form>
</div>

<script>
    jQuery(document).ready(function ($) {
        $('.apwp-activate-organization').click(function (e) {
            e.preventDefault();
            if (confirm(' echo esc_js(esc_html__('Tem certeza que deseja ativar esta organização?', 'amigopet')); ?>')) {
                $(this).closest('form').submit();
            }
        });

        $('.apwp-deactivate-organization').click(function (e) {
            e.preventDefault();
            if (confirm(' echo esc_js(esc_html__('Tem certeza que deseja desativar esta organização?', 'amigopet')); ?>')) {
                $(this).closest('form').submit();
            }
        });
    });
</script>