<?php declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Template para formulário de evento no admin
 * Combina as melhores características de ambas implementações anteriores
 */


$event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$is_edit = $event_id > 0;
$event = $is_edit ? get_post($event_id) : null;

if ($is_edit && (!$event || $event->post_type !== 'apwp_event')) {
    wp_die(esc_html__('Evento não encontrado', 'amigopet'));
}

$title = $is_edit ? esc_html__('Editar Evento', 'amigopet') : esc_html__('Adicionar Novo Evento', 'amigopet');

// Carrega dados do evento se estiver editando
$event_data = $is_edit ? [
    'title' => get_post_meta($event_id, 'event_title', true),
    'type' => get_post_meta($event_id, 'event_type', true),
    'date' => get_post_meta($event_id, 'event_date', true),
    'time' => get_post_meta($event_id, 'event_time', true),
    'location' => get_post_meta($event_id, 'event_location', true),
    'organizer' => get_post_meta($event_id, 'event_organizer', true),
    'description' => get_post_meta($event_id, 'event_description', true),
    'contact' => get_post_meta($event_id, 'event_contact', true)
] : [];
?>

<div class="wrap">
    <h1 class="wp-heading-inline"> echo esc_html($title); ?></h1>
    <a href=" echo esc_url(remove_query_arg(['action', 'id']); ?>" class="page-title-action">
         esc_html_e('Voltar para Lista', 'amigopet'); ?>
    </a>
    
    <div class="apwp-event-form">
        <form id="apwp-event-form" method="post" action=" echo esc_url(admin_url('admin-post.php'); ?>" enctype="multipart/form-data">
             wp_nonce_field('apwp_save_event', 'apwp_event_nonce'); ?>
            <input type="hidden" name="action" value="apwp_save_event">
             if ($is_edit): ?>
                <input type="hidden" name="event_id" value=" echo esc_attr($event_id); ?>">
             endif; ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="event_title"> esc_html_e('Título do Evento', 'amigopet'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="text" id="event_title" name="event_title" class="regular-text" required
                               value=" echo esc_attr($event_data['title'] ?? ''); ?>">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="event_type"> esc_html_e('Tipo do Evento', 'amigopet'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <select id="event_type" name="event_type" required>
                            <option value=""> esc_html_e('Selecione o tipo', 'amigopet'); ?></option>
                            <option value="adoption_fair"  selected($event_data['type'] ?? '', 'adoption_fair'); ?>>
                                 esc_html_e('Feira de Adoção', 'amigopet'); ?>
                            </option>
                            <option value="fundraising"  selected($event_data['type'] ?? '', 'fundraising'); ?>>
                                 esc_html_e('Arrecadação de Fundos', 'amigopet'); ?>
                            </option>
                            <option value="vaccination"  selected($event_data['type'] ?? '', 'vaccination'); ?>>
                                 esc_html_e('Campanha de Vacinação', 'amigopet'); ?>
                            </option>
                            <option value="other"  selected($event_data['type'] ?? '', 'other'); ?>>
                                 esc_html_e('Outro', 'amigopet'); ?>
                            </option>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="event_date"> esc_html_e('Data do Evento', 'amigopet'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="date" id="event_date" name="event_date" required
                               min=" echo gmdate('Y-m-d'); ?>"
                               value=" echo esc_attr($event_data['date'] ?? gmdate('Y-m-d')); ?>">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="event_time"> esc_html_e('Horário do Evento', 'amigopet'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="time" id="event_time" name="event_time" required
                               value=" echo esc_attr($event_data['time'] ?? '09:00'); ?>">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="event_location"> esc_html_e('Local do Evento', 'amigopet'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="text" id="event_location" name="event_location" class="regular-text" required
                               value=" echo esc_attr($event_data['location'] ?? ''); ?>">
                        <p class="description">
                             esc_html_e('Endereço completo onde o evento será realizado.', 'amigopet'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="event_organizer"> esc_html_e('Organizador', 'amigopet'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="text" id="event_organizer" name="event_organizer" class="regular-text" required
                               value=" echo esc_attr($event_data['organizer'] ?? ''); ?>">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="event_contact"> esc_html_e('Contato', 'amigopet'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="text" id="event_contact" name="event_contact" class="regular-text" required
                               value=" echo esc_attr($event_data['contact'] ?? ''); ?>">
                        <p class="description">
                             esc_html_e('Telefone ou email para contato sobre o evento.', 'amigopet'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="event_description"> esc_html_e('Descrição', 'amigopet'); ?></label>
                    </th>
                    <td>
                        
                        wp_editor(
                            $event_data['description'] ?? '',
                            'event_description',
                            [
                                'textarea_name' => 'event_description',
                                'textarea_rows' => 10,
                                'media_buttons' => true,
                                'teeny' => true,
                                'quicktags' => true
                            ]
                        );
                        ?>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="event_image"> esc_html_e('Imagem do Evento', 'amigopet'); ?></label>
                    </th>
                    <td>
                        <div id="event-image-container">
                             if ($is_edit && has_post_thumbnail($event_id)): ?>
                                <div class="current-thumbnail">
                                     echo get_the_post_thumbnail($event_id, 'thumbnail'); ?>
                                </div>
                             endif; ?>
                            <input type="file" id="event_image" name="event_image" accept="image/*">
                            <p class="description">
                                 esc_html_e('Selecione uma imagem para o evento. Tamanho recomendado: 800x600 pixels.', 'amigopet'); ?>
                            </p>
                        </div>
                    </td>
                </tr>
            </table>
            
            <p class="submit">
                <button type="submit" class="button button-primary">
                     echo $is_edit ? esc_html__('Atualizar Evento', 'amigopet') : esc_html__('Adicionar Evento', 'amigopet'); ?>
                </button>
            </p>
        </form>
    </div>
</div>

<style>
.apwp-event-form .required {
    color: #dc3232;
}

.apwp-event-form .error {
    border-color: #dc3232;
}

.apwp-event-form .description {
    font-style: italic;
    color: #646970;
}

.apwp-event-form .current-thumbnail {
    margin-bottom: 10px;
}

.apwp-event-form .current-thumbnail img {
    max-width: 150px;
    height: auto;
    border: 1px solid #ddd;
    padding: 2px;
}

#event-image-preview {
    margin-top: 10px;
}

#event-image-preview img {
    max-width: 150px;
    height: auto;
    border: 1px solid #ddd;
    padding: 2px;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Validação do formulário
    $('#apwp-event-form').on('submit', function(e) {
        var $required = $(this).find('[required]');
        var valid = true;
        
        $required.each(function() {
            if (!$(this).val()) {
                valid = false;
                $(this).addClass('error');
            } else {
                $(this).removeClass('error');
            }
        });
        
        if (!valid) {
            e.preventDefault();
            alert(' esc_html_e('Por favor, preencha todos os campos obrigatórios.', 'amigopet'); ?>');
        }
    });
    
    // Preview da imagem
    $('#event_image').on('change', function() {
        var $container = $('#event-image-container');
        var $preview = $container.find('#event-image-preview');
        
        if (!$preview.length) {
            $preview = $('<div id="event-image-preview"></div>').appendTo($container);
        } else {
            $preview.empty();
        }
        
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function(e) {
                $preview.html('<img src="' + e.target.result + '">');
            }
            
            reader.readAsDataURL(this.files[0]);
        }
    });
    
    // Validação da data (não permitir datas passadas)
    var today = new Date().toISOString().split('T')[0];
    $('#event_date').attr('min', today);
});
</script>