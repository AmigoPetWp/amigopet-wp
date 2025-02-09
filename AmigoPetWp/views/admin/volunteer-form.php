<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php echo $volunteer ? __('Editar Voluntário', 'amigopet-wp') : __('Adicionar Novo Voluntário', 'amigopet-wp'); ?></h1>
    
    <form method="post" action="" enctype="multipart/form-data">
        <?php wp_nonce_field('apwp_save_volunteer', 'apwp_volunteer_nonce'); ?>
        <input type="hidden" name="volunteer_id" value="<?php echo $volunteer ? $volunteer->ID : ''; ?>">
        
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="volunteer_name"><?php _e('Nome Completo', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <input type="text" id="volunteer_name" name="volunteer_name" class="regular-text" value="<?php echo $volunteer ? esc_attr(get_post_meta($volunteer->ID, 'volunteer_name', true)) : ''; ?>" required>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="volunteer_email"><?php _e('Email', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <input type="email" id="volunteer_email" name="volunteer_email" class="regular-text" value="<?php echo $volunteer ? esc_attr(get_post_meta($volunteer->ID, 'volunteer_email', true)) : ''; ?>" required>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="volunteer_phone"><?php _e('Telefone', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <input type="tel" id="volunteer_phone" name="volunteer_phone" class="regular-text" value="<?php echo $volunteer ? esc_attr(get_post_meta($volunteer->ID, 'volunteer_phone', true)) : ''; ?>" required>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="volunteer_address"><?php _e('Endereço', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <textarea id="volunteer_address" name="volunteer_address" class="large-text" rows="3" required><?php echo $volunteer ? esc_textarea(get_post_meta($volunteer->ID, 'volunteer_address', true)) : ''; ?></textarea>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="volunteer_birth_date"><?php _e('Data de Nascimento', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <input type="date" id="volunteer_birth_date" name="volunteer_birth_date" value="<?php echo $volunteer ? esc_attr(get_post_meta($volunteer->ID, 'volunteer_birth_date', true)) : ''; ?>" required>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="volunteer_availability"><?php _e('Disponibilidade', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <select id="volunteer_availability" name="volunteer_availability[]" multiple required>
                        <option value="morning" <?php echo $volunteer && in_array('morning', (array)get_post_meta($volunteer->ID, 'volunteer_availability', true)) ? 'selected' : ''; ?>><?php _e('Manhã', 'amigopet-wp'); ?></option>
                        <option value="afternoon" <?php echo $volunteer && in_array('afternoon', (array)get_post_meta($volunteer->ID, 'volunteer_availability', true)) ? 'selected' : ''; ?>><?php _e('Tarde', 'amigopet-wp'); ?></option>
                        <option value="evening" <?php echo $volunteer && in_array('evening', (array)get_post_meta($volunteer->ID, 'volunteer_availability', true)) ? 'selected' : ''; ?>><?php _e('Noite', 'amigopet-wp'); ?></option>
                        <option value="weekend" <?php echo $volunteer && in_array('weekend', (array)get_post_meta($volunteer->ID, 'volunteer_availability', true)) ? 'selected' : ''; ?>><?php _e('Fim de Semana', 'amigopet-wp'); ?></option>
                    </select>
                    <p class="description"><?php _e('Segure Ctrl/Cmd para selecionar múltiplos horários', 'amigopet-wp'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="volunteer_skills"><?php _e('Habilidades', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <select id="volunteer_skills" name="volunteer_skills[]" multiple>
                        <option value="pet_care" <?php echo $volunteer && in_array('pet_care', (array)get_post_meta($volunteer->ID, 'volunteer_skills', true)) ? 'selected' : ''; ?>><?php _e('Cuidados com Pets', 'amigopet-wp'); ?></option>
                        <option value="veterinary" <?php echo $volunteer && in_array('veterinary', (array)get_post_meta($volunteer->ID, 'volunteer_skills', true)) ? 'selected' : ''; ?>><?php _e('Veterinária', 'amigopet-wp'); ?></option>
                        <option value="grooming" <?php echo $volunteer && in_array('grooming', (array)get_post_meta($volunteer->ID, 'volunteer_skills', true)) ? 'selected' : ''; ?>><?php _e('Banho e Tosa', 'amigopet-wp'); ?></option>
                        <option value="driving" <?php echo $volunteer && in_array('driving', (array)get_post_meta($volunteer->ID, 'volunteer_skills', true)) ? 'selected' : ''; ?>><?php _e('Motorista', 'amigopet-wp'); ?></option>
                        <option value="photography" <?php echo $volunteer && in_array('photography', (array)get_post_meta($volunteer->ID, 'volunteer_skills', true)) ? 'selected' : ''; ?>><?php _e('Fotografia', 'amigopet-wp'); ?></option>
                        <option value="social_media" <?php echo $volunteer && in_array('social_media', (array)get_post_meta($volunteer->ID, 'volunteer_skills', true)) ? 'selected' : ''; ?>><?php _e('Redes Sociais', 'amigopet-wp'); ?></option>
                    </select>
                    <p class="description"><?php _e('Segure Ctrl/Cmd para selecionar múltiplas habilidades', 'amigopet-wp'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="volunteer_status"><?php _e('Status', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <select id="volunteer_status" name="volunteer_status" required>
                        <option value="active" <?php selected($volunteer ? get_post_meta($volunteer->ID, 'volunteer_status', true) : '', 'active'); ?>><?php _e('Ativo', 'amigopet-wp'); ?></option>
                        <option value="inactive" <?php selected($volunteer ? get_post_meta($volunteer->ID, 'volunteer_status', true) : '', 'inactive'); ?>><?php _e('Inativo', 'amigopet-wp'); ?></option>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="volunteer_photo"><?php _e('Foto', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <?php if ($volunteer && has_post_thumbnail($volunteer->ID)): ?>
                        <div class="volunteer-current-photo">
                            <?php echo get_the_post_thumbnail($volunteer->ID, 'thumbnail'); ?>
                        </div>
                    <?php endif; ?>
                    <input type="file" id="volunteer_photo" name="volunteer_photo" accept="image/*">
                    <p class="description"><?php _e('Selecione uma foto do voluntário', 'amigopet-wp'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="volunteer_notes"><?php _e('Observações', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <textarea id="volunteer_notes" name="volunteer_notes" class="large-text" rows="5"><?php echo $volunteer ? esc_textarea(get_post_meta($volunteer->ID, 'volunteer_notes', true)) : ''; ?></textarea>
                </td>
            </tr>
        </table>
        
        <?php submit_button($volunteer ? __('Atualizar Voluntário', 'amigopet-wp') : __('Adicionar Voluntário', 'amigopet-wp')); ?>
    </form>
</div>
