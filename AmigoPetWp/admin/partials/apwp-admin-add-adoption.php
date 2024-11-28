<?php
/**
 * Template para a página de adicionar adoção
 *
 * @link       https://github.com/wendelmax/amigopet-wp
 * @since      1.0.0
 *
 * @package    AmigoPet_Wp
 * @subpackage AmigoPet_Wp/admin/partials
 */

// Se este arquivo for chamado diretamente, aborte.
if (!defined('WPINC')) {
    die;
}

// Verifica permissões
if (!current_user_can('manage_options')) {
    wp_die(__('Você não tem permissão para acessar esta página.', 'amigopet-wp'));
}

// Inicializa as classes necessárias
$pet = new APWP_Pet();
$adopter = new APWP_Adopter();

// Lista de pets disponíveis
$available_pets = $pet->list(array('status' => 'available'));

// Lista de adotantes
$adopters = $adopter->list();
?>

<div class="wrap">
    <h1><?php _e('Adicionar Nova Adoção', 'amigopet-wp'); ?></h1>

    <form method="post" action="<?php echo admin_url('admin.php?page=amigopet-wp-adoptions'); ?>">
        <?php wp_nonce_field('apwp_add_adoption', 'apwp_nonce'); ?>
        <input type="hidden" name="action" value="add">

        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="pet_id"><?php _e('Pet', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <select name="pet_id" id="pet_id" class="regular-text" required>
                        <option value=""><?php _e('Selecione um pet', 'amigopet-wp'); ?></option>
                        <?php foreach ($available_pets as $available_pet) : ?>
                            <option value="<?php echo esc_attr($available_pet->id); ?>">
                                <?php echo esc_html($available_pet->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="adopter_id"><?php _e('Adotante', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <select name="adopter_id" id="adopter_id" class="regular-text" required>
                        <option value=""><?php _e('Selecione um adotante', 'amigopet-wp'); ?></option>
                        <?php foreach ($adopters as $adopter_item) : ?>
                            <option value="<?php echo esc_attr($adopter_item->id); ?>">
                                <?php echo esc_html($adopter_item->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="status"><?php _e('Status', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <select name="status" id="status" class="regular-text" required>
                        <option value="pending"><?php _e('Pendente', 'amigopet-wp'); ?></option>
                        <option value="approved"><?php _e('Aprovada', 'amigopet-wp'); ?></option>
                        <option value="rejected"><?php _e('Rejeitada', 'amigopet-wp'); ?></option>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="notes"><?php _e('Observações', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <textarea name="notes" id="notes" class="large-text" rows="5"></textarea>
                </td>
            </tr>
        </table>

        <?php submit_button(__('Adicionar Adoção', 'amigopet-wp')); ?>
    </form>
</div>
