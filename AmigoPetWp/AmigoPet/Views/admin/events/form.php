<?php
/**
 * Template para formulário de evento no admin
 */
if (!defined('ABSPATH')) {
    exit;
}

$event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$is_edit = $event_id > 0;
$title = $is_edit ? __('Editar Evento', 'amigopet-wp') : __('Adicionar Novo Evento', 'amigopet-wp');
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php echo esc_html($title); ?></h1>
    <a href="<?php echo remove_query_arg(['action', 'id']); ?>" class="page-title-action">
        <?php _e('Voltar para Lista', 'amigopet-wp'); ?>
    </a>
    
    <div class="apwp-event-form">
        <form id="apwp-event-form" method="post">
            <?php wp_nonce_field('apwp_save_event', '_wpnonce'); ?>
            <input type="hidden" name="action" value="<?php echo $is_edit ? 'edit' : 'add'; ?>">
            <?php if ($is_edit): ?>
                <input type="hidden" name="id" value="<?php echo $event_id; ?>">
            <?php endif; ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="title"><?php _e('Título', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="text" id="title" name="title" class="regular-text" required
                            value="<?php echo $is_edit ? esc_attr($event->title) : ''; ?>">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="description"><?php _e('Descrição', 'amigopet-wp'); ?></label>
                    </th>
                    <td>
                        <?php
                        wp_editor(
                            $is_edit ? $event->description : '',
                            'description',
                            [
                                'textarea_name' => 'description',
                                'textarea_rows' => 10,
                                'media_buttons' => false,
                                'teeny' => true,
                                'quicktags' => false
                            ]
                        );
                        ?>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="date"><?php _e('Data', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="date" id="date" name="date" required
                            value="<?php echo $is_edit ? esc_attr($event->date) : ''; ?>">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="time"><?php _e('Horário', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="time" id="time" name="time" required
                            value="<?php echo $is_edit ? esc_attr($event->time) : ''; ?>">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="duration"><?php _e('Duração (minutos)', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="number" id="duration" name="duration" min="1" required
                            value="<?php echo $is_edit ? esc_attr($event->duration) : '60'; ?>">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="location"><?php _e('Local', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="text" id="location" name="location" class="regular-text" required
                            value="<?php echo $is_edit ? esc_attr($event->location) : ''; ?>">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="address"><?php _e('Endereço', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <textarea id="address" name="address" class="regular-text" rows="3" required><?php
                            echo $is_edit ? esc_textarea($event->address) : '';
                        ?></textarea>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="total_slots"><?php _e('Total de Vagas', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="number" id="total_slots" name="total_slots" min="1" required
                            value="<?php echo $is_edit ? esc_attr($event->total_slots) : '20'; ?>">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="status"><?php _e('Status', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <select id="status" name="status" required>
                            <option value="upcoming" <?php selected($is_edit && $event->status == 'upcoming'); ?>>
                                <?php _e('Próximo', 'amigopet-wp'); ?>
                            </option>
                            <option value="ongoing" <?php selected($is_edit && $event->status == 'ongoing'); ?>>
                                <?php _e('Em andamento', 'amigopet-wp'); ?>
                            </option>
                            <option value="past" <?php selected($is_edit && $event->status == 'past'); ?>>
                                <?php _e('Passado', 'amigopet-wp'); ?>
                            </option>
                            <option value="canceled" <?php selected($is_edit && $event->status == 'canceled'); ?>>
                                <?php _e('Cancelado', 'amigopet-wp'); ?>
                            </option>
                        </select>
                    </td>
                </tr>
            </table>
            
            <p class="submit">
                <button type="submit" class="button button-primary">
                    <?php echo $is_edit ? __('Atualizar', 'amigopet-wp') : __('Adicionar', 'amigopet-wp'); ?>
                </button>
            </p>
        </form>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Validação do formulário
    $('#apwp-event-form').on('submit', function(e) {
        var $form = $(this);
        var $submit = $form.find(':submit');
        
        $submit.prop('disabled', true);
        
        var data = $form.serialize();
        data += '&action=apwp_save_event';
        
        $.post(apwp.ajax_url, data, function(response) {
            if (response.success) {
                window.location.href = response.data.redirect;
            } else {
                alert(response.data.message);
                $submit.prop('disabled', false);
            }
        }).fail(function() {
            alert(apwp.i18n.error_saving);
            $submit.prop('disabled', false);
        });
        
        e.preventDefault();
    });
});
</script>
