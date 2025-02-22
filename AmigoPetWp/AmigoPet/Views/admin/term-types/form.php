<?php
if (!defined('ABSPATH')) {
    exit;
}

$termType = $data['termType'] ?? null;
$action = $termType ? 'edit' : 'new';
$title = $action === 'edit' ? __('Editar Tipo de Termo', 'amigopet-wp') : __('Novo Tipo de Termo', 'amigopet-wp');
?>

<div class="wrap">
    <h1><?php echo esc_html($title); ?></h1>

    <?php settings_errors(); ?>

    <form method="post" action="">
        <?php wp_nonce_field('amigopet_save_term_type'); ?>
        <input type="hidden" name="action" value="<?php echo esc_attr($action); ?>">
        <?php if ($termType): ?>
            <input type="hidden" name="id" value="<?php echo esc_attr($termType->getId()); ?>">
        <?php endif; ?>

        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="name"><?php _e('Nome', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <input name="name" type="text" id="name" 
                           value="<?php echo esc_attr($termType ? $termType->getName() : ''); ?>" 
                           class="regular-text" required>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="description"><?php _e('Descrição', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <textarea name="description" id="description" class="large-text" rows="5"><?php 
                        echo esc_textarea($termType ? $termType->getDescription() : ''); 
                    ?></textarea>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="status"><?php _e('Status', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <select name="status" id="status">
                        <option value="active" <?php selected($termType ? $termType->getStatus() : 'active', 'active'); ?>>
                            <?php _e('Ativo', 'amigopet-wp'); ?>
                        </option>
                        <option value="inactive" <?php selected($termType ? $termType->getStatus() : '', 'inactive'); ?>>
                            <?php _e('Inativo', 'amigopet-wp'); ?>
                        </option>
                    </select>
                </td>
            </tr>
        </table>

        <?php submit_button(); ?>
    </form>
</div>
