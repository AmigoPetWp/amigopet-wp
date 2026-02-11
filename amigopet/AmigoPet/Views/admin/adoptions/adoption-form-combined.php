<?php declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Template para formulário de adoção no admin
 */


global $wpdb;

$adoption_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$is_edit = $adoption_id > 0;

// Carrega a adoção se estiver editando
if ($is_edit) {
    $adoption = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}amigopet_adoptions WHERE id = %d",
        $adoption_id
    ));
    
    if (!$adoption) {
        wp_die(esc_html__('Adoção não encontrada', 'amigopet'));
    }
}

$title = $is_edit ? esc_html__('Editar Adoção', 'amigopet') : esc_html__('Adicionar Nova Adoção', 'amigopet');

// Carrega os pets disponíveis
$pets = $wpdb->get_results(
    "SELECT p.*, s.name as species_name, b.name as breed_name 
    FROM {$wpdb->prefix}amigopet_pets p
    LEFT JOIN {$wpdb->prefix}amigopet_pet_species s ON p.species_id = s.id
    LEFT JOIN {$wpdb->prefix}amigopet_pet_breeds b ON p.breed_id = b.id
    WHERE p.status = 'available'"
);

// Carrega os adotantes
$adopters = $wpdb->get_results(
    "SELECT * FROM {$wpdb->prefix}amigopet_adopters"
);

// Carrega os voluntários (para revisão)
$volunteers = $wpdb->get_results(
    "SELECT * FROM {$wpdb->prefix}amigopet_volunteers"
);
?>

<div class="wrap">
    <h1 class="wp-heading-inline"> echo esc_html($title); ?></h1>
    <a href=" echo esc_url(remove_query_arg(['action', 'id']); ?>" class="page-title-action">
         esc_html_e('Voltar para Lista', 'amigopet'); ?>
    </a>
    
    <div class="apwp-adoption-form">
        <form id="apwp-adoption-form" method="post" action=" echo esc_url(admin_url('admin-post.php'); ?>">
             wp_nonce_field('apwp_save_adoption', 'apwp_adoption_nonce'); ?>
            <input type="hidden" name="action" value="apwp_save_adoption">
             if ($is_edit): ?>
                <input type="hidden" name="adoption_id" value=" echo esc_attr($adoption_id); ?>">
             endif; ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="pet_id"> esc_html_e('Pet', 'amigopet'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <select name="pet_id" id="pet_id" class="regular-text" required>
                            <option value=""> esc_html_e('Selecione um pet', 'amigopet'); ?></option>
                             foreach ($pets as $pet) : ?>
                                <option value=" echo esc_attr($pet->id); ?>"  selected($is_edit ? $adoption->pet_id : '', $pet->id); ?>>
                                     echo esc_html($pet->name); ?> ( echo esc_html($pet->species_name); ?> -  echo esc_html($pet->breed_name); ?>)
                                </option>
                             endforeach; ?>
                        </select>
                        <p class="description">
                             esc_html_e('Apenas pets com status "Disponível" são mostrados aqui.', 'amigopet'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="adopter_id"> esc_html_e('Adotante', 'amigopet'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <select name="adopter_id" id="adopter_id" class="regular-text" required>
                            <option value=""> esc_html_e('Selecione um adotante', 'amigopet'); ?></option>
                             foreach ($adopters as $adopter) : ?>
                                <option value=" echo esc_attr($adopter->id); ?>"  selected($is_edit ? $adoption->adopter_id : '', $adopter->id); ?>>
                                     echo esc_html($adopter->name); ?> ( echo esc_html($adopter->email); ?>)
                                </option>
                             endforeach; ?>
                        </select>
                        <p class="description">
                            <a href=" echo esc_url(admin_url('admin.php?page=amigopet-adopters&action=add'); ?>" target="_blank">
                                 esc_html_e('Cadastrar novo adotante', 'amigopet'); ?>
                            </a>
                        </p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="adoption_reason"> esc_html_e('Motivo da Adoção', 'amigopet'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <textarea name="adoption_reason" id="adoption_reason" class="regular-text" rows="3" required> echo $is_edit ? esc_textarea($adoption->adoption_reason) : ''; ?></textarea>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="pet_experience"> esc_html_e('Experiência com Pets', 'amigopet'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <textarea name="pet_experience" id="pet_experience" class="regular-text" rows="3" required> echo $is_edit ? esc_textarea($adoption->pet_experience) : ''; ?></textarea>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="status"> esc_html_e('Status', 'amigopet'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <select id="adoption_status" name="adoption_status" required>
                            <option value=""> esc_html_e('Selecione o status', 'amigopet'); ?></option>
                            <option value="pending"  selected($adoption_data['adoption_status'] ?? '', 'pending'); ?>>
                                 esc_html_e('Pendente', 'amigopet'); ?>
                            </option>
                            <option value="approved"  selected($is_edit ? $adoption->status : '', 'approved'); ?>>
                                 esc_html_e('Aprovada', 'amigopet'); ?>
                            </option>
                            <option value="rejected"  selected($is_edit ? $adoption->status : '', 'rejected'); ?>>
                                 esc_html_e('Rejeitada', 'amigopet'); ?>
                            </option>
                            <option value="awaiting_payment"  selected($is_edit ? $adoption->status : '', 'awaiting_payment'); ?>>
                                 esc_html_e('Aguardando Pagamento', 'amigopet'); ?>
                            </option>
                            <option value="paid"  selected($is_edit ? $adoption->status : '', 'paid'); ?>>
                                 esc_html_e('Pago', 'amigopet'); ?>
                            </option>
                            <option value="completed"  selected($is_edit ? $adoption->status : '', 'completed'); ?>>
                                 esc_html_e('Concluída', 'amigopet'); ?>
                            </option>
                            <option value="cancelled"  selected($is_edit ? $adoption->status : '', 'cancelled'); ?>>
                                 esc_html_e('Cancelada', 'amigopet'); ?>
                            </option>
                        </select>
                    </td>
                </tr>

                 if ($is_edit) : ?>
                <tr>
                    <th scope="row">
                        <label for="reviewer_id"> esc_html_e('Revisor', 'amigopet'); ?></label>
                    </th>
                    <td>
                        <select name="reviewer_id" id="reviewer_id" class="regular-text">
                            <option value=""> esc_html_e('Selecione um revisor', 'amigopet'); ?></option>
                             foreach ($volunteers as $volunteer) : ?>
                                <option value=" echo esc_attr($volunteer->id); ?>"  selected($adoption->reviewer_id, $volunteer->id); ?>>
                                     echo esc_html($volunteer->name); ?> ( echo esc_html($volunteer->role); ?>)
                                </option>
                             endforeach; ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="review_notes"> esc_html_e('Notas da Revisão', 'amigopet'); ?></label>
                    </th>
                    <td>
                        <textarea name="review_notes" id="review_notes" class="regular-text" rows="3"> echo esc_textarea($adoption->review_notes); ?></textarea>
                    </td>
                </tr>
                 endif; ?>

                <tr>
                    <th scope="row">
                        <label for="adoption_notes"> esc_html_e('Observações', 'amigopet'); ?></label>
                    </th>
                    <td>
                        <textarea name="adoption_notes" id="adoption_notes" class="regular-text" rows="5"> echo $is_edit ? esc_textarea($adoption->notes) : ''; ?></textarea>
                    </td>
                </tr>
            </table>
            
            <p class="submit">
                <button type="submit" class="button button-primary">
                     echo $is_edit ? esc_html__('Atualizar Adoção', 'amigopet') : esc_html__('Adicionar Adoção', 'amigopet'); ?>
                </button>
            </p>
        </form>
    </div>
</div>

<style>
.apwp-adoption-form .required {
    color: #dc3232;
}

.apwp-adoption-form .error {
    border-color: #dc3232;
}

.apwp-adoption-form .description {
    font-style: italic;
    color: #646970;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Validação do formulário
    $('#apwp-adoption-form').on('submit', function(e) {
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
    
    // Máscara para telefone
    $('#adopter_phone').on('input', function() {
        var phone = $(this).val().replace(/\D/g, '');
        if (phone.length > 0) {
            phone = phone.match(new RegExp('.{1,11}'));
            phone = phone[0].replace(/(\d{2})(\d{4,5})(\d{4})/, '($1) $2-$3');
            $(this).val(phone);
        }
    });
});
</script>