<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php echo $pet ? __('Editar Pet', 'amigopet-wp') : __('Adicionar Novo Pet', 'amigopet-wp'); ?></h1>
    
    <form method="post" action="" enctype="multipart/form-data">
        <?php wp_nonce_field('apwp_save_pet', 'apwp_pet_nonce'); ?>
        <input type="hidden" name="pet_id" value="<?php echo $pet ? $pet->ID : ''; ?>">
        
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="pet_name"><?php _e('Nome', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <input type="text" id="pet_name" name="pet_name" class="regular-text" value="<?php echo $pet ? esc_attr(get_post_meta($pet->ID, 'pet_name', true)) : ''; ?>" required>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="pet_type"><?php _e('Tipo', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <select id="pet_type" name="pet_type" required>
                        <option value=""><?php _e('Selecione...', 'amigopet-wp'); ?></option>
                        <option value="dog" <?php selected($pet ? get_post_meta($pet->ID, 'pet_type', true) : '', 'dog'); ?>><?php _e('Cachorro', 'amigopet-wp'); ?></option>
                        <option value="cat" <?php selected($pet ? get_post_meta($pet->ID, 'pet_type', true) : '', 'cat'); ?>><?php _e('Gato', 'amigopet-wp'); ?></option>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="pet_breed"><?php _e('Raça', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <input type="text" id="pet_breed" name="pet_breed" class="regular-text" value="<?php echo $pet ? esc_attr(get_post_meta($pet->ID, 'pet_breed', true)) : ''; ?>">
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="pet_age"><?php _e('Idade Aproximada', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <input type="number" id="pet_age" name="pet_age" class="small-text" min="0" value="<?php echo $pet ? esc_attr(get_post_meta($pet->ID, 'pet_age', true)) : ''; ?>" required>
                    <span class="description"><?php _e('Em anos', 'amigopet-wp'); ?></span>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="pet_size"><?php _e('Porte', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <select id="pet_size" name="pet_size" required>
                        <option value=""><?php _e('Selecione...', 'amigopet-wp'); ?></option>
                        <option value="small" <?php selected($pet ? get_post_meta($pet->ID, 'pet_size', true) : '', 'small'); ?>><?php _e('Pequeno', 'amigopet-wp'); ?></option>
                        <option value="medium" <?php selected($pet ? get_post_meta($pet->ID, 'pet_size', true) : '', 'medium'); ?>><?php _e('Médio', 'amigopet-wp'); ?></option>
                        <option value="large" <?php selected($pet ? get_post_meta($pet->ID, 'pet_size', true) : '', 'large'); ?>><?php _e('Grande', 'amigopet-wp'); ?></option>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="pet_gender"><?php _e('Sexo', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <select id="pet_gender" name="pet_gender" required>
                        <option value=""><?php _e('Selecione...', 'amigopet-wp'); ?></option>
                        <option value="male" <?php selected($pet ? get_post_meta($pet->ID, 'pet_gender', true) : '', 'male'); ?>><?php _e('Macho', 'amigopet-wp'); ?></option>
                        <option value="female" <?php selected($pet ? get_post_meta($pet->ID, 'pet_gender', true) : '', 'female'); ?>><?php _e('Fêmea', 'amigopet-wp'); ?></option>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="pet_vaccinated"><?php _e('Vacinado', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <input type="checkbox" id="pet_vaccinated" name="pet_vaccinated" value="1" <?php checked($pet ? get_post_meta($pet->ID, 'pet_vaccinated', true) : '', '1'); ?>>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="pet_neutered"><?php _e('Castrado', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <input type="checkbox" id="pet_neutered" name="pet_neutered" value="1" <?php checked($pet ? get_post_meta($pet->ID, 'pet_neutered', true) : '', '1'); ?>>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="pet_description"><?php _e('Descrição', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <textarea id="pet_description" name="pet_description" class="large-text" rows="5"><?php echo $pet ? esc_textarea(get_post_meta($pet->ID, 'pet_description', true)) : ''; ?></textarea>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="pet_photo"><?php _e('Foto', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <?php if ($pet && has_post_thumbnail($pet->ID)): ?>
                        <div class="pet-current-photo">
                            <?php echo get_the_post_thumbnail($pet->ID, 'thumbnail'); ?>
                        </div>
                    <?php endif; ?>
                    <input type="file" id="pet_photo" name="pet_photo" accept="image/*">
                    <p class="description"><?php _e('Selecione uma foto do pet', 'amigopet-wp'); ?></p>
                </td>
            </tr>
        </table>
        
        <?php submit_button($pet ? __('Atualizar Pet', 'amigopet-wp') : __('Adicionar Pet', 'amigopet-wp')); ?>
    </form>
</div>
