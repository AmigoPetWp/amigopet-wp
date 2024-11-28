<?php
/**
 * Template para o modal de detalhes do pet
 */

// Se acessado diretamente, sai
if (!defined('ABSPATH')) {
    exit;
}
?>

<div id="apwp-pet-modal" class="apwp-modal">
    <div class="apwp-modal-content">
        <span class="apwp-modal-close">&times;</span>
        
        <div class="apwp-modal-body">
            <div class="apwp-pet-modal-image">
                <img src="" alt="" id="apwp-modal-pet-image">
            </div>
            
            <div class="apwp-pet-modal-info">
                <h2 id="apwp-modal-pet-name"></h2>
                
                <div class="apwp-pet-modal-details">
                    <div class="apwp-detail-item">
                        <span class="dashicons dashicons-tag"></span>
                        <span id="apwp-modal-pet-breed"></span>
                    </div>
                    
                    <div class="apwp-detail-item">
                        <span class="dashicons dashicons-calendar"></span>
                        <span id="apwp-modal-pet-age"></span>
                    </div>
                    
                    <div class="apwp-detail-item">
                        <span class="dashicons dashicons-businessman"></span>
                        <span id="apwp-modal-pet-gender"></span>
                    </div>
                    
                    <div class="apwp-detail-item">
                        <span class="dashicons dashicons-image-filter"></span>
                        <span id="apwp-modal-pet-size"></span>
                    </div>
                    
                    <div class="apwp-detail-item">
                        <span class="dashicons dashicons-performance"></span>
                        <span id="apwp-modal-pet-weight"></span>
                    </div>
                </div>
                
                <div class="apwp-pet-modal-description">
                    <h3><?php _e('Sobre mim', 'amigopet-wp'); ?></h3>
                    <p id="apwp-modal-pet-description"></p>
                </div>
                
                <div class="apwp-pet-modal-actions">
                    <button id="apwp-modal-adopt-button" class="button button-primary">
                        <?php _e('Quero Adotar', 'amigopet-wp'); ?>
                    </button>
                    
                    <button id="apwp-modal-contact-button" class="button">
                        <?php _e('Entrar em Contato', 'amigopet-wp'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="apwp-adoption-form-modal" class="apwp-modal">
    <div class="apwp-modal-content">
        <span class="apwp-modal-close">&times;</span>
        
        <div class="apwp-modal-body">
            <h2><?php _e('Formulário de Adoção', 'amigopet-wp'); ?></h2>
            
            <form id="apwp-adoption-form">
                <input type="hidden" id="apwp-adoption-pet-id" name="pet_id">
                
                <div class="apwp-form-row">
                    <label for="adopter_name"><?php _e('Nome Completo', 'amigopet-wp'); ?></label>
                    <input type="text" id="adopter_name" name="adopter_name" required>
                </div>
                
                <div class="apwp-form-row">
                    <label for="adopter_email"><?php _e('E-mail', 'amigopet-wp'); ?></label>
                    <input type="email" id="adopter_email" name="adopter_email" required>
                </div>
                
                <div class="apwp-form-row">
                    <label for="adopter_phone"><?php _e('Telefone', 'amigopet-wp'); ?></label>
                    <input type="tel" id="adopter_phone" name="adopter_phone" required>
                </div>
                
                <div class="apwp-form-row">
                    <label for="adopter_address"><?php _e('Endereço', 'amigopet-wp'); ?></label>
                    <textarea id="adopter_address" name="adopter_address" required></textarea>
                </div>
                
                <div class="apwp-form-row">
                    <label for="adoption_reason"><?php _e('Por que você quer adotar este pet?', 'amigopet-wp'); ?></label>
                    <textarea id="adoption_reason" name="adoption_reason" required></textarea>
                </div>
                
                <div class="apwp-form-actions">
                    <button type="submit" class="button button-primary">
                        <?php _e('Enviar Solicitação', 'amigopet-wp'); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
