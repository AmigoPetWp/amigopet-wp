<?php
/**
 * Template para formulário de adoção no admin
 */
if (!defined('ABSPATH')) {
    exit;
}

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
        wp_die(__('Adoção não encontrada', 'amigopet-wp'));
    }
}

$title = $is_edit ? __('Editar Adoção', 'amigopet-wp') : __('Adicionar Nova Adoção', 'amigopet-wp');

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
    <h1 class="wp-heading-inline"><?php echo esc_html($title); ?></h1>
    <a href="<?php echo remove_query_arg(['action', 'id']); ?>" class="page-title-action">
        <?php _e('Voltar para Lista', 'amigopet-wp'); ?>
    </a>
    
    <div class="apwp-adoption-form">
        <form id="apwp-adoption-form" method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <?php wp_nonce_field('apwp_save_adoption', 'apwp_adoption_nonce'); ?>
            <input type="hidden" name="action" value="apwp_save_adoption">
            <?php if ($is_edit): ?>
                <input type="hidden" name="adoption_id" value="<?php echo $adoption_id; ?>">
            <?php endif; ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="pet_id"><?php _e('Pet', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <select name="pet_id" id="pet_id" class="regular-text" required>
                            <option value=""><?php _e('Selecione um pet', 'amigopet-wp'); ?></option>
                            <?php foreach ($pets as $pet) : ?>
                                <option value="<?php echo $pet->id; ?>" <?php selected($is_edit ? $adoption->pet_id : '', $pet->id); ?>>
                                    <?php echo esc_html($pet->name); ?> (<?php echo esc_html($pet->species_name); ?> - <?php echo esc_html($pet->breed_name); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description">
                            <?php _e('Apenas pets com status "Disponível" são mostrados aqui.', 'amigopet-wp'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="adopter_id"><?php _e('Adotante', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <select name="adopter_id" id="adopter_id" class="regular-text" required>
                            <option value=""><?php _e('Selecione um adotante', 'amigopet-wp'); ?></option>
                            <?php foreach ($adopters as $adopter) : ?>
                                <option value="<?php echo $adopter->id; ?>" <?php selected($is_edit ? $adoption->adopter_id : '', $adopter->id); ?>>
                                    <?php echo esc_html($adopter->name); ?> (<?php echo esc_html($adopter->email); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description">
                            <a href="<?php echo admin_url('admin.php?page=amigopet-wp-adopters&action=add'); ?>" target="_blank">
                                <?php _e('Cadastrar novo adotante', 'amigopet-wp'); ?>
                            </a>
                        </p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="adoption_reason"><?php _e('Motivo da Adoção', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <textarea name="adoption_reason" id="adoption_reason" class="regular-text" rows="3" required><?php echo $is_edit ? esc_textarea($adoption->adoption_reason) : ''; ?></textarea>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="pet_experience"><?php _e('Experiência com Pets', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <textarea name="pet_experience" id="pet_experience" class="regular-text" rows="3" required><?php echo $is_edit ? esc_textarea($adoption->pet_experience) : ''; ?></textarea>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="status"><?php _e('Status', 'amigopet-wp'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <select id="adoption_status" name="adoption_status" required>
                            <option value=""><?php _e('Selecione o status', 'amigopet-wp'); ?></option>
                            <option value="pending" <?php selected($adoption_data['adoption_status'] ?? '', 'pending'); ?>>
                                <?php _e('Pendente', 'amigopet-wp'); ?>
                            </option>
                            <option value="approved" <?php selected($is_edit ? $adoption->status : '', 'approved'); ?>>
                                <?php _e('Aprovada', 'amigopet-wp'); ?>
                            </option>
                            <option value="rejected" <?php selected($is_edit ? $adoption->status : '', 'rejected'); ?>>
                                <?php _e('Rejeitada', 'amigopet-wp'); ?>
                            </option>
                            <option value="awaiting_payment" <?php selected($is_edit ? $adoption->status : '', 'awaiting_payment'); ?>>
                                <?php _e('Aguardando Pagamento', 'amigopet-wp'); ?>
                            </option>
                            <option value="paid" <?php selected($is_edit ? $adoption->status : '', 'paid'); ?>>
                                <?php _e('Pago', 'amigopet-wp'); ?>
                            </option>
                            <option value="completed" <?php selected($is_edit ? $adoption->status : '', 'completed'); ?>>
                                <?php _e('Concluída', 'amigopet-wp'); ?>
                            </option>
                            <option value="cancelled" <?php selected($is_edit ? $adoption->status : '', 'cancelled'); ?>>
                                <?php _e('Cancelada', 'amigopet-wp'); ?>
                            </option>
                        </select>
                    </td>
                </tr>

                <?php if ($is_edit) : ?>
                <tr>
                    <th scope="row">
                        <label for="reviewer_id"><?php _e('Revisor', 'amigopet-wp'); ?></label>
                    </th>
                    <td>
                        <select name="reviewer_id" id="reviewer_id" class="regular-text">
                            <option value=""><?php _e('Selecione um revisor', 'amigopet-wp'); ?></option>
                            <?php foreach ($volunteers as $volunteer) : ?>
                                <option value="<?php echo $volunteer->id; ?>" <?php selected($adoption->reviewer_id, $volunteer->id); ?>>
                                    <?php echo esc_html($volunteer->name); ?> (<?php echo esc_html($volunteer->role); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="review_notes"><?php _e('Notas da Revisão', 'amigopet-wp'); ?></label>
                    </th>
                    <td>
                        <textarea name="review_notes" id="review_notes" class="regular-text" rows="3"><?php echo esc_textarea($adoption->review_notes); ?></textarea>
                    </td>
                </tr>
                <?php endif; ?>

                <tr>
                    <th scope="row">
                        <label for="adoption_notes"><?php _e('Observações', 'amigopet-wp'); ?></label>
                    </th>
                    <td>
                        <textarea name="adoption_notes" id="adoption_notes" class="regular-text" rows="5"><?php echo $is_edit ? esc_textarea($adoption->notes) : ''; ?></textarea>
                    </td>
                </tr>
            </table>
            
            <p class="submit">
                <button type="submit" class="button button-primary">
                    <?php echo $is_edit ? __('Atualizar Adoção', 'amigopet-wp') : __('Adicionar Adoção', 'amigopet-wp'); ?>
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
            alert('<?php _e('Por favor, preencha todos os campos obrigatórios.', 'amigopet-wp'); ?>');
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
