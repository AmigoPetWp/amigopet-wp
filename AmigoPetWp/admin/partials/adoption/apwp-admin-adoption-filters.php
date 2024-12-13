<?php
/**
 * Template de filtros para a listagem de adoções
 *
 * @package AmigoPet_Wp
 */

// Se este arquivo for chamado diretamente, aborte.
if (!defined('WPINC')) {
    die;
}
?>

<form class="filters-form" method="get" action="">
    <input type="hidden" name="page" value="amigopet-wp-adoptions">
    
    <div class="filter-group">
        <label for="filter-pet"><?php esc_html_e('Pet', 'amigopet-wp'); ?></label>
        <input type="text" id="filter-pet" name="filter_pet" class="form-control" 
               value="<?php echo esc_attr($_GET['filter_pet'] ?? ''); ?>">
    </div>
    
    <div class="filter-group">
        <label for="filter-adopter"><?php esc_html_e('Adotante', 'amigopet-wp'); ?></label>
        <input type="text" id="filter-adopter" name="filter_adopter" class="form-control" 
               value="<?php echo esc_attr($_GET['filter_adopter'] ?? ''); ?>">
    </div>
    
    <div class="filter-group">
        <label for="filter-status"><?php esc_html_e('Status', 'amigopet-wp'); ?></label>
        <select id="filter-status" name="filter_status" class="form-control">
            <option value=""><?php esc_html_e('Todos', 'amigopet-wp'); ?></option>
            <option value="pending" <?php selected($_GET['filter_status'] ?? '', 'pending'); ?>>
                <?php esc_html_e('Pendente', 'amigopet-wp'); ?>
            </option>
            <option value="approved" <?php selected($_GET['filter_status'] ?? '', 'approved'); ?>>
                <?php esc_html_e('Aprovada', 'amigopet-wp'); ?>
            </option>
            <option value="rejected" <?php selected($_GET['filter_status'] ?? '', 'rejected'); ?>>
                <?php esc_html_e('Rejeitada', 'amigopet-wp'); ?>
            </option>
        </select>
    </div>
    
    <div class="filter-group">
        <label for="filter-date"><?php esc_html_e('Data', 'amigopet-wp'); ?></label>
        <input type="date" id="filter-date" name="filter_date" class="form-control" 
               value="<?php echo esc_attr($_GET['filter_date'] ?? ''); ?>">
    </div>
    
    <div class="filter-group">
        <label>&nbsp;</label>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-search"></i>
            <?php esc_html_e('Filtrar', 'amigopet-wp'); ?>
        </button>
        
        <a href="<?php echo esc_url(admin_url('admin.php?page=amigopet-wp-adoptions')); ?>" class="btn btn-default">
            <i class="fas fa-times"></i>
            <?php esc_html_e('Limpar', 'amigopet-wp'); ?>
        </a>
    </div>
</form>
