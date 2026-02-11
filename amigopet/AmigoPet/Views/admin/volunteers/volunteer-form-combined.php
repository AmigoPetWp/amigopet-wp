<?php declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Template para formulário de voluntário no admin
 * Combina as melhores características de ambas implementações anteriores
 */


$volunteer_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$is_edit = $volunteer_id > 0;
$volunteer = $is_edit ? get_post($volunteer_id) : null;

if ($is_edit && (!$volunteer || $volunteer->post_type !== 'apwp_volunteer')) {
    wp_die(esc_html__('Voluntário não encontrado', 'amigopet'));
}

$title = $is_edit ? esc_html__('Editar Voluntário', 'amigopet') : esc_html__('Adicionar Novo Voluntário', 'amigopet');

// Carrega dados do voluntário se estiver editando
$volunteer_data = $is_edit ? [
    'name' => get_post_meta($volunteer_id, 'volunteer_name', true),
    'email' => get_post_meta($volunteer_id, 'volunteer_email', true),
    'phone' => get_post_meta($volunteer_id, 'volunteer_phone', true),
    'address' => get_post_meta($volunteer_id, 'volunteer_address', true),
    'availability_days' => get_post_meta($volunteer_id, 'volunteer_availability_days', true),
    'availability_periods' => get_post_meta($volunteer_id, 'volunteer_availability_periods', true),
    'skills' => get_post_meta($volunteer_id, 'volunteer_skills', true),
    'experience' => get_post_meta($volunteer_id, 'volunteer_experience', true),
    'status' => get_post_meta($volunteer_id, 'volunteer_status', true),
    'notes' => get_post_meta($volunteer_id, 'volunteer_notes', true)
] : [];
?>

<div class="wrap">
    <h1 class="wp-heading-inline"> echo esc_html($title); ?></h1>
    <a href=" echo esc_url(remove_query_arg(['action', 'id']); ?>" class="page-title-action">
         esc_html_e('Voltar para Lista', 'amigopet'); ?>
    </a>
    
    <div class="apwp-volunteer-form">
        <form id="apwp-volunteer-form" method="post" action=" echo esc_url(admin_url('admin-post.php'); ?>" enctype="multipart/form-data">
             wp_nonce_field('apwp_save_volunteer', 'apwp_volunteer_nonce'); ?>
            <input type="hidden" name="action" value="apwp_save_volunteer">
             if ($is_edit): ?>
                <input type="hidden" name="volunteer_id" value=" echo esc_attr($volunteer_id); ?>">
             endif; ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="volunteer_name"> esc_html_e('Nome Completo', 'amigopet'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="text" id="volunteer_name" name="volunteer_name" class="regular-text" required
                               value=" echo esc_attr($volunteer_data['name'] ?? ''); ?>">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="volunteer_email"> esc_html_e('Email', 'amigopet'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="email" id="volunteer_email" name="volunteer_email" class="regular-text" required
                               value=" echo esc_attr($volunteer_data['email'] ?? ''); ?>">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="volunteer_phone"> esc_html_e('Telefone', 'amigopet'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="tel" id="volunteer_phone" name="volunteer_phone" class="regular-text" required
                               value=" echo esc_attr($volunteer_data['phone'] ?? ''); ?>">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="volunteer_address"> esc_html_e('Endereço', 'amigopet'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <textarea id="volunteer_address" name="volunteer_address" class="large-text" rows="3" required> 
                            echo esc_textarea($volunteer_data['address'] ?? ''); 
                        ?></textarea>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="volunteer_availability_days"> esc_html_e('Dias Disponíveis', 'amigopet'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <select id="volunteer_availability_days" name="volunteer_availability_days[]" multiple required>
                            
                            $days = [
                                'monday'    => esc_html__('Segunda-feira', 'amigopet'),
                                'tuesday'   => esc_html__('Terça-feira', 'amigopet'),
                                'wednesday' => esc_html__('Quarta-feira', 'amigopet'),
                                'thursday'  => esc_html__('Quinta-feira', 'amigopet'),
                                'friday'    => esc_html__('Sexta-feira', 'amigopet'),
                                'saturday'  => esc_html__('Sábado', 'amigopet'),
                                'sunday'    => esc_html__('Domingo', 'amigopet')
                            ];
                            
                            $selected_days = is_array($volunteer_data['availability_days'] ?? '') 
                                ? $volunteer_data['availability_days'] 
                                : [];
                            
                            foreach ($days as $value => $label) {
                                printf(
                                    '<option value="%s" %s>%s</option>',
                                    esc_attr($value),
                                    in_array($value, $selected_days) ? 'selected' : '',
                                    esc_html($label)
                                );
                            }
                            ?>
                        </select>
                        <p class="description">
                             esc_html_e('Pressione Ctrl (ou Cmd no Mac) para selecionar múltiplos dias.', 'amigopet'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="volunteer_availability_periods"> esc_html_e('Períodos Disponíveis', 'amigopet'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <select id="volunteer_availability_periods" name="volunteer_availability_periods[]" multiple required>
                            
                            $periods = [
                                'morning'   => esc_html__('Manhã', 'amigopet'),
                                'afternoon' => esc_html__('Tarde', 'amigopet'),
                                'evening'   => esc_html__('Noite', 'amigopet')
                            ];
                            
                            $selected_periods = is_array($volunteer_data['availability_periods'] ?? '') 
                                ? $volunteer_data['availability_periods'] 
                                : [];
                            
                            foreach ($periods as $value => $label) {
                                printf(
                                    '<option value="%s" %s>%s</option>',
                                    esc_attr($value),
                                    in_array($value, $selected_periods) ? 'selected' : '',
                                    esc_html($label)
                                );
                            }
                            ?>
                        </select>
                        <p class="description">
                             esc_html_e('Pressione Ctrl (ou Cmd no Mac) para selecionar múltiplos períodos.', 'amigopet'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="volunteer_skills"> esc_html_e('Habilidades', 'amigopet'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <select id="volunteer_skills" name="volunteer_skills[]" multiple required>
                            
                            $skills = [
                                'veterinary'  => esc_html__('Veterinária', 'amigopet'),
                                'grooming'    => esc_html__('Banho e Tosa', 'amigopet'),
                                'transport'   => esc_html__('Transporte', 'amigopet'),
                                'social'      => esc_html__('Mídias Sociais', 'amigopet'),
                                'events'      => esc_html__('Eventos', 'amigopet'),
                                'fundraising' => esc_html__('Captação de Recursos', 'amigopet'),
                                'other'       => esc_html__('Outro', 'amigopet')
                            ];
                            
                            $selected_skills = is_array($volunteer_data['skills'] ?? '') 
                                ? $volunteer_data['skills'] 
                                : [];
                            
                            foreach ($skills as $value => $label) {
                                printf(
                                    '<option value="%s" %s>%s</option>',
                                    esc_attr($value),
                                    in_array($value, $selected_skills) ? 'selected' : '',
                                    esc_html($label)
                                );
                            }
                            ?>
                        </select>
                        <p class="description">
                             esc_html_e('Pressione Ctrl (ou Cmd no Mac) para selecionar múltiplas habilidades.', 'amigopet'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="volunteer_experience"> esc_html_e('Experiência', 'amigopet'); ?></label>
                    </th>
                    <td>
                        
                        wp_editor(
                            $volunteer_data['experience'] ?? '',
                            'volunteer_experience',
                            [
                                'textarea_name' => 'volunteer_experience',
                                'textarea_rows' => 5,
                                'media_buttons' => false,
                                'teeny' => true,
                                'quicktags' => true
                            ]
                        );
                        ?>
                        <p class="description">
                             esc_html_e('Descreva sua experiência prévia com animais ou em outras atividades voluntárias.', 'amigopet'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="volunteer_photo"> esc_html_e('Foto', 'amigopet'); ?></label>
                    </th>
                    <td>
                        <div id="volunteer-photo-container">
                             if ($is_edit && has_post_thumbnail($volunteer_id)): ?>
                                <div class="current-thumbnail">
                                     echo get_the_post_thumbnail($volunteer_id, 'thumbnail'); ?>
                                </div>
                             endif; ?>
                            <input type="file" id="volunteer_photo" name="volunteer_photo" accept="image/*">
                            <p class="description">
                                 esc_html_e('Selecione uma foto sua. Tamanho recomendado: 300x300 pixels.', 'amigopet'); ?>
                            </p>
                        </div>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="volunteer_status"> esc_html_e('Status', 'amigopet'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <select id="volunteer_status" name="volunteer_status" required>
                            <option value=""> esc_html_e('Selecione o status', 'amigopet'); ?></option>
                            <option value="active"  selected($volunteer_data['status'] ?? '', 'active'); ?>>
                                 esc_html_e('Ativo', 'amigopet'); ?>
                            </option>
                            <option value="inactive"  selected($volunteer_data['status'] ?? '', 'inactive'); ?>>
                                 esc_html_e('Inativo', 'amigopet'); ?>
                            </option>
                            <option value="pending"  selected($volunteer_data['status'] ?? '', 'pending'); ?>>
                                 esc_html_e('Pendente', 'amigopet'); ?>
                            </option>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="volunteer_notes"> esc_html_e('Observações', 'amigopet'); ?></label>
                    </th>
                    <td>
                        
                        wp_editor(
                            $volunteer_data['notes'] ?? '',
                            'volunteer_notes',
                            [
                                'textarea_name' => 'volunteer_notes',
                                'textarea_rows' => 5,
                                'media_buttons' => false,
                                'teeny' => true,
                                'quicktags' => true
                            ]
                        );
                        ?>
                    </td>
                </tr>
            </table>
            
            <p class="submit">
                <button type="submit" class="button button-primary">
                     echo $is_edit ? esc_html__('Atualizar Voluntário', 'amigopet') : esc_html__('Adicionar Voluntário', 'amigopet'); ?>
                </button>
            </p>
        </form>
    </div>
</div>

<style>
.apwp-volunteer-form .required {
    color: #dc3232;
}

.apwp-volunteer-form .error {
    border-color: #dc3232;
}

.apwp-volunteer-form .description {
    font-style: italic;
    color: #646970;
}

.apwp-volunteer-form .current-thumbnail {
    margin-bottom: 10px;
}

.apwp-volunteer-form .current-thumbnail img {
    max-width: 150px;
    height: auto;
    border: 1px solid #ddd;
    padding: 2px;
    border-radius: 50%;
}

#volunteer-photo-preview {
    margin-top: 10px;
}

#volunteer-photo-preview img {
    max-width: 150px;
    height: auto;
    border: 1px solid #ddd;
    padding: 2px;
    border-radius: 50%;
}

select[multiple] {
    min-width: 200px;
    min-height: 100px;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Validação do formulário
    $('#apwp-volunteer-form').on('submit', function(e) {
        var $required = $(this).find('[required]');
        var valid = true;
        
        $required.each(function() {
            if (!$(this).val() || ($(this).is('select[multiple]') && !$(this).val()?.length)) {
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
    
    // Máscara para telefone
    $('#volunteer_phone').on('input', function() {
        var phone = $(this).val().replace(/\D/g, '');
        if (phone.length > 0) {
            phone = phone.match(new RegExp('.{1,11}'));
            phone = phone[0].replace(/(\d{2})(\d{4,5})(\d{4})/, '($1) $2-$3');
            $(this).val(phone);
        }
    });
    
    // Preview da foto
    $('#volunteer_photo').on('change', function() {
        var $container = $('#volunteer-photo-container');
        var $preview = $container.find('#volunteer-photo-preview');
        
        if (!$preview.length) {
            $preview = $('<div id="volunteer-photo-preview"></div>').appendTo($container);
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
});
</script>