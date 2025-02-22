<?php
/**
 * @var \AmigoPetWp\Domain\Entities\Organization|null $organization
 */
?>

<div class="wrap">
    <h1><?php echo $organization ? __('Editar Organização', 'amigopet-wp') : __('Nova Organização', 'amigopet-wp'); ?></h1>

    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" class="apwp-form">
        <?php wp_nonce_field('apwp_save_organization'); ?>
        <input type="hidden" name="action" value="apwp_save_organization">
        <?php if ($organization): ?>
            <input type="hidden" name="organization_id" value="<?php echo esc_attr($organization->getId()); ?>">
        <?php endif; ?>

        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="name"><?php _e('Nome', 'amigopet-wp'); ?> <span class="required">*</span></label>
                </th>
                <td>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           class="regular-text" 
                           value="<?php echo $organization ? esc_attr($organization->getName()) : ''; ?>" 
                           required>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="email"><?php _e('E-mail', 'amigopet-wp'); ?> <span class="required">*</span></label>
                </th>
                <td>
                    <input type="email" 
                           name="email" 
                           id="email" 
                           class="regular-text" 
                           value="<?php echo $organization ? esc_attr($organization->getEmail()) : ''; ?>" 
                           required>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="phone"><?php _e('Telefone', 'amigopet-wp'); ?> <span class="required">*</span></label>
                </th>
                <td>
                    <input type="tel" 
                           name="phone" 
                           id="phone" 
                           class="regular-text" 
                           value="<?php echo $organization ? esc_attr($organization->getPhone()) : ''; ?>" 
                           required>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="address"><?php _e('Endereço', 'amigopet-wp'); ?> <span class="required">*</span></label>
                </th>
                <td>
                    <input type="text" 
                           name="address" 
                           id="address" 
                           class="regular-text" 
                           value="<?php echo $organization ? esc_attr($organization->getAddress()) : ''; ?>" 
                           required>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="website"><?php _e('Website', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <input type="url" 
                           name="website" 
                           id="website" 
                           class="regular-text" 
                           value="<?php echo $organization ? esc_attr($organization->getWebsite()) : ''; ?>">
                </td>
            </tr>

            <?php if ($organization): ?>
            <tr>
                <th scope="row">
                    <label><?php _e('Status', 'amigopet-wp'); ?></label>
                </th>
                <td>
                    <p>
                        <span class="apwp-status-badge apwp-status-<?php echo esc_attr($organization->getStatus()); ?>">
                            <?php echo esc_html(ucfirst($organization->getStatus())); ?>
                        </span>
                    </p>
                </td>
            </tr>
            <?php endif; ?>
        </table>

        <p class="submit">
            <button type="submit" class="button button-primary">
                <?php echo $organization ? __('Atualizar', 'amigopet-wp') : __('Cadastrar', 'amigopet-wp'); ?>
            </button>
            <a href="<?php echo admin_url('admin.php?page=amigopet-wp-organizations'); ?>" class="button">
                <?php _e('Cancelar', 'amigopet-wp'); ?>
            </a>
        </p>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    $('#phone').mask('(00) 00000-0000');
});
</script>
