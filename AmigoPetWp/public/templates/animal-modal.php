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

<div class="pr-modal-header">
    <h2 class="pr-modal-title"><?php echo esc_html($animal->name); ?></h2>
    <div class="pr-animal-status pr-status-<?php echo esc_attr($animal->status); ?>">
        <?php echo esc_html(APWP_Core::get_status_label($animal->status)); ?>
    </div>
</div>

<div class="pr-modal-body">
    <div class="pr-animal-gallery">
        <?php if (!empty($animal->photos)) : ?>
            <div class="pr-gallery-main">
                <img src="<?php echo esc_url($animal->photos[0]); ?>" alt="<?php echo esc_attr($animal->name); ?>">
            </div>
            
            <?php if (count($animal->photos) > 1) : ?>
                <div class="pr-gallery-thumbs">
                    <?php foreach ($animal->photos as $index => $photo) : ?>
                        <div class="pr-gallery-thumb <?php echo $index === 0 ? 'active' : ''; ?>">
                            <img src="<?php echo esc_url($photo); ?>" alt="<?php echo esc_attr($animal->name); ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php else : ?>
            <div class="pr-gallery-main">
                <img src="<?php echo esc_url(APWP_PLUGIN_URL . 'public/images/no-photo.png'); ?>" alt="Sem foto">
            </div>
        <?php endif; ?>
    </div>

    <div class="pr-animal-content">
        <div class="pr-animal-details">
            <div class="pr-detail-group">
                <h3>Características</h3>
                <div class="pr-detail-grid">
                    <div class="pr-detail-item">
                        <span class="pr-detail-label">Espécie</span>
                        <span class="pr-detail-value">
                            <i class="fas fa-paw"></i>
                            <?php echo esc_html(APWP_Core::get_species_label($animal->species)); ?>
                        </span>
                    </div>
                    
                    <div class="pr-detail-item">
                        <span class="pr-detail-label">Porte</span>
                        <span class="pr-detail-value">
                            <i class="fas fa-ruler-vertical"></i>
                            <?php echo esc_html(APWP_Core::get_size_label($animal->size)); ?>
                        </span>
                    </div>
                    
                    <div class="pr-detail-item">
                        <span class="pr-detail-label">Idade</span>
                        <span class="pr-detail-value">
                            <i class="fas fa-birthday-cake"></i>
                            <?php echo esc_html(APWP_Core::get_age_label($animal->age)); ?>
                        </span>
                    </div>
                    
                    <div class="pr-detail-item">
                        <span class="pr-detail-label">Sexo</span>
                        <span class="pr-detail-value">
                            <?php if ($animal->gender === 'male') : ?>
                                <i class="fas fa-mars"></i>
                                Macho
                            <?php else : ?>
                                <i class="fas fa-venus"></i>
                                Fêmea
                            <?php endif; ?>
                        </span>
                    </div>
                    
                    <div class="pr-detail-item">
                        <span class="pr-detail-label">Castrado</span>
                        <span class="pr-detail-value">
                            <i class="fas fa-check-circle"></i>
                            <?php echo $animal->neutered ? 'Sim' : 'Não'; ?>
                        </span>
                    </div>
                    
                    <div class="pr-detail-item">
                        <span class="pr-detail-label">Vacinado</span>
                        <span class="pr-detail-value">
                            <i class="fas fa-syringe"></i>
                            <?php echo $animal->vaccinated ? 'Sim' : 'Não'; ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="pr-detail-group">
                <h3>História</h3>
                <p><?php echo nl2br(esc_html($animal->description)); ?></p>
            </div>

            <?php if (!empty($animal->temperament)) : ?>
                <div class="pr-detail-group">
                    <h3>Temperamento</h3>
                    <div class="pr-temperament-tags">
                        <?php foreach ($animal->temperament as $trait) : ?>
                            <span class="pr-tag"><?php echo esc_html($trait); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($animal->requirements)) : ?>
                <div class="pr-detail-group">
                    <h3>Requisitos para Adoção</h3>
                    <ul class="pr-requirements-list">
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
            <div class="pr-adoption-cta">
                <button class="pr-button pr-button-primary" data-action="adopt">
                    <i class="fas fa-heart"></i>
                    Quero Adotar
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>
