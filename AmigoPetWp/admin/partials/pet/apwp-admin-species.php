<?php
/**
 * Template para a página de espécies
 *
 * @link       https://github.com/AmigoPetWp/amigopet-wp
 * @since      1.0.0
 *
 * @package    AmigoPet_Wp
 * @subpackage AmigoPet_Wp/admin/partials
 */

// Se este arquivo for chamado diretamente, aborte.
if (!defined('WPINC')) {
    die;
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php echo esc_html__('Espécies', 'amigopet-wp'); ?></h1>
    <a href="#" class="page-title-action">
        <span class="dashicons dashicons-plus" style="font-size: 16px; vertical-align: middle;"></span>
        <?php echo esc_html__('Adicionar Nova', 'amigopet-wp'); ?>
    </a>
    <hr class="wp-header-end">

    <div class="tablenav top">
        <div class="alignleft actions">
            <form method="post">
                <input type="text" name="search" placeholder="<?php echo esc_attr__('Buscar espécies...', 'amigopet-wp'); ?>">
                <?php submit_button(__('Buscar', 'amigopet-wp'), 'button', 'submit', false); ?>
            </form>
        </div>
    </div>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th scope="col" class="manage-column column-name column-primary"><?php echo esc_html__('Nome', 'amigopet-wp'); ?></th>
                <th scope="col" class="manage-column column-description"><?php echo esc_html__('Descrição', 'amigopet-wp'); ?></th>
                <th scope="col" class="manage-column column-count"><?php echo esc_html__('Total de Pets', 'amigopet-wp'); ?></th>
            </tr>
        </thead>
        <tbody id="the-list">
            <!-- Os dados serão carregados dinamicamente -->
        </tbody>
    </table>
</div>
