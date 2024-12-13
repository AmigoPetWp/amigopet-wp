<?php
/**
 * Template para uma linha da tabela de adoções
 * 
 * @var object $item O objeto da adoção
 * @package AmigoPet_Wp
 */

// Se este arquivo for chamado diretamente, aborte.
if (!defined('WPINC')) {
    die;
}
?>
<tr class="adoption-row <?php echo esc_attr($item->status); ?>">
    <td class="column-id">
        #<?php echo esc_html($item->id); ?>
    </td>
    
    <td class="column-pet">
        <div class="pet-info">
            <?php if ($item->pet->photo_url) : ?>
                <img src="<?php echo esc_url($item->pet->photo_url); ?>" alt="<?php echo esc_attr($item->pet->name); ?>" class="pet-mini-photo">
            <?php endif; ?>
            <span><?php echo esc_html($item->pet->name); ?></span>
        </div>
    </td>
    
    <td class="column-adopter">
        <div class="adopter-info">
            <strong><?php echo esc_html($item->adopter->name); ?></strong>
            <br>
            <small><?php echo esc_html($item->adopter->email); ?></small>
        </div>
    </td>
    
    <td class="column-date">
        <div class="date-info">
            <strong><?php echo esc_html(APWP_Utils::format_date($item->created_at)); ?></strong>
            <br>
            <small><?php echo esc_html(APWP_Utils::format_time($item->created_at)); ?></small>
        </div>
    </td>
    
    <td class="column-status">
        <?php
        $status_class = '';
        $status_text = '';
        
        switch ($item->status) {
            case 'pending':
                $status_class = 'apwp-status-pending';
                $status_text = __('Pendente', 'amigopet-wp');
                break;
            case 'approved':
                $status_class = 'apwp-status-approved';
                $status_text = __('Aprovada', 'amigopet-wp');
                break;
            case 'rejected':
                $status_class = 'apwp-status-rejected';
                $status_text = __('Rejeitada', 'amigopet-wp');
                break;
        }
        ?>
        <span class="apwp-status-tag <?php echo esc_attr($status_class); ?>">
            <?php echo esc_html($status_text); ?>
        </span>
    </td>
    
    <td class="column-actions">
        <div class="apwp-admin-actions">
            <?php if ($item->status === 'pending') : ?>
                <a href="#" 
                   class="apwp-admin-button apwp-admin-button-success" 
                   data-action="approve_adoption"
                   data-id="<?php echo esc_attr($item->id); ?>"
                   title="<?php esc_attr_e('Aprovar', 'amigopet-wp'); ?>">
                    <span class="dashicons dashicons-yes"></span>
                </a>
                
                <a href="#" 
                   class="apwp-admin-button apwp-admin-button-danger" 
                   data-action="reject_adoption"
                   data-id="<?php echo esc_attr($item->id); ?>"
                   title="<?php esc_attr_e('Rejeitar', 'amigopet-wp'); ?>">
                    <span class="dashicons dashicons-no"></span>
                </a>
            <?php endif; ?>
            
            <a href="<?php echo esc_url(admin_url('admin.php?page=amigopet-wp-adoptions-view&id=' . $item->id)); ?>" 
               class="apwp-admin-button apwp-admin-button-secondary" 
               title="<?php esc_attr_e('Detalhes', 'amigopet-wp'); ?>">
                <span class="dashicons dashicons-visibility"></span>
            </a>
        </div>
    </td>
</tr>
