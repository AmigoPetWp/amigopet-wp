<?php
/**
 * Template de filtros para a listagem de organizações
 *
 * @package AmigoPet_Wp
 */

// Se este arquivo for chamado diretamente, aborte.
if (!defined('WPINC')) {
    die;
}
?>

<form class="filters-form" method="get" action="">
    <input type="hidden" name="page" value="amigopet-wp-organizations">
    
    <div class="filter-group">
        <label for="filter-name"><?php esc_html_e('Nome', 'amigopet-wp'); ?></label>
        <input type="text" id="filter-name" name="filter_name" class="form-control" 
               value="<?php echo esc_attr($_GET['filter_name'] ?? ''); ?>">
    </div>
    
    <div class="filter-group">
        <label for="filter-type"><?php esc_html_e('Tipo', 'amigopet-wp'); ?></label>
        <select id="filter-type" name="filter_type" class="form-control">
            <option value=""><?php esc_html_e('Todos', 'amigopet-wp'); ?></option>
            <option value="shelter" <?php selected($_GET['filter_type'] ?? '', 'shelter'); ?>>
                <?php esc_html_e('Abrigo', 'amigopet-wp'); ?>
            </option>
            <option value="clinic" <?php selected($_GET['filter_type'] ?? '', 'clinic'); ?>>
                <?php esc_html_e('Clínica', 'amigopet-wp'); ?>
            </option>
            <option value="ngo" <?php selected($_GET['filter_type'] ?? '', 'ngo'); ?>>
                <?php esc_html_e('ONG', 'amigopet-wp'); ?>
            </option>
        </select>
    </div>
    
    <div class="filter-group">
        <label for="filter-status"><?php esc_html_e('Status', 'amigopet-wp'); ?></label>
        <select id="filter-status" name="filter_status" class="form-control">
            <option value=""><?php esc_html_e('Todos', 'amigopet-wp'); ?></option>
            <option value="active" <?php selected($_GET['filter_status'] ?? '', 'active'); ?>>
                <?php esc_html_e('Ativa', 'amigopet-wp'); ?>
            </option>
            <option value="inactive" <?php selected($_GET['filter_status'] ?? '', 'inactive'); ?>>
                <?php esc_html_e('Inativa', 'amigopet-wp'); ?>
            </option>
            <option value="pending" <?php selected($_GET['filter_status'] ?? '', 'pending'); ?>>
                <?php esc_html_e('Pendente', 'amigopet-wp'); ?>
            </option>
        </select>
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
        
        <a href="<?php echo esc_url(admin_url('admin.php?page=amigopet-wp-organizations')); ?>" class="btn btn-default">
            <i class="fas fa-times"></i>
            <?php esc_html_e('Limpar', 'amigopet-wp'); ?>
        </a>
    </div>
</form>
