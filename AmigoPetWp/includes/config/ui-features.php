<?php
/**
 * Configuração das features de UI do plugin
 *
 * @package AmigoPet_Wp
 */

// Previne acesso direto
if (!defined('ABSPATH')) {
    exit;
}

return array(
    'filters' => array(
        'enabled' => true,
        'options' => array(
            'age' => array('0-1', '1-3', '3-5', '5+'),
            'size' => array('pequeno', 'médio', 'grande'),
            'gender' => array('macho', 'fêmea'),
            'species' => array('cachorro', 'gato', 'outros'),
        ),
    ),
    'gallery' => array(
        'enabled' => true,
        'max_images' => 10,
        'thumbnail_size' => array(300, 300),
        'large_size' => array(1200, 1200),
    ),
    'form' => array(
        'validation' => true,
        'masks' => true,
        'autosave' => true,
        'cep_autofill' => true,
        'document_upload' => array(
            'enabled' => true,
            'max_size' => '5MB',
            'allowed_types' => array('pdf', 'jpg', 'png'),
        ),
    ),
    'accessibility' => array(
        'aria_labels' => true,
        'keyboard_navigation' => true,
        'screen_reader' => true,
        'high_contrast' => true,
    ),
    'responsive' => array(
        'breakpoints' => array(
            'mobile' => '576px',
            'tablet' => '768px',
            'desktop' => '992px',
        ),
        'lazy_loading' => true,
    ),
    'social' => array(
        'share_buttons' => array('facebook', 'twitter', 'whatsapp'),
        'whatsapp_integration' => true,
        'social_feed' => false,
    ),
    'additional' => array(
        'favorites' => true,
        'view_history' => true,
        'email_notifications' => true,
        'live_chat' => false,
    ),
);
