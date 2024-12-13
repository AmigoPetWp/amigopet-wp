<?php
/**
 * Template de filtros para a listagem de adotantes
 *
 * @package AmigoPet_Wp
 */

// Se este arquivo for chamado diretamente, aborte.
if (!defined('WPINC')) {
    die;
}
?>

<form class="filters-form" method="get" action="">
    <input type="hidden" name="page" value="amigopet-wp-adopters">
    
    <div class="filter-group">
        <label for="filter-name"><?php esc_html_e('Nome', 'amigopet-wp'); ?></label>
        <input type="text" id="filter-name" name="filter_name" class="form-control" 
               value="<?php echo esc_attr($_GET['filter_name'] ?? ''); ?>">
    </div>
    
    <div class="filter-group">
        <label for="filter-document"><?php esc_html_e('CPF', 'amigopet-wp'); ?></label>
        <input type="text" id="filter-document" name="filter_document" class="form-control" 
               value="<?php echo esc_attr($_GET['filter_document'] ?? ''); ?>">
    </div>
    
    <div class="filter-group">
        <label for="filter-email"><?php esc_html_e('Email', 'amigopet-wp'); ?></label>
        <input type="email" id="filter-email" name="filter_email" class="form-control" 
               value="<?php echo esc_attr($_GET['filter_email'] ?? ''); ?>">
    </div>
    
    <div class="filter-group">
        <label for="filter-city"><?php esc_html_e('Cidade', 'amigopet-wp'); ?></label>
        <input type="text" id="filter-city" name="filter_city" class="form-control" 
               value="<?php echo esc_attr($_GET['filter_city'] ?? ''); ?>">
    </div>
    
    <div class="filter-group">
        <label>&nbsp;</label>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-search"></i>
            <?php esc_html_e('Filtrar', 'amigopet-wp'); ?>
        </button>
        
        <a href="<?php echo esc_url(admin_url('admin.php?page=amigopet-wp-adopters')); ?>" class="btn btn-default">
            <i class="fas fa-times"></i>
            <?php esc_html_e('Limpar', 'amigopet-wp'); ?>
        </a>
    </div>
</form>
