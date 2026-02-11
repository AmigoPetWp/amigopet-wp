<?php declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Template para formulário de evento no admin
 */


$event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$is_edit = $event_id > 0;
$title = $is_edit ? esc_html__('Editar Evento', 'amigopet') : esc_html__('Adicionar Novo Evento', 'amigopet');
?>

<div class="wrap">
    <h1 class="wp-heading-inline"> echo esc_html($title); ?></h1>
    <a href=" echo esc_url(remove_query_arg(['action', 'id']); ?>" class="page-title-action">
         esc_html_e('Voltar para Lista', 'amigopet'); ?>
    </a>
    
    <div class="apwp-event-form">
        <form id="apwp-event-form" method="post">
             wp_nonce_field('apwp_save_event', '_wpnonce'); ?>
            <input type="hidden" name="action" value=" echo $is_edit ? 'edit' : 'add'; ?>">
             if ($is_edit): ?>
                <input type="hidden" name="id" value=" echo esc_attr($event_id); ?>">
             endif; ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="title"> esc_html_e('Título', 'amigopet'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="text" id="title" name="title" class="regular-text" required
                            value=" echo $is_edit ? esc_attr($event->title) : ''; ?>">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="description"> esc_html_e('Descrição', 'amigopet'); ?></label>
                    </th>
                    <td>
                        
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
                        <label for="date"> esc_html_e('Data', 'amigopet'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="date" id="date" name="date" required
                            value=" echo $is_edit ? esc_attr($event->date) : ''; ?>">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="time"> esc_html_e('Horário', 'amigopet'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="time" id="time" name="time" required
                            value=" echo $is_edit ? esc_attr($event->time) : ''; ?>">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="duration"> esc_html_e('Duração (minutos)', 'amigopet'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="number" id="duration" name="duration" min="1" required
                            value=" echo $is_edit ? esc_attr($event->duration) : '60'; ?>">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="location"> esc_html_e('Local', 'amigopet'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="text" id="location" name="location" class="regular-text" required
                            value=" echo $is_edit ? esc_attr($event->location) : ''; ?>">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="address"> esc_html_e('Endereço', 'amigopet'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <textarea id="address" name="address" class="regular-text" rows="3" required>
                            echo $is_edit ? esc_textarea($event->address) : '';
                        ?></textarea>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="total_slots"> esc_html_e('Total de Vagas', 'amigopet'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="number" id="total_slots" name="total_slots" min="1" required
                            value=" echo $is_edit ? esc_attr($event->total_slots) : '20'; ?>">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="status"> esc_html_e('Status', 'amigopet'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <select id="status" name="status" required>
                            <option value="upcoming"  selected($is_edit && $event->status == 'upcoming'); ?>>
                                 esc_html_e('Próximo', 'amigopet'); ?>
                            </option>
                            <option value="ongoing"  selected($is_edit && $event->status == 'ongoing'); ?>>
                                 esc_html_e('Em andamento', 'amigopet'); ?>
                            </option>
                            <option value="past"  selected($is_edit && $event->status == 'past'); ?>>
                                 esc_html_e('Passado', 'amigopet'); ?>
                            </option>
                            <option value="canceled"  selected($is_edit && $event->status == 'canceled'); ?>>
                                 esc_html_e('Cancelado', 'amigopet'); ?>
                            </option>
                        </select>
                    </td>
                </tr>
            </table>
            
            <p class="submit">
                <button type="submit" class="button button-primary">
                     echo $is_edit ? esc_html__('Atualizar', 'amigopet') : esc_html__('Adicionar', 'amigopet'); ?>
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