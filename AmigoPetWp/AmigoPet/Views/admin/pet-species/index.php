<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Espécies de Pets', 'amigopet-wp'); ?></h1>
    <a href="<?php echo admin_url('admin.php?page=amigopet-pet-species&action=new'); ?>" class="page-title-action">
        <?php _e('Adicionar Nova', 'amigopet-wp'); ?>
    </a>
    <hr class="wp-header-end">

    <?php settings_errors(); ?>

    <form method="get">
        <input type="hidden" name="page" value="amigopet-pet-species">
        <?php
        $list_table = new \AmigoPetWp\Controllers\Admin\Tables\APWP_Pet_Species_List_Table();
        $list_table->prepare_items();
        $list_table->search_box(__('Buscar', 'amigopet-wp'), 'search_id');
        $list_table->display();
        ?>
    </form>
</div>
