<?php
/**
 * Template para uma linha da tabela de adotantes
 * 
 * @var object $item O objeto do adotante
 * @package AmigoPet_Wp
 */

// Se este arquivo for chamado diretamente, aborte.
if (!defined('WPINC')) {
    die;
}
?>
<tr class="adopter-row">
    <td class="column-name">
        <div class="adopter-info">
            <strong><?php echo esc_html($item->name); ?></strong>
            <?php if ($item->document) : ?>
                <br><small><?php echo esc_html(__('CPF:', 'amigopet-wp') . ' ' . $item->document); ?></small>
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
    
    <td class="column-adoptions">
        <div class="adoptions-count">
            <?php
            $adoptions_count = isset($item->adoptions_count) ? intval($item->adoptions_count) : 0;
            printf(
                esc_html(_n('%d adoção', '%d adoções', $adoptions_count, 'amigopet-wp')),
                $adoptions_count
            );
            ?>
        </div>
    </td>
    
    <td class="column-actions">
        <div class="apwp-admin-actions">
            <a href="<?php echo esc_url(admin_url('admin.php?page=amigopet-wp-adopters-edit&id=' . $item->id)); ?>" 
               class="apwp-admin-button apwp-admin-button-secondary" 
               title="<?php esc_attr_e('Editar', 'amigopet-wp'); ?>">
                <span class="dashicons dashicons-edit"></span>
            </a>
            
            <a href="<?php echo esc_url(admin_url('admin.php?page=amigopet-wp-adoptions-list&adopter_id=' . $item->id)); ?>" 
               class="apwp-admin-button apwp-admin-button-primary" 
               title="<?php esc_attr_e('Ver Adoções', 'amigopet-wp'); ?>">
                <span class="dashicons dashicons-list-view"></span>
            </a>
            
            <a href="#" 
               class="apwp-admin-button apwp-admin-button-danger" 
               data-confirm="<?php esc_attr_e('Tem certeza que deseja excluir este adotante?', 'amigopet-wp'); ?>"
               data-action="delete_adopter"
               data-id="<?php echo esc_attr($item->id); ?>"
               title="<?php esc_attr_e('Excluir', 'amigopet-wp'); ?>">
                <span class="dashicons dashicons-trash"></span>
            </a>
        </div>
    </td>
</tr>
