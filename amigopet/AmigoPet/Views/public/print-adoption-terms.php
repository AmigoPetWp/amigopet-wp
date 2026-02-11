<?php declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Template para impressão do termo de adoção
 */

$pet = isset($pet) ? $pet : null;
$user = wp_get_current_user();
$settings = get_option('amigopet_settings', []);
$terms = isset($settings['adoption_terms']) ? $settings['adoption_terms'] : '';

// Processa os placeholders
$apwp_data = [
    'adopter_name' => $user->display_name,
    'adopter_cpf' => get_user_meta($user->ID, 'cpf', true),
    'adopter_rg' => get_user_meta($user->ID, 'rg', true),
    'adopter_birth' => get_user_meta($user->ID, 'birth_date', true),
    'adopter_address' => get_user_meta($user->ID, 'address', true),
    'adopter_phone' => get_user_meta($user->ID, 'phone', true),
    'adopter_email' => $user->user_email
];

if ($pet) {
    $apwp_data = array_merge($apwp_data, [
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
$apwp_data = array_merge($apwp_data, [
    'org_name' => $settings['org_name'] ?? '',
    'org_cnpj' => $settings['org_cnpj'] ?? '',
    'org_address' => $settings['org_address'] ?? '',
    'org_phone' => $settings['org_phone'] ?? '',
    'org_email' => $settings['org_email'] ?? ''
]);

// Data e hora atual
$apwp_data = array_merge($apwp_data, [
    'current_date' => wp_date(get_option('date_format')),
    'current_time' => wp_date(get_option('time_format')),
    'current_datetime' => wp_date(get_option('date_format') . ' ' . get_option('time_format'))
]);

// Substitui os placeholders
$placeholders = array_map(function ($key) {
    return '{' . $key . '}';
}, array_keys($apwp_data));

$terms = str_replace($placeholders, array_values($apwp_data), $terms);
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php esc_html_e('Termo de Adoção', 'amigopet'); ?></title>
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
            display: flex;
            justify-content: space-between;
        }

        .signature-line {
            border-top: 1px solid #000;
            width: 300px;
            margin: 10px 0;
            text-align: center;
            padding-top: 10px;
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
            border: none;
        }

        .button:hover {
            background: #135e96;
        }

        @media screen and (max-width: 600px) {
            body {
                padding: 20px;
            }

            .signature {
                flex-direction: column;
                align-items: center;
            }

            .signature-line {
                margin-bottom: 40px;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <h1><?php esc_html_e('Termo de Adoção', 'amigopet'); ?></h1>
        <?php if (!empty($settings['org_name'])): ?>
            <div class="org-name"><?php echo esc_html($settings['org_name']); ?></div>
        <?php endif; ?>
    </div>

    <div class="content">
        <?php echo wp_kses_post(wpautop($terms)); ?>
    </div>

    <div class="signature">
        <div class="signature-line">
            <?php echo esc_html($user->display_name); ?><br>
            <?php if (!empty($apwp_data['adopter_cpf'])): ?>
                CPF: <?php echo esc_html($apwp_data['adopter_cpf']); ?>
            <?php endif; ?>
        </div>
        <div class="signature-line">
            <?php echo esc_html($settings['org_name'] ?? ''); ?><br>
            <?php if (!empty($settings['org_cnpj'])): ?>
                CNPJ: <?php echo esc_html($settings['org_cnpj']); ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="buttons no-print">
        <button onclick="window.print();" class="button">
            <?php esc_html_e('Imprimir Termo', 'amigopet'); ?>
        </button>
        <button onclick="window.close();" class="button">
            <?php esc_html_e('Fechar', 'amigopet'); ?>
        </button>
    </div>
</body>

</html>