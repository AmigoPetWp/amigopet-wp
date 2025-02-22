<?php
/**
 * Template para impressão do termo de adoção
 */
if (!defined('ABSPATH')) {
    exit;
}

$pet = isset($pet) ? $pet : null;
$user = wp_get_current_user();
$settings = get_option('amigopet_settings', []);
$terms = isset($settings['adoption_terms']) ? $settings['adoption_terms'] : '';

// Processa os placeholders
$data = [
    'adopter_name' => $user->display_name,
    'adopter_cpf' => get_user_meta($user->ID, 'cpf', true),
    'adopter_rg' => get_user_meta($user->ID, 'rg', true),
    'adopter_birth' => get_user_meta($user->ID, 'birth_date', true),
    'adopter_address' => get_user_meta($user->ID, 'address', true),
    'adopter_phone' => get_user_meta($user->ID, 'phone', true),
    'adopter_email' => $user->user_email
];

if ($pet) {
    $data = array_merge($data, [
        'pet_name' => $pet->name,
        'pet_species' => $pet->species,
        'pet_breed' => $pet->breed,
        'pet_age' => $pet->age,
        'pet_gender' => $pet->gender,
        'pet_size' => $pet->size,
        'pet_chip' => $pet->chip_number
    ]);
}

// Dados da organização
$data = array_merge($data, [
    'org_name' => $settings['org_name'] ?? '',
    'org_cnpj' => $settings['org_cnpj'] ?? '',
    'org_address' => $settings['org_address'] ?? '',
    'org_phone' => $settings['org_phone'] ?? '',
    'org_email' => $settings['org_email'] ?? ''
]);

// Data e hora atual
$data = array_merge($data, [
    'current_date' => wp_date(get_option('date_format')),
    'current_time' => wp_date(get_option('time_format')),
    'current_datetime' => wp_date(get_option('date_format') . ' ' . get_option('time_format'))
]);

// Substitui os placeholders
$placeholders = array_map(function($key) {
    return '{' . $key . '}';
}, array_keys($data));

$terms = str_replace($placeholders, array_values($data), $terms);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php _e('Termo de Adoção', 'amigopet-wp'); ?></title>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            
            body {
                padding: 20px;
            }
        }
        
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #000;
            max-width: 800px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .header h1 {
            font-size: 24px;
            margin: 0 0 10px;
        }
        
        .content {
            margin-bottom: 40px;
        }
        
        .signature {
            margin-top: 60px;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            width: 300px;
            margin: 10px 0;
            text-align: center;
        }
        
        .buttons {
            text-align: center;
            margin-top: 40px;
        }
        
        .button {
            display: inline-block;
            padding: 10px 20px;
            background: #2271b1;
            color: #fff;
            text-decoration: none;
            border-radius: 3px;
            margin: 0 10px;
            cursor: pointer;
        }
        
        .button:hover {
            background: #135e96;
        }
        
        @media screen and (max-width: 600px) {
            body {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><?php _e('Termo de Adoção', 'amigopet-wp'); ?></h1>
        <?php if ($settings['org_name']): ?>
            <div class="org-name"><?php echo esc_html($settings['org_name']); ?></div>
        <?php endif; ?>
    </div>
    
    <div class="content">
        <?php echo wpautop($terms); ?>
    </div>
    
    <div class="signature">
        <div class="signature-line">
            <?php echo esc_html($user->display_name); ?><br>
            <?php if ($data['adopter_cpf']): ?>
                CPF: <?php echo esc_html($data['adopter_cpf']); ?>
            <?php endif; ?>
        </div>
        <div class="signature-line">
            <?php echo esc_html($settings['org_name']); ?><br>
            <?php if ($settings['org_cnpj']): ?>
                CNPJ: <?php echo esc_html($settings['org_cnpj']); ?>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="buttons no-print">
        <button onclick="window.print();" class="button">
            <?php _e('Imprimir Termo', 'amigopet-wp'); ?>
        </button>
        <a href="#" onclick="window.close();" class="button">
            <?php _e('Fechar', 'amigopet-wp'); ?>
        </a>
    </div>
</body>
</html>
