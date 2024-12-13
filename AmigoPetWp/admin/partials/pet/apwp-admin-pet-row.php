<?php
/**
 * Template para uma linha da tabela de pets
 * 
 * @var object $item O objeto do pet
 * @package AmigoPet_Wp
 */

// Se este arquivo for chamado diretamente, aborte.
if (!defined('WPINC')) {
    die;
}
?>
<tr class="pet-row <?php echo esc_attr($item->status); ?>">
    <td class="column-photo">
        <?php if ($item->photo_url) : ?>
            <img src="<?php echo esc_url($item->photo_url); ?>" alt="<?php echo esc_attr($item->name); ?>" class="pet-photo">
        <?php else : ?>
            <div class="pet-photo-placeholder">
                <span class="dashicons dashicons-pets"></span>
            </div>
        <?php endif; ?>
    </td>
    
    <td class="column-name">
        <strong><?php echo esc_html($item->name); ?></strong>
        <?php if ($item->chip_id) : ?>
            <br><small><?php echo esc_html(__('Chip:', 'amigopet-wp') . ' ' . $item->chip_id); ?></small>
        <?php endif; ?>
    </td>
    
    <td class="column-species">
        <?php echo esc_html($item->species); ?>
        <?php if ($item->breed) : ?>
            <br><small><?php echo esc_html($item->breed); ?></small>
        <?php endif; ?>
    </td>
    
    <td class="column-age">
        <?php 
        if ($item->birth_date) {
            $age = APWP_Utils::calculate_age($item->birth_date);
            echo esc_html($age);
        } else {
            echo esc_html($item->estimated_age);
        }
        ?>
    </td>
    
    <td class="column-status">
        <?php
        $status_class = '';
        $status_text = '';
        
        switch ($item->status) {
            case 'available':
                $status_class = 'apwp-status-available';
                $status_text = __('Disponível', 'amigopet-wp');
                break;
            case 'adopted':
                $status_class = 'apwp-status-adopted';
                $status_text = __('Adotado', 'amigopet-wp');
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
            <a href="<?php echo esc_url(admin_url('admin.php?page=amigopet-wp-pets-edit&id=' . $item->id)); ?>" 
               class="apwp-admin-button apwp-admin-button-secondary" 
               title="<?php esc_attr_e('Editar', 'amigopet-wp'); ?>">
                <span class="dashicons dashicons-edit"></span>
            </a>
            
            <?php if ($item->status === 'available') : ?>
                <a href="<?php echo esc_url(admin_url('admin.php?page=amigopet-wp-adoptions-new&pet_id=' . $item->id)); ?>" 
                   class="apwp-admin-button apwp-admin-button-success" 
                   title="<?php esc_attr_e('Iniciar Adoção', 'amigopet-wp'); ?>">
                    <span class="dashicons dashicons-heart"></span>
                </a>
            <?php endif; ?>
            
            <a href="#" 
               class="apwp-admin-button apwp-admin-button-danger" 
               data-confirm="<?php esc_attr_e('Tem certeza que deseja excluir este pet?', 'amigopet-wp'); ?>"
               data-action="delete_pet"
               data-id="<?php echo esc_attr($item->id); ?>"
               title="<?php esc_attr_e('Excluir', 'amigopet-wp'); ?>">
                <span class="dashicons dashicons-trash"></span>
            </a>
        </div>
    </td>
</tr>
