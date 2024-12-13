<?php
/**
 * Template para uma linha da tabela de voluntários
 * 
 * @var object $item O objeto do voluntário
 * @package AmigoPet_Wp
 */

// Se este arquivo for chamado diretamente, aborte.
if (!defined('WPINC')) {
    die;
}
?>
<tr class="volunteer-row <?php echo esc_attr($item->status); ?>">
    <td class="column-name">
        <div class="volunteer-info">
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
    
    <td class="column-skills">
        <?php if (!empty($item->skills)) : ?>
            <div class="skills-list">
                <?php foreach ($item->skills as $skill) : ?>
                    <span class="skill-tag"><?php echo esc_html($skill); ?></span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </td>
    
    <td class="column-availability">
        <div class="availability-info">
            <?php
            if (!empty($item->availability)) {
                $days = array(
                    'monday' => __('Segunda', 'amigopet-wp'),
                    'tuesday' => __('Terça', 'amigopet-wp'),
                    'wednesday' => __('Quarta', 'amigopet-wp'),
                    'thursday' => __('Quinta', 'amigopet-wp'),
                    'friday' => __('Sexta', 'amigopet-wp'),
                    'saturday' => __('Sábado', 'amigopet-wp'),
                    'sunday' => __('Domingo', 'amigopet-wp')
                );
                
                $available_days = array_filter(array_map(function($day) use ($item, $days) {
                    return isset($item->availability[$day]) && $item->availability[$day] ? $days[$day] : null;
                }, array_keys($days)));
                
                echo esc_html(implode(', ', $available_days));
            }
            ?>
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
            <a href="<?php echo esc_url(admin_url('admin.php?page=amigopet-wp-volunteers-edit&id=' . $item->id)); ?>" 
               class="apwp-admin-button apwp-admin-button-secondary" 
               title="<?php esc_attr_e('Editar', 'amigopet-wp'); ?>">
                <span class="dashicons dashicons-edit"></span>
            </a>
            
            <?php if ($item->status === 'pending') : ?>
                <a href="#" 
                   class="apwp-admin-button apwp-admin-button-success" 
                   data-action="approve_volunteer"
                   data-id="<?php echo esc_attr($item->id); ?>"
                   title="<?php esc_attr_e('Aprovar', 'amigopet-wp'); ?>">
                    <span class="dashicons dashicons-yes"></span>
                </a>
            <?php endif; ?>
            
            <?php if ($item->status === 'active') : ?>
                <a href="#" 
                   class="apwp-admin-button apwp-admin-button-warning" 
                   data-action="deactivate_volunteer"
                   data-id="<?php echo esc_attr($item->id); ?>"
                   title="<?php esc_attr_e('Desativar', 'amigopet-wp'); ?>">
                    <span class="dashicons dashicons-pause"></span>
                </a>
            <?php elseif ($item->status === 'inactive') : ?>
                <a href="#" 
                   class="apwp-admin-button apwp-admin-button-success" 
                   data-action="activate_volunteer"
                   data-id="<?php echo esc_attr($item->id); ?>"
                   title="<?php esc_attr_e('Ativar', 'amigopet-wp'); ?>">
                    <span class="dashicons dashicons-play"></span>
                </a>
            <?php endif; ?>
            
            <a href="#" 
               class="apwp-admin-button apwp-admin-button-danger" 
               data-confirm="<?php esc_attr_e('Tem certeza que deseja excluir este voluntário?', 'amigopet-wp'); ?>"
               data-action="delete_volunteer"
               data-id="<?php echo esc_attr($item->id); ?>"
               title="<?php esc_attr_e('Excluir', 'amigopet-wp'); ?>">
                <span class="dashicons dashicons-trash"></span>
            </a>
        </div>
    </td>
</tr>
