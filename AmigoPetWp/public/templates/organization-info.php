<?php
/**
 * Template para exibição das informações da organização
 *
 * @package AmigoPet_Wp
 */

// Previne acesso direto
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="pr-org-info">
    <div class="pr-org-header">
        <?php if ($organization->logo_url) : ?>
            <div class="pr-org-logo">
                <img src="<?php echo esc_url($organization->logo_url); ?>" alt="<?php echo esc_attr($organization->name); ?>">
            </div>
        <?php endif; ?>
        
        <div class="pr-org-title-group">
            <h2 class="pr-org-title"><?php echo esc_html($organization->name); ?></h2>
            <?php if ($organization->cnpj) : ?>
                <div class="pr-org-cnpj">CNPJ: <?php echo esc_html($organization->cnpj); ?></div>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($organization->description) : ?>
        <div class="pr-org-description">
            <?php echo nl2br(esc_html($organization->description)); ?>
        </div>
    <?php endif; ?>

    <div class="pr-org-contact">
        <?php if ($organization->email) : ?>
            <div class="pr-org-contact-item">
                <i class="fas fa-envelope"></i>
                <a href="mailto:<?php echo esc_attr($organization->email); ?>">
                    <?php echo esc_html($organization->email); ?>
                </a>
            </div>
        <?php endif; ?>

        <?php if ($organization->phone) : ?>
            <div class="pr-org-contact-item">
                <i class="fas fa-phone"></i>
                <a href="tel:<?php echo esc_attr($organization->phone); ?>">
                    <?php echo esc_html($organization->phone); ?>
                </a>
            </div>
        <?php endif; ?>

        <?php if ($organization->whatsapp) : ?>
            <div class="pr-org-contact-item">
                <i class="fab fa-whatsapp"></i>
                <a href="https://wa.me/<?php echo esc_attr(preg_replace('/[^0-9]/', '', $organization->whatsapp)); ?>" target="_blank">
                    <?php echo esc_html($organization->whatsapp); ?>
                </a>
            </div>
        <?php endif; ?>

        <?php if ($organization->address) : ?>
            <div class="pr-org-contact-item">
                <i class="fas fa-map-marker-alt"></i>
                <span><?php echo esc_html($organization->address); ?></span>
            </div>
        <?php endif; ?>
    </div>

    <?php if (!empty($organization->social_media)) : ?>
        <div class="pr-org-social">
            <?php foreach ($organization->social_media as $network => $url) : ?>
                <?php if ($url) : ?>
                    <a href="<?php echo esc_url($url); ?>" target="_blank" title="<?php echo esc_attr(ucfirst($network)); ?>">
                        <i class="fab fa-<?php echo esc_attr($network); ?>"></i>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($organization->bank_info) : ?>
        <div class="pr-org-bank-info">
            <h3>Dados Bancários para Doação</h3>
            <div class="pr-bank-details">
                <?php foreach ($organization->bank_info as $label => $value) : ?>
                    <div class="pr-bank-detail">
                        <span class="pr-bank-label"><?php echo esc_html($label); ?>:</span>
                        <span class="pr-bank-value"><?php echo esc_html($value); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <?php if ($organization->pix_key) : ?>
                <div class="pr-pix-info">
                    <h4>PIX</h4>
                    <div class="pr-pix-key">
                        <span class="pr-pix-label">Chave:</span>
                        <span class="pr-pix-value"><?php echo esc_html($organization->pix_key); ?></span>
                        <button class="pr-button pr-button-small pr-copy-pix" data-pix="<?php echo esc_attr($organization->pix_key); ?>">
                            <i class="fas fa-copy"></i>
                            Copiar
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
