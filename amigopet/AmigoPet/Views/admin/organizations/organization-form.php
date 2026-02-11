<?php declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

/**
 * @var \AmigoPetWp\Domain\Entities\Organization|null $organization
 */
?>

<div class="wrap">
    <h1> echo $organization ? esc_html__('Editar Organização', 'amigopet') : esc_html__('Nova Organização', 'amigopet'); ?></h1>

    <form method="post" action=" echo esc_url(admin_url('admin-post.php'); ?>" class="apwp-form">
         wp_nonce_field('apwp_save_organization'); ?>
        <input type="hidden" name="action" value="apwp_save_organization">
         if ($organization): ?>
            <input type="hidden" name="organization_id" value=" echo esc_attr($organization->getId()); ?>">
         endif; ?>

        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="name"> esc_html_e('Nome', 'amigopet'); ?> <span class="required">*</span></label>
                </th>
                <td>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           class="regular-text" 
                           value=" echo $organization ? esc_attr($organization->getName()) : ''; ?>" 
                           required>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="email"> esc_html_e('E-mail', 'amigopet'); ?> <span class="required">*</span></label>
                </th>
                <td>
                    <input type="email" 
                           name="email" 
                           id="email" 
                           class="regular-text" 
                           value=" echo $organization ? esc_attr($organization->getEmail()) : ''; ?>" 
                           required>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="phone"> esc_html_e('Telefone', 'amigopet'); ?> <span class="required">*</span></label>
                </th>
                <td>
                    <input type="tel" 
                           name="phone" 
                           id="phone" 
                           class="regular-text" 
                           value=" echo $organization ? esc_attr($organization->getPhone()) : ''; ?>" 
                           required>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="address"> esc_html_e('Endereço', 'amigopet'); ?> <span class="required">*</span></label>
                </th>
                <td>
                    <input type="text" 
                           name="address" 
                           id="address" 
                           class="regular-text" 
                           value=" echo $organization ? esc_attr($organization->getAddress()) : ''; ?>" 
                           required>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="website"> esc_html_e('Website', 'amigopet'); ?></label>
                </th>
                <td>
                    <input type="url" 
                           name="website" 
                           id="website" 
                           class="regular-text" 
                           value=" echo $organization ? esc_attr($organization->getWebsite()) : ''; ?>">
                </td>
            </tr>

             if ($organization): ?>
            <tr>
                <th scope="row">
                    <label> esc_html_e('Status', 'amigopet'); ?></label>
                </th>
                <td>
                    <p>
                        <span class="apwp-status-badge apwp-status- echo esc_attr($organization->getStatus()); ?>">
                             echo esc_html(ucfirst($organization->getStatus())); ?>
                        </span>
                    </p>
                </td>
            </tr>
             endif; ?>
        </table>

        <p class="submit">
            <button type="submit" class="button button-primary">
                 echo $organization ? esc_html__('Atualizar', 'amigopet') : esc_html__('Cadastrar', 'amigopet'); ?>
            </button>
            <a href=" echo esc_url(admin_url('admin.php?page=amigopet-organizations'); ?>" class="button">
                 esc_html_e('Cancelar', 'amigopet'); ?>
            </a>
        </p>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    $('#phone').mask('(00) 00000-0000');
});
</script>