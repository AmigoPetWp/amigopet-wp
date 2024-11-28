<?php
/**
 * Template para o modal de detalhes do animal
 *
 * @package AmigoPet_Wp
 */

// Previne acesso direto
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="apwp-modal-header">
    <h2 class="apwp-modal-title"><?php echo esc_html($animal->name); ?></h2>
    <div class="apwp-animal-status apwp-status-<?php echo esc_attr($animal->status); ?>">
        <?php echo esc_html(APWP_Core::get_status_label($animal->status)); ?>
    </div>
</div>

<div class="apwp-modal-body">
    <div class="apwp-animal-gallery">
        <?php if (!empty($animal->photos)) : ?>
            <div class="apwp-gallery-main">
                <img src="<?php echo esc_url($animal->photos[0]); ?>" alt="<?php echo esc_attr($animal->name); ?>">
            </div>
            
            <?php if (count($animal->photos) > 1) : ?>
                <div class="apwp-gallery-thumbs">
                    <?php foreach ($animal->photos as $index => $photo) : ?>
                        <div class="apwp-gallery-thumb <?php echo $index === 0 ? 'active' : ''; ?>">
                            <img src="<?php echo esc_url($photo); ?>" alt="<?php echo esc_attr($animal->name); ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php else : ?>
            <div class="apwp-gallery-main">
                <img src="<?php echo esc_url(APWP_PLUGIN_URL . 'public/images/no-photo.png'); ?>" alt="Sem foto">
            </div>
        <?php endif; ?>
    </div>

    <div class="apwp-animal-content">
        <div class="apwp-animal-details">
            <div class="apwp-detail-group">
                <h3>Características</h3>
                <div class="apwp-detail-grid">
                    <div class="apwp-detail-item">
                        <span class="apwp-detail-label">Espécie</span>
                        <span class="apwp-detail-value">
                            <i class="fas fa-paw"></i>
                            <?php echo esc_html(APWP_Core::get_species_label($animal->species)); ?>
                        </span>
                    </div>
                    
                    <div class="apwp-detail-item">
                        <span class="apwp-detail-label">Porte</span>
                        <span class="apwp-detail-value">
                            <i class="fas fa-ruler-vertical"></i>
                            <?php echo esc_html(APWP_Core::get_size_label($animal->size)); ?>
                        </span>
                    </div>
                    
                    <div class="apwp-detail-item">
                        <span class="apwp-detail-label">Idade</span>
                        <span class="apwp-detail-value">
                            <i class="fas fa-birthday-cake"></i>
                            <?php echo esc_html(APWP_Core::get_age_label($animal->age)); ?>
                        </span>
                    </div>
                    
                    <div class="apwp-detail-item">
                        <span class="apwp-detail-label">Sexo</span>
                        <span class="apwp-detail-value">
                            <?php if ($animal->gender === 'male') : ?>
                                <i class="fas fa-mars"></i>
                                Macho
                            <?php else : ?>
                                <i class="fas fa-venus"></i>
                                Fêmea
                            <?php endif; ?>
                        </span>
                    </div>
                    
                    <div class="apwp-detail-item">
                        <span class="apwp-detail-label">Castrado</span>
                        <span class="apwp-detail-value">
                            <i class="fas fa-check-circle"></i>
                            <?php echo $animal->neutered ? 'Sim' : 'Não'; ?>
                        </span>
                    </div>
                    
                    <div class="apwp-detail-item">
                        <span class="apwp-detail-label">Vacinado</span>
                        <span class="apwp-detail-value">
                            <i class="fas fa-syringe"></i>
                            <?php echo $animal->vaccinated ? 'Sim' : 'Não'; ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="apwp-detail-group">
                <h3>História</h3>
                <p><?php echo nl2br(esc_html($animal->description)); ?></p>
            </div>

            <?php if (!empty($animal->temperament)) : ?>
                <div class="apwp-detail-group">
                    <h3>Temperamento</h3>
                    <div class="apwp-temperament-tags">
                        <?php foreach ($animal->temperament as $trait) : ?>
                            <span class="apwp-tag"><?php echo esc_html($trait); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($animal->requirements)) : ?>
                <div class="apwp-detail-group">
                    <h3>Requisitos para Adoção</h3>
                    <ul class="apwp-requirements-list">
                        <?php foreach ($animal->requirements as $requirement) : ?>
                            <li>
                                <i class="fas fa-check"></i>
                                <?php echo esc_html($requirement); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($animal->status === 'available') : ?>
            <div class="apwp-adoption-cta">
                <button class="apwp-button apwp-button-primary" data-action="adopt">
                    <i class="fas fa-heart"></i>
                    Quero Adotar
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>
