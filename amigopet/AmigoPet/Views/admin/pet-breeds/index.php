<?php declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

?>

<div class="wrap">
    <h1 class="wp-heading-inline"> esc_html_e('Raças de Pets', 'amigopet'); ?></h1>
    <a href=" echo esc_url(admin_url('admin.php?page=amigopet-pet-breeds&action=new'); ?>" class="page-title-action">
         esc_html_e('Adicionar Nova', 'amigopet'); ?>
    </a>
    <hr class="wp-header-end">

     settings_errors(); ?>

    <form method="get">
        <input type="hidden" name="page" value="amigopet-pet-breeds">
        
        $list_table = new \AmigoPetWp\Controllers\Admin\Tables\APWP_Pet_Breeds_List_Table();
        $list_table->prepare_items();
        $list_table->views();
        $list_table->search_box(esc_html__('Buscar', 'amigopet'), 'search_id');
        $list_table->display();
        ?>
    </form>
</div>