<?php
/**
 * Template de filtros para a listagem de pets
 *
 * @package AmigoPet_Wp
 */

// Se este arquivo for chamado diretamente, aborte.
if (!defined('WPINC')) {
    die;
}
?>

<form class="filters-form" method="get" action="">
    <input type="hidden" name="page" value="amigopet-wp-pets">
    
    <div class="filter-group">
        <label for="filter-name"><?php esc_html_e('Nome', 'amigopet-wp'); ?></label>
        <input type="text" id="filter-name" name="filter_name" class="form-control" 
               value="<?php echo esc_attr($_GET['filter_name'] ?? ''); ?>">
    </div>
    
    <div class="filter-group">
        <label for="filter-species"><?php esc_html_e('Espécie', 'amigopet-wp'); ?></label>
        <select id="filter-species" name="filter_species" class="form-control">
            <option value=""><?php esc_html_e('Todas', 'amigopet-wp'); ?></option>
            <option value="dog" <?php selected($_GET['filter_species'] ?? '', 'dog'); ?>>
                <?php esc_html_e('Cachorro', 'amigopet-wp'); ?>
            </option>
            <option value="cat" <?php selected($_GET['filter_species'] ?? '', 'cat'); ?>>
                <?php esc_html_e('Gato', 'amigopet-wp'); ?>
            </option>
        </select>
    </div>
    
    <div class="filter-group">
        <label for="filter-status"><?php esc_html_e('Status', 'amigopet-wp'); ?></label>
        <select id="filter-status" name="filter_status" class="form-control">
            <option value=""><?php esc_html_e('Todos', 'amigopet-wp'); ?></option>
            <option value="available" <?php selected($_GET['filter_status'] ?? '', 'available'); ?>>
                <?php esc_html_e('Disponível', 'amigopet-wp'); ?>
            </option>
            <option value="adopted" <?php selected($_GET['filter_status'] ?? '', 'adopted'); ?>>
                <?php esc_html_e('Adotado', 'amigopet-wp'); ?>
            </option>
            <option value="pending" <?php selected($_GET['filter_status'] ?? '', 'pending'); ?>>
                <?php esc_html_e('Pendente', 'amigopet-wp'); ?>
            </option>
        </select>
    </div>
    
    <div class="filter-group">
        <label>&nbsp;</label>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-search"></i>
            <?php esc_html_e('Filtrar', 'amigopet-wp'); ?>
        </button>
        
        <a href="<?php echo esc_url(admin_url('admin.php?page=amigopet-wp-pets')); ?>" class="btn btn-default">
            <i class="fas fa-times"></i>
            <?php esc_html_e('Limpar', 'amigopet-wp'); ?>
        </a>
    </div>
</form>
