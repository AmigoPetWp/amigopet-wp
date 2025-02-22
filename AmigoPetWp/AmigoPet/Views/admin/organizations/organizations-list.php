<?php
/**
 * @var array $report
 * @var \AmigoPetWp\Admin\Tables\APWP_Organizations_List_Table $list_table
 */
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Organizações', 'amigopet-wp'); ?></h1>
    <a href="<?php echo admin_url('admin.php?page=amigopet-wp-organizations&action=new'); ?>" class="page-title-action">
        <?php _e('Adicionar Nova', 'amigopet-wp'); ?>
    </a>

    <div class="apwp-dashboard-stats">
        <div class="apwp-stat-card">
            <h3><?php _e('Total de Organizações', 'amigopet-wp'); ?></h3>
            <p class="apwp-stat-number"><?php echo esc_html($report['total']); ?></p>
        </div>
        <div class="apwp-stat-card">
            <h3><?php _e('Organizações Ativas', 'amigopet-wp'); ?></h3>
            <p class="apwp-stat-number"><?php echo esc_html($report['active']); ?></p>
        </div>
        <div class="apwp-stat-card">
            <h3><?php _e('Organizações Inativas', 'amigopet-wp'); ?></h3>
            <p class="apwp-stat-number"><?php echo esc_html($report['inactive']); ?></p>
        </div>
        <div class="apwp-stat-card">
            <h3><?php _e('Organizações Pendentes', 'amigopet-wp'); ?></h3>
            <p class="apwp-stat-number"><?php echo esc_html($report['pending']); ?></p>
        </div>
    </div>

    <form method="post">
        <?php
        $list_table->search_box(__('Buscar', 'amigopet-wp'), 'search_id');
        $list_table->display();
        ?>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    $('.apwp-activate-organization').click(function(e) {
        e.preventDefault();
        if (confirm('<?php _e('Tem certeza que deseja ativar esta organização?', 'amigopet-wp'); ?>')) {
            $(this).closest('form').submit();
        }
    });

    $('.apwp-deactivate-organization').click(function(e) {
        e.preventDefault();
        if (confirm('<?php _e('Tem certeza que deseja desativar esta organização?', 'amigopet-wp'); ?>')) {
            $(this).closest('form').submit();
        }
    });
});
</script>
