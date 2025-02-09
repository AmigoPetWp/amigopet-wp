<?php
/**
 * Partial template para listagem de adoções
 */
if (!defined('ABSPATH')) {
    exit;
}
?>

<?php if (empty($adoptions)): ?>
    <tr>
        <td colspan="7" class="apwp-no-items">
            <?php _e('Nenhuma adoção encontrada.', 'amigopet-wp'); ?>
        </td>
    </tr>
<?php else: ?>
    <?php foreach ($adoptions as $adoption): ?>
        <tr>
            <td data-label="<?php esc_attr_e('ID', 'amigopet-wp'); ?>">
                <?php echo esc_html($adoption->getId()); ?>
            </td>
            
            <td data-label="<?php esc_attr_e('Pet', 'amigopet-wp'); ?>">
                <?php if ($adoption->getPet()): ?>
                    <a href="<?php echo get_edit_post_link($adoption->getPet()->getId()); ?>" target="_blank">
                        <?php echo esc_html($adoption->getPet()->getName()); ?>
                    </a>
                <?php else: ?>
                    <span class="apwp-deleted-pet">
                        <?php _e('Pet removido', 'amigopet-wp'); ?>
                    </span>
                <?php endif; ?>
            </td>
            
            <td data-label="<?php esc_attr_e('Nome', 'amigopet-wp'); ?>">
                <?php echo esc_html($adoption->getAdopter()->getName()); ?>
            </td>
            
            <td data-label="<?php esc_attr_e('E-mail', 'amigopet-wp'); ?>">
                <a href="mailto:<?php echo esc_attr($adoption->getAdopter()->getEmail()); ?>">
                    <?php echo esc_html($adoption->getAdopter()->getEmail()); ?>
                </a>
            </td>
            
            <td data-label="<?php esc_attr_e('Data', 'amigopet-wp'); ?>">
                <?php echo date_i18n(
                    get_option('date_format') . ' ' . get_option('time_format'),
                    strtotime($adoption->getCreatedAt())
                ); ?>
            </td>
            
            <td data-label="<?php esc_attr_e('Status', 'amigopet-wp'); ?>">
                <span class="apwp-status-badge status-<?php echo esc_attr($adoption->getStatus()); ?>">
                    <?php echo esc_html($adoption->getStatusLabel()); ?>
                </span>
            </td>
            
            <td data-label="<?php esc_attr_e('Ações', 'amigopet-wp'); ?>">
                <?php if ($adoption->isPending()): ?>
                    <button 
                        class="button action-button approve" 
                        data-action="approve" 
                        data-id="<?php echo esc_attr($adoption->getId()); ?>"
                        data-original-text="<?php esc_attr_e('Aprovar', 'amigopet-wp'); ?>"
                    >
                        <span class="dashicons dashicons-yes"></span>
                        <?php esc_html_e('Aprovar', 'amigopet-wp'); ?>
                    </button>
                    
                    <button 
                        class="button action-button reject" 
                        data-action="reject" 
                        data-id="<?php echo esc_attr($adoption->getId()); ?>"
                        data-original-text="<?php esc_attr_e('Rejeitar', 'amigopet-wp'); ?>"
                    >
                        <span class="dashicons dashicons-no"></span>
                        <?php esc_html_e('Rejeitar', 'amigopet-wp'); ?>
                    </button>
                <?php else: ?>
                    <button 
                        class="button action-button view-details" 
                        data-action="view" 
                        data-id="<?php echo esc_attr($adoption->getId()); ?>"
                    >
                        <span class="dashicons dashicons-visibility"></span>
                        <?php esc_html_e('Ver Detalhes', 'amigopet-wp'); ?>
                    </button>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>
