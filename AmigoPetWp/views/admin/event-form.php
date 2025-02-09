<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php echo $event ? __('Editar Evento', 'amigopet-wp') : __('Adicionar Novo Evento', 'amigopet-wp'); ?></h1>
    
    <form method="post" action="" enctype="multipart/form-data">
        <?php wp_nonce_field('apwp_save_event', 'apwp_event_nonce'); ?>
        <input type="hidden" name="event_id" value="<?php echo $event ? $event->ID : ''; ?>">
        
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="event_title"><?php _e('Título do Evento', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <input type="text" id="event_title" name="event_title" class="regular-text" value="<?php echo $event ? esc_attr(get_post_meta($event->ID, 'event_title', true)) : ''; ?>" required>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="event_type"><?php _e('Tipo do Evento', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <select id="event_type" name="event_type" required>
                        <option value=""><?php _e('Selecione...', 'amigopet-wp'); ?></option>
                        <option value="adoption_fair" <?php selected($event ? get_post_meta($event->ID, 'event_type', true) : '', 'adoption_fair'); ?>><?php _e('Feira de Adoção', 'amigopet-wp'); ?></option>
                        <option value="fundraising" <?php selected($event ? get_post_meta($event->ID, 'event_type', true) : '', 'fundraising'); ?>><?php _e('Arrecadação de Fundos', 'amigopet-wp'); ?></option>
                        <option value="vaccination" <?php selected($event ? get_post_meta($event->ID, 'event_type', true) : '', 'vaccination'); ?>><?php _e('Campanha de Vacinação', 'amigopet-wp'); ?></option>
                        <option value="other" <?php selected($event ? get_post_meta($event->ID, 'event_type', true) : '', 'other'); ?>><?php _e('Outro', 'amigopet-wp'); ?></option>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="event_date"><?php _e('Data do Evento', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <input type="date" id="event_date" name="event_date" value="<?php echo $event ? esc_attr(get_post_meta($event->ID, 'event_date', true)) : ''; ?>" required>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="event_time"><?php _e('Horário do Evento', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <input type="time" id="event_time" name="event_time" value="<?php echo $event ? esc_attr(get_post_meta($event->ID, 'event_time', true)) : ''; ?>" required>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="event_location"><?php _e('Local do Evento', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <input type="text" id="event_location" name="event_location" class="regular-text" value="<?php echo $event ? esc_attr(get_post_meta($event->ID, 'event_location', true)) : ''; ?>" required>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="event_description"><?php _e('Descrição do Evento', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <textarea id="event_description" name="event_description" class="large-text" rows="5" required><?php echo $event ? esc_textarea(get_post_meta($event->ID, 'event_description', true)) : ''; ?></textarea>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="event_organizer"><?php _e('Organizador', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <input type="text" id="event_organizer" name="event_organizer" class="regular-text" value="<?php echo $event ? esc_attr(get_post_meta($event->ID, 'event_organizer', true)) : ''; ?>" required>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="event_contact"><?php _e('Contato', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <input type="text" id="event_contact" name="event_contact" class="regular-text" value="<?php echo $event ? esc_attr(get_post_meta($event->ID, 'event_contact', true)) : ''; ?>" required>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="event_photo"><?php _e('Imagem do Evento', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <?php if ($event && has_post_thumbnail($event->ID)): ?>
                        <div class="event-current-photo">
                            <?php echo get_the_post_thumbnail($event->ID, 'thumbnail'); ?>
                        </div>
                    <?php endif; ?>
                    <input type="file" id="event_photo" name="event_photo" accept="image/*">
                    <p class="description"><?php _e('Selecione uma imagem para o evento', 'amigopet-wp'); ?></p>
                </td>
            </tr>
        </table>
        
        <?php submit_button($event ? __('Atualizar Evento', 'amigopet-wp') : __('Adicionar Evento', 'amigopet-wp')); ?>
    </form>
</div>
