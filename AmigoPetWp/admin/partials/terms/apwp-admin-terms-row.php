<?php
/**
 * Template para uma linha da tabela de termos
 * 
 * @var object $item O objeto do termo
 * @package AmigoPet_Wp
 */

// Se este arquivo for chamado diretamente, aborte.
if (!defined('WPINC')) {
    die;
}
?>
<tr class="terms-row <?php echo esc_attr($item->status); ?>">
    <td class="column-title">
        <div class="terms-info">
            <strong><?php echo esc_html($item->title); ?></strong>
            <?php if ($item->version) : ?>
                <br><small><?php echo esc_html(__('Versão:', 'amigopet-wp') . ' ' . $item->version); ?></small>
            <?php endif; ?>
        </div>
    </td>
    
    <td class="column-type">
        <?php
        $type_text = '';
        switch ($item->type) {
            case 'adoption':
                $type_text = __('Adoção', 'amigopet-wp');
                break;
            case 'volunteer':
                $type_text = __('Voluntariado', 'amigopet-wp');
                break;
            case 'organization':
                $type_text = __('Organização', 'amigopet-wp');
                break;
            case 'privacy':
                $type_text = __('Privacidade', 'amigopet-wp');
                break;
        }
        ?>
        <span class="terms-type-tag">
            <?php echo esc_html($type_text); ?>
        </span>
    </td>
    
    <td class="column-date">
        <div class="date-info">
            <strong><?php echo esc_html(APWP_Utils::format_date($item->created_at)); ?></strong>
            <?php if ($item->updated_at && $item->updated_at !== $item->created_at) : ?>
                <br>
                <small>
                    <?php 
                    printf(
                        esc_html__('Atualizado em %s', 'amigopet-wp'),
                        APWP_Utils::format_date($item->updated_at)
                    ); 
                    ?>
                </small>
            <?php endif; ?>
        </div>
    </td>
    
    <td class="column-status">
        <?php
        $status_class = '';
        $status_text = '';
        
        switch ($item->status) {
            case 'active':
                $status_class = 'apwp-status-active';
                $status_text = __('Ativo', 'amigopet-wp');
                break;
            case 'inactive':
                $status_class = 'apwp-status-inactive';
                $status_text = __('Inativo', 'amigopet-wp');
                break;
            case 'draft':
                $status_class = 'apwp-status-draft';
                $status_text = __('Rascunho', 'amigopet-wp');
                break;
        }
        ?>
        <span class="apwp-status-tag <?php echo esc_attr($status_class); ?>">
            <?php echo esc_html($status_text); ?>
        </span>
    </td>
    
    <td class="column-actions">
        <div class="apwp-admin-actions">
            <a href="<?php echo esc_url(admin_url('admin.php?page=amigopet-wp-terms-edit&id=' . $item->id)); ?>" 
               class="apwp-admin-button apwp-admin-button-secondary" 
               title="<?php esc_attr_e('Editar', 'amigopet-wp'); ?>">
                <span class="dashicons dashicons-edit"></span>
            </a>
            
            <a href="<?php echo esc_url(admin_url('admin.php?page=amigopet-wp-terms-view&id=' . $item->id)); ?>" 
               class="apwp-admin-button apwp-admin-button-primary" 
               title="<?php esc_attr_e('Visualizar', 'amigopet-wp'); ?>">
                <span class="dashicons dashicons-visibility"></span>
            </a>
            
            <?php if ($item->status === 'draft') : ?>
                <a href="#" 
                   class="apwp-admin-button apwp-admin-button-success" 
                   data-action="publish_terms"
                   data-id="<?php echo esc_attr($item->id); ?>"
                   title="<?php esc_attr_e('Publicar', 'amigopet-wp'); ?>">
                    <span class="dashicons dashicons-yes"></span>
                </a>
            <?php elseif ($item->status === 'active') : ?>
                <a href="#" 
                   class="apwp-admin-button apwp-admin-button-warning" 
                   data-action="deactivate_terms"
                   data-id="<?php echo esc_attr($item->id); ?>"
                   title="<?php esc_attr_e('Desativar', 'amigopet-wp'); ?>">
                    <span class="dashicons dashicons-pause"></span>
                </a>
            <?php elseif ($item->status === 'inactive') : ?>
                <a href="#" 
                   class="apwp-admin-button apwp-admin-button-success" 
                   data-action="activate_terms"
                   data-id="<?php echo esc_attr($item->id); ?>"
                   title="<?php esc_attr_e('Ativar', 'amigopet-wp'); ?>">
                    <span class="dashicons dashicons-play"></span>
                </a>
            <?php endif; ?>
            
            <a href="#" 
               class="apwp-admin-button apwp-admin-button-info" 
               data-action="duplicate_terms"
               data-id="<?php echo esc_attr($item->id); ?>"
               title="<?php esc_attr_e('Duplicar', 'amigopet-wp'); ?>">
                <span class="dashicons dashicons-admin-page"></span>
            </a>
            
            <?php if ($item->status !== 'active') : ?>
                <a href="#" 
                   class="apwp-admin-button apwp-admin-button-danger" 
                   data-confirm="<?php esc_attr_e('Tem certeza que deseja excluir este termo?', 'amigopet-wp'); ?>"
                   data-action="delete_terms"
                   data-id="<?php echo esc_attr($item->id); ?>"
                   title="<?php esc_attr_e('Excluir', 'amigopet-wp'); ?>">
                    <span class="dashicons dashicons-trash"></span>
                </a>
            <?php endif; ?>
        </div>
    </td>
</tr>
