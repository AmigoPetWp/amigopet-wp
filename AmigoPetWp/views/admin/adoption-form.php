<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php echo $adoption ? __('Editar Adoção', 'amigopet-wp') : __('Adicionar Nova Adoção', 'amigopet-wp'); ?></h1>
    
    <form method="post" action="" enctype="multipart/form-data">
        <?php wp_nonce_field('apwp_save_adoption', 'apwp_adoption_nonce'); ?>
        <input type="hidden" name="adoption_id" value="<?php echo $adoption ? $adoption->ID : ''; ?>">
        
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="pet_id"><?php _e('Pet', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <?php
                    $pets = get_posts([
                        'post_type' => 'apwp_pet',
                        'posts_per_page' => -1,
                        'orderby' => 'title',
                        'order' => 'ASC'
                    ]);
                    ?>
                    <select id="pet_id" name="pet_id" required>
                        <option value=""><?php _e('Selecione um pet...', 'amigopet-wp'); ?></option>
                        <?php foreach ($pets as $pet): ?>
                            <option value="<?php echo $pet->ID; ?>" <?php selected($adoption ? get_post_meta($adoption->ID, 'pet_id', true) : '', $pet->ID); ?>>
                                <?php echo get_post_meta($pet->ID, 'pet_name', true); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="adopter_name"><?php _e('Nome do Adotante', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <input type="text" id="adopter_name" name="adopter_name" class="regular-text" value="<?php echo $adoption ? esc_attr(get_post_meta($adoption->ID, 'adopter_name', true)) : ''; ?>" required>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="adopter_email"><?php _e('Email do Adotante', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <input type="email" id="adopter_email" name="adopter_email" class="regular-text" value="<?php echo $adoption ? esc_attr(get_post_meta($adoption->ID, 'adopter_email', true)) : ''; ?>" required>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="adopter_phone"><?php _e('Telefone do Adotante', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <input type="tel" id="adopter_phone" name="adopter_phone" class="regular-text" value="<?php echo $adoption ? esc_attr(get_post_meta($adoption->ID, 'adopter_phone', true)) : ''; ?>" required>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="adopter_address"><?php _e('Endereço do Adotante', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <textarea id="adopter_address" name="adopter_address" class="large-text" rows="3" required><?php echo $adoption ? esc_textarea(get_post_meta($adoption->ID, 'adopter_address', true)) : ''; ?></textarea>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="adoption_date"><?php _e('Data da Adoção', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <input type="date" id="adoption_date" name="adoption_date" value="<?php echo $adoption ? esc_attr(get_post_meta($adoption->ID, 'adoption_date', true)) : date('Y-m-d'); ?>" required>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="adoption_status"><?php _e('Status da Adoção', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <select id="adoption_status" name="adoption_status" required>
                        <option value="pending" <?php selected($adoption ? get_post_meta($adoption->ID, 'adoption_status', true) : '', 'pending'); ?>><?php _e('Pendente', 'amigopet-wp'); ?></option>
                        <option value="approved" <?php selected($adoption ? get_post_meta($adoption->ID, 'adoption_status', true) : '', 'approved'); ?>><?php _e('Aprovada', 'amigopet-wp'); ?></option>
                        <option value="rejected" <?php selected($adoption ? get_post_meta($adoption->ID, 'adoption_status', true) : '', 'rejected'); ?>><?php _e('Rejeitada', 'amigopet-wp'); ?></option>
                        <option value="completed" <?php selected($adoption ? get_post_meta($adoption->ID, 'adoption_status', true) : '', 'completed'); ?>><?php _e('Concluída', 'amigopet-wp'); ?></option>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="adoption_notes"><?php _e('Observações', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <textarea id="adoption_notes" name="adoption_notes" class="large-text" rows="5"><?php echo $adoption ? esc_textarea(get_post_meta($adoption->ID, 'adoption_notes', true)) : ''; ?></textarea>
                </td>
            </tr>
        </table>
        
        <?php submit_button($adoption ? __('Atualizar Adoção', 'amigopet-wp') : __('Adicionar Adoção', 'amigopet-wp')); ?>
    </form>
</div>
