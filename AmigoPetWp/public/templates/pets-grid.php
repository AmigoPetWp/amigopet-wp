<?php
/**
 * Template para exibir a grade de pets
 */

// Se acessado diretamente, sai
if (!defined('ABSPATH')) {
    exit;
}

$pet = new APWP_Pet();
$pets = $pet->list(array(
    'status' => 'available',
    'limit' => 12
));
?>

<div class="apwp-pets-container">
    <?php if (!empty($pets)): ?>
        <div class="apwp-pets-grid">
            <?php foreach ($pets as $item): ?>
                <div class="apwp-pet-item" data-pet-id="<?php echo esc_attr($item->id); ?>">
                    <div class="apwp-pet-image">
                        <?php if (!empty($item->photo_url)): ?>
                            <img src="<?php echo esc_url($item->photo_url); ?>" 
                                 alt="<?php echo esc_attr($item->name); ?>">
                        <?php else: ?>
                            <div class="apwp-pet-no-image">
                                <span class="dashicons dashicons-pets"></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="apwp-pet-info">
                        <h3 class="apwp-pet-name"><?php echo esc_html($item->name); ?></h3>
                        
                        <div class="apwp-pet-details">
                            <?php if (!empty($item->breed)): ?>
                                <p class="apwp-pet-breed">
                                    <span class="dashicons dashicons-tag"></span>
                                    <?php echo esc_html($item->breed); ?>
                                </p>
                            <?php endif; ?>
                            
                            <?php if (!empty($item->age)): ?>
                                <p class="apwp-pet-age">
                                    <span class="dashicons dashicons-calendar"></span>
                                    <?php 
                                    echo esc_html(
                                        sprintf(
                                            _n('%d ano', '%d anos', $item->age, 'amigopet-wp'),
                                            $item->age
                                        )
                                    ); 
                                    ?>
                                </p>
                            <?php endif; ?>
                            
                            <p class="apwp-pet-gender">
                                <span class="dashicons dashicons-<?php echo $item->gender === 'male' ? 'businessman' : 'businesswoman'; ?>"></span>
                                <?php echo $item->gender === 'male' ? __('Macho', 'amigopet-wp') : __('Fêmea', 'amigopet-wp'); ?>
                            </p>
                            
                            <p class="apwp-pet-size">
                                <span class="dashicons dashicons-image-filter"></span>
                                <?php
                                $sizes = array(
                                    'small' => __('Pequeno', 'amigopet-wp'),
                                    'medium' => __('Médio', 'amigopet-wp'),
                                    'large' => __('Grande', 'amigopet-wp')
                                );
                                echo isset($sizes[$item->size]) ? $sizes[$item->size] : '';
                                ?>
                            </p>
                        </div>
                        
                        <button class="apwp-pet-adopt-button" data-pet-id="<?php echo esc_attr($item->id); ?>">
                            <?php _e('Quero Adotar', 'amigopet-wp'); ?>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="apwp-no-pets">
            <p><?php _e('Nenhum pet disponível para adoção no momento.', 'amigopet-wp'); ?></p>
        </div>
    <?php endif; ?>
</div>
