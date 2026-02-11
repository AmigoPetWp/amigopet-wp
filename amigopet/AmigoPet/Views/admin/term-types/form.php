<?php declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

$termType = $apwp_data['termType'] ?? null;
$action = $termType ? 'edit' : 'new';
$title = $action === 'edit' ? esc_html__('Editar Tipo de Termo', 'amigopet') : esc_html__('Novo Tipo de Termo', 'amigopet');
?>

<div class="wrap">
    <h1> echo esc_html($title); ?></h1>

     settings_errors(); ?>

    <form method="post" action="">
         wp_nonce_field('amigopet_save_term_type'); ?>
        <input type="hidden" name="action" value=" echo esc_attr($action); ?>">
         if ($termType): ?>
            <input type="hidden" name="id" value=" echo esc_attr($termType->getId()); ?>">
         endif; ?>

        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="name"> esc_html_e('Nome', 'amigopet'); ?></label>
                </th>
                <td>
                    <input name="name" type="text" id="name" 
                           value=" echo esc_attr($termType ? $termType->getName() : ''); ?>" 
                           class="regular-text" required>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="description"> esc_html_e('Descrição', 'amigopet'); ?></label>
                </th>
                <td>
                    <textarea name="description" id="description" class="large-text" rows="5"> 
                        echo esc_textarea($termType ? $termType->getDescription() : ''); 
                    ?></textarea>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="status"> esc_html_e('Status', 'amigopet'); ?></label>
                </th>
                <td>
                    <select name="status" id="status">
                        <option value="active"  selected($termType ? $termType->getStatus() : 'active', 'active'); ?>>
                             esc_html_e('Ativo', 'amigopet'); ?>
                        </option>
                        <option value="inactive"  selected($termType ? $termType->getStatus() : '', 'inactive'); ?>>
                             esc_html_e('Inativo', 'amigopet'); ?>
                        </option>
                    </select>
                </td>
            </tr>
        </table>

         submit_button(); ?>
    </form>
</div>