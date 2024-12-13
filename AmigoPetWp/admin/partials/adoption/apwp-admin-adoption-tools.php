<?php
/**
 * Template de ferramentas para a listagem de adoções
 *
 * @package AmigoPet_Wp
 */

// Se este arquivo for chamado diretamente, aborte.
if (!defined('WPINC')) {
    die;
}
?>

<div class="btn-group">
    <a href="<?php echo esc_url(admin_url('admin.php?page=amigopet-wp-adoptions-new')); ?>" class="btn btn-success">
        <i class="fas fa-plus"></i>
        <?php esc_html_e('Nova Adoção', 'amigopet-wp'); ?>
    </a>
    
    <button type="button" class="btn btn-tool" data-card-widget="collapse">
        <i class="fas fa-minus"></i>
    </button>
</div>
