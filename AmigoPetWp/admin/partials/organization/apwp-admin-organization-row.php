<?php
/**
 * Template para uma linha da tabela de organizações
 * 
 * @var object $item O objeto da organização
 * @package AmigoPet_Wp
 */

// Se este arquivo for chamado diretamente, aborte.
if (!defined('WPINC')) {
    die;
}
?>
<tr class="organization-row <?php echo esc_attr($item->status); ?>">
    <td class="column-logo">
        <?php if ($item->logo_url) : ?>
            <img src="<?php echo esc_url($item->logo_url); ?>" alt="<?php echo esc_attr($item->name); ?>" class="organization-logo">
        <?php else : ?>
            <div class="organization-logo-placeholder">
                <span class="dashicons dashicons-building"></span>
            </div>
        <?php endif; ?>
    </td>
    
    <td class="column-name">
        <div class="organization-info">
            <strong><?php echo esc_html($item->name); ?></strong>
            <?php if ($item->document) : ?>
                <br><small><?php echo esc_html(__('CNPJ:', 'amigopet-wp') . ' ' . $item->document); ?></small>
            <?php endif; ?>
        </div>
    </td>
    
    <td class="column-contact">
        <div class="contact-info">
            <span><?php echo esc_html($item->email); ?></span>
            <?php if ($item->phone) : ?>
                <br><small><?php echo esc_html($item->phone); ?></small>
            <?php endif; ?>
        </div>
    </td>
    
    <td class="column-address">
        <div class="address-info">
            <?php
            $address_parts = array_filter([
                $item->address_street,
                $item->address_number,
                $item->address_neighborhood,
                $item->address_city,
                $item->address_state
            ]);
            echo esc_html(implode(', ', $address_parts));
            ?>
        </div>
    </td>
    
    <td class="column-type">
        <?php
        $type_text = '';
        switch ($item->type) {
            case 'shelter':
                $type_text = __('Abrigo', 'amigopet-wp');
                break;
            case 'clinic':
                $type_text = __('Clínica', 'amigopet-wp');
                break;
            case 'ngo':
                $type_text = __('ONG', 'amigopet-wp');
                break;
        }
        ?>
        <span class="organization-type-tag">
            <?php echo esc_html($type_text); ?>
        </span>
    </td>
    
    <td class="column-status">
        <?php
        $status_class = '';
        $status_text = '';
        
        switch ($item->status) {
            case 'active':
                $status_class = 'apwp-status-active';
                $status_text = __('Ativa', 'amigopet-wp');
                break;
            case 'inactive':
                $status_class = 'apwp-status-inactive';
                $status_text = __('Inativa', 'amigopet-wp');
                break;
            case 'pending':
                $status_class = 'apwp-status-pending';
                $status_text = __('Pendente', 'amigopet-wp');
                break;
        }
        ?>
        <span class="apwp-status-tag <?php echo esc_attr($status_class); ?>">
            <?php echo esc_html($status_text); ?>
        </span>
    </td>
    
    <td class="column-actions">
        <div class="apwp-admin-actions">
            <a href="<?php echo esc_url(admin_url('admin.php?page=amigopet-wp-organizations-edit&id=' . $item->id)); ?>" 
               class="apwp-admin-button apwp-admin-button-secondary" 
               title="<?php esc_attr_e('Editar', 'amigopet-wp'); ?>">
                <span class="dashicons dashicons-edit"></span>
            </a>
            
            <?php if ($item->status === 'pending') : ?>
                <a href="#" 
                   class="apwp-admin-button apwp-admin-button-success" 
                   data-action="approve_organization"
                   data-id="<?php echo esc_attr($item->id); ?>"
                   title="<?php esc_attr_e('Aprovar', 'amigopet-wp'); ?>">
                    <span class="dashicons dashicons-yes"></span>
                </a>
            <?php endif; ?>
            
            <?php if ($item->status === 'active') : ?>
                <a href="#" 
                   class="apwp-admin-button apwp-admin-button-warning" 
                   data-action="deactivate_organization"
                   data-id="<?php echo esc_attr($item->id); ?>"
                   title="<?php esc_attr_e('Desativar', 'amigopet-wp'); ?>">
                    <span class="dashicons dashicons-pause"></span>
                </a>
            <?php elseif ($item->status === 'inactive') : ?>
                <a href="#" 
                   class="apwp-admin-button apwp-admin-button-success" 
                   data-action="activate_organization"
                   data-id="<?php echo esc_attr($item->id); ?>"
                   title="<?php esc_attr_e('Ativar', 'amigopet-wp'); ?>">
                    <span class="dashicons dashicons-play"></span>
                </a>
            <?php endif; ?>
            
            <a href="#" 
               class="apwp-admin-button apwp-admin-button-danger" 
               data-confirm="<?php esc_attr_e('Tem certeza que deseja excluir esta organização?', 'amigopet-wp'); ?>"
               data-action="delete_organization"
               data-id="<?php echo esc_attr($item->id); ?>"
               title="<?php esc_attr_e('Excluir', 'amigopet-wp'); ?>">
                <span class="dashicons dashicons-trash"></span>
            </a>
        </div>
    </td>
</tr>
