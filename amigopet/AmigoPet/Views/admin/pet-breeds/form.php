<?php declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

$breed = $apwp_data['breed'] ?? null;
$species = $apwp_data['species'] ?? [];
$action = $breed ? 'edit' : 'new';
$title = $action === 'edit' ? esc_html__('Editar Raça', 'amigopet') : esc_html__('Nova Raça', 'amigopet');
?>

<div class="wrap">
    <h1> echo esc_html($title); ?></h1>

     settings_errors(); ?>

    <form method="post" action="">
         wp_nonce_field('amigopet_save_pet_breed'); ?>
        <input type="hidden" name="action" value=" echo esc_attr($action); ?>">
         if ($breed): ?>
            <input type="hidden" name="id" value=" echo esc_attr($breed->getId()); ?>">
         endif; ?>

        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="species_id"> esc_html_e('Espécie', 'amigopet'); ?></label>
                </th>
                <td>
                    <select name="species_id" id="species_id" class="regular-text" required>
                        <option value=""> esc_html_e('Selecione uma espécie', 'amigopet'); ?></option>
                         foreach ($species as $specie): ?>
                            <option value=" echo esc_attr($specie->getId()); ?>" 
                                 selected($breed ? $breed->getSpeciesId() : '', $specie->getId()); ?>>
                                 echo esc_html($specie->getName()); ?>
                            </option>
                         endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="name"> esc_html_e('Nome', 'amigopet'); ?></label>
                </th>
                <td>
                    <input name="name" type="text" id="name" 
                           value=" echo esc_attr($breed ? $breed->getName() : ''); ?>" 
                           class="regular-text" required>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="description"> esc_html_e('Descrição', 'amigopet'); ?></label>
                </th>
                <td>
                    <textarea name="description" id="description" class="large-text" rows="5"> 
                        echo esc_textarea($breed ? $breed->getDescription() : ''); 
                    ?></textarea>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="status"> esc_html_e('Status', 'amigopet'); ?></label>
                </th>
                <td>
                    <select name="status" id="status">
                        <option value="active"  selected($breed ? $breed->getStatus() : 'active', 'active'); ?>>
                             esc_html_e('Ativo', 'amigopet'); ?>
                        </option>
                        <option value="inactive"  selected($breed ? $breed->getStatus() : '', 'inactive'); ?>>
                             esc_html_e('Inativo', 'amigopet'); ?>
                        </option>
                    </select>
                </td>
            </tr>
        </table>

         submit_button(); ?>
    </form>
</div>