<?php
if (!defined('ABSPATH')) {
    exit;
}

$breed = $data['breed'] ?? null;
$species = $data['species'] ?? [];
$action = $breed ? 'edit' : 'new';
$title = $action === 'edit' ? __('Editar Raça', 'amigopet-wp') : __('Nova Raça', 'amigopet-wp');
?>

<div class="wrap">
    <h1><?php echo esc_html($title); ?></h1>

    <?php settings_errors(); ?>

    <form method="post" action="">
        <?php wp_nonce_field('amigopet_save_pet_breed'); ?>
        <input type="hidden" name="action" value="<?php echo esc_attr($action); ?>">
        <?php if ($breed): ?>
            <input type="hidden" name="id" value="<?php echo esc_attr($breed->getId()); ?>">
        <?php endif; ?>

        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="species_id"><?php _e('Espécie', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <select name="species_id" id="species_id" class="regular-text" required>
                        <option value=""><?php _e('Selecione uma espécie', 'amigopet-wp'); ?></option>
                        <?php foreach ($species as $specie): ?>
                            <option value="<?php echo esc_attr($specie->getId()); ?>" 
                                <?php selected($breed ? $breed->getSpeciesId() : '', $specie->getId()); ?>>
                                <?php echo esc_html($specie->getName()); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="name"><?php _e('Nome', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <input name="name" type="text" id="name" 
                           value="<?php echo esc_attr($breed ? $breed->getName() : ''); ?>" 
                           class="regular-text" required>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="description"><?php _e('Descrição', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <textarea name="description" id="description" class="large-text" rows="5"><?php 
                        echo esc_textarea($breed ? $breed->getDescription() : ''); 
                    ?></textarea>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="status"><?php _e('Status', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <select name="status" id="status">
                        <option value="active" <?php selected($breed ? $breed->getStatus() : 'active', 'active'); ?>>
                            <?php _e('Ativo', 'amigopet-wp'); ?>
                        </option>
                        <option value="inactive" <?php selected($breed ? $breed->getStatus() : '', 'inactive'); ?>>
                            <?php _e('Inativo', 'amigopet-wp'); ?>
                        </option>
                    </select>
                </td>
            </tr>
        </table>

        <?php submit_button(); ?>
    </form>
</div>
