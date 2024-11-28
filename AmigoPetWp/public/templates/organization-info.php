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

<div class="apwp-org-info">
    <div class="apwp-org-header">
        <?php if ($organization->logo_url) : ?>
            <div class="apwp-org-logo">
                <img src="<?php echo esc_url($organization->logo_url); ?>" alt="<?php echo esc_attr($organization->name); ?>">
            </div>
        <?php endif; ?>
        
        <div class="apwp-org-title-group">
            <h2 class="apwp-org-title"><?php echo esc_html($organization->name); ?></h2>
            <?php if ($organization->cnpj) : ?>
                <div class="apwp-org-cnpj">CNPJ: <?php echo esc_html($organization->cnpj); ?></div>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($organization->description) : ?>
        <div class="apwp-org-description">
            <?php echo nl2br(esc_html($organization->description)); ?>
        </div>
    <?php endif; ?>

    <div class="apwp-org-contact">
        <?php if ($organization->email) : ?>
            <div class="apwp-org-contact-item">
                <i class="fas fa-envelope"></i>
                <a href="mailto:<?php echo esc_attr($organization->email); ?>">
                    <?php echo esc_html($organization->email); ?>
                </a>
            </div>
        <?php endif; ?>

        <?php if ($organization->phone) : ?>
            <div class="apwp-org-contact-item">
                <i class="fas fa-phone"></i>
                <a href="tel:<?php echo esc_attr($organization->phone); ?>">
                    <?php echo esc_html($organization->phone); ?>
                </a>
            </div>
        <?php endif; ?>

        <?php if ($organization->whatsapp) : ?>
            <div class="apwp-org-contact-item">
                <i class="fab fa-whatsapp"></i>
                <a href="https://wa.me/<?php echo esc_attr(preg_replace('/[^0-9]/', '', $organization->whatsapp)); ?>" target="_blank">
                    <?php echo esc_html($organization->whatsapp); ?>
                </a>
            </div>
        <?php endif; ?>

        <?php if ($organization->address) : ?>
            <div class="apwp-org-contact-item">
                <i class="fas fa-map-marker-alt"></i>
                <span><?php echo esc_html($organization->address); ?></span>
            </div>
        <?php endif; ?>
    </div>

    <?php if (!empty($organization->social_media)) : ?>
        <div class="apwp-org-social">
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
        <div class="apwp-org-bank-info">
            <h3>Dados Bancários para Doação</h3>
            <div class="apwp-bank-details">
                <?php foreach ($organization->bank_info as $label => $value) : ?>
                    <div class="apwp-bank-detail">
                        <span class="apwp-bank-label"><?php echo esc_html($label); ?>:</span>
                        <span class="apwp-bank-value"><?php echo esc_html($value); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <?php if ($organization->pix_key) : ?>
                <div class="apwp-pix-info">
                    <h4>PIX</h4>
                    <div class="apwp-pix-key">
                        <span class="apwp-pix-label">Chave:</span>
                        <span class="apwp-pix-value"><?php echo esc_html($organization->pix_key); ?></span>
                        <button class="apwp-button apwp-button-small apwp-copy-pix" data-pix="<?php echo esc_attr($organization->pix_key); ?>">
                            <i class="fas fa-copy"></i>
                            Copiar
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
