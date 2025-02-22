<?php
if (!defined('ABSPATH')) {
    exit;
}

$species = $data['species'] ?? null;
$action = $species ? 'edit' : 'new';
$title = $action === 'edit' ? __('Editar Espécie', 'amigopet-wp') : __('Nova Espécie', 'amigopet-wp');
?>

<div class="wrap">
    <h1><?php echo esc_html($title); ?></h1>

    <?php settings_errors(); ?>

    <form method="post" action="">
        <?php wp_nonce_field('amigopet_save_pet_species'); ?>
        <input type="hidden" name="action" value="<?php echo esc_attr($action); ?>">
        <?php if ($species): ?>
            <input type="hidden" name="id" value="<?php echo esc_attr($species->getId()); ?>">
        <?php endif; ?>

        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="name"><?php _e('Nome', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <input name="name" type="text" id="name" 
                           value="<?php echo esc_attr($species ? $species->getName() : ''); ?>" 
                           class="regular-text" required>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="description"><?php _e('Descrição', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <textarea name="description" id="description" class="large-text" rows="5"><?php 
                        echo esc_textarea($species ? $species->getDescription() : ''); 
                    ?></textarea>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="status"><?php _e('Status', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <select name="status" id="status">
                        <option value="active" <?php selected($species ? $species->getStatus() : 'active', 'active'); ?>>
                            <?php _e('Ativo', 'amigopet-wp'); ?>
                        </option>
                        <option value="inactive" <?php selected($species ? $species->getStatus() : '', 'inactive'); ?>>
                            <?php _e('Inativo', 'amigopet-wp'); ?>
                        </option>
                    </select>
                </td>
            </tr>
        </table>

        <?php submit_button(); ?>
    </form>
</div>
