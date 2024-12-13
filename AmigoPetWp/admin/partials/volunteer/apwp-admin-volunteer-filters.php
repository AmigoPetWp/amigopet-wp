<?php
/**
 * Template de filtros para a listagem de voluntários
 *
 * @package AmigoPet_Wp
 */

// Se este arquivo for chamado diretamente, aborte.
if (!defined('WPINC')) {
    die;
}
?>

<form class="filters-form" method="get" action="">
    <input type="hidden" name="page" value="amigopet-wp-volunteers">
    
    <div class="filter-group">
        <label for="filter-name"><?php esc_html_e('Nome', 'amigopet-wp'); ?></label>
        <input type="text" id="filter-name" name="filter_name" class="form-control" 
               value="<?php echo esc_attr($_GET['filter_name'] ?? ''); ?>">
    </div>
    
    <div class="filter-group">
        <label for="filter-skills"><?php esc_html_e('Habilidades', 'amigopet-wp'); ?></label>
        <select id="filter-skills" name="filter_skills" class="form-control">
            <option value=""><?php esc_html_e('Todas', 'amigopet-wp'); ?></option>
            <?php
            $skills = APWP_Volunteer_Model::get_available_skills();
            foreach ($skills as $skill) :
            ?>
                <option value="<?php echo esc_attr($skill); ?>" <?php selected($_GET['filter_skills'] ?? '', $skill); ?>>
                    <?php echo esc_html($skill); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <div class="filter-group">
        <label for="filter-status"><?php esc_html_e('Status', 'amigopet-wp'); ?></label>
        <select id="filter-status" name="filter_status" class="form-control">
            <option value=""><?php esc_html_e('Todos', 'amigopet-wp'); ?></option>
            <option value="active" <?php selected($_GET['filter_status'] ?? '', 'active'); ?>>
                <?php esc_html_e('Ativo', 'amigopet-wp'); ?>
            </option>
            <option value="inactive" <?php selected($_GET['filter_status'] ?? '', 'inactive'); ?>>
                <?php esc_html_e('Inativo', 'amigopet-wp'); ?>
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
        
        <a href="<?php echo esc_url(admin_url('admin.php?page=amigopet-wp-volunteers')); ?>" class="btn btn-default">
            <i class="fas fa-times"></i>
            <?php esc_html_e('Limpar', 'amigopet-wp'); ?>
        </a>
    </div>
</form>
