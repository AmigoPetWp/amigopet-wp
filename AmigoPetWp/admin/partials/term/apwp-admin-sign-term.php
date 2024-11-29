<?php
/**
 * Template para assinatura de termos
 */

// Se este arquivo for chamado diretamente, aborte.
if (!defined('WPINC')) {
    die;
}

// Verifica se o usuário está logado
if (!is_user_logged_in()) {
    wp_die(__('Você precisa estar logado para assinar termos.', 'amigopet-wp'));
}

// Obtém o template solicitado
$template_id = isset($_GET['template']) ? intval($_GET['template']) : 0;
$term_template = new APWP_Term_Template();
$template = $term_template->get($template_id);

if (!$template) {
    wp_die(__('Template não encontrado.', 'amigopet-wp'));
}

// Verifica se o usuário tem permissão para assinar este tipo de termo
$term_type = new APWP_Term_Type();
$type = $term_type->get($template['type_id']);
$user_roles = wp_get_current_user()->roles;
$allowed_roles = maybe_unserialize($type['roles']);

$can_sign = false;
foreach ($user_roles as $role) {
    if (in_array($role, $allowed_roles)) {
        $can_sign = true;
        break;
    }
}

if (!$can_sign) {
    wp_die(__('Você não tem permissão para assinar este tipo de termo.', 'amigopet-wp'));
}

// Verifica se o usuário já assinou este termo
$signed_term = new APWP_Signed_Term();
if ($signed_term->has_user_signed(get_current_user_id(), $template_id)) {
    wp_die(__('Você já assinou este termo.', 'amigopet-wp'));
}

// Processa o formulário de assinatura
if (isset($_POST['action']) && $_POST['action'] === 'sign_term') {
    if (check_admin_referer('apwp_sign_term', 'apwp_nonce')) {
        // Processa o conteúdo com os dados do usuário
        $user = wp_get_current_user();
        $content = $term_template->process_shortcodes($template['content'], array(
            'user' => array(
                'name' => $user->display_name,
                'email' => $user->user_email
            )
        ));
        
        // Salva o termo assinado
        $signed_term->template_id = $template_id;
        $signed_term->user_id = get_current_user_id();
        $signed_term->content = $content;
        $signed_term->signature = sanitize_text_field($_POST['signature']);
        $signed_term->ip_address = $_SERVER['REMOTE_ADDR'];
        $signed_term->status = 'active';
        
        if ($signed_term->save()) {
            // Envia o termo por email
            $signed_term->send_email();
            
            add_settings_error(
                'apwp_messages',
                'apwp_term_signed',
                __('Termo assinado com sucesso! Uma cópia foi enviada para seu email.', 'amigopet-wp'),
                'success'
            );
            
            // Redireciona para evitar reenvio do formulário
            wp_redirect(add_query_arg('signed', '1'));
            exit;
        }
    }
}

// Verifica se o termo foi assinado
$just_signed = isset($_GET['signed']) && $_GET['signed'] === '1';
?>

<div class="wrap">
    <h1><?php echo esc_html($template['title']); ?></h1>
    
    <?php settings_errors('apwp_messages'); ?>

    <?php if ($just_signed) : ?>
        <div class="notice notice-success">
            <p>
                <?php _e('Termo assinado com sucesso! Uma cópia foi enviada para seu email.', 'amigopet-wp'); ?>
                <a href="<?php echo admin_url('admin.php?page=amigopet-wp-terms'); ?>" class="button button-small">
                    <?php _e('Voltar para Termos', 'amigopet-wp'); ?>
                </a>
            </p>
        </div>
    <?php else : ?>
        <div class="apwp-sign-term-container">
            <!-- Preview do termo -->
            <div class="apwp-term-preview">
                <h2><?php _e('Conteúdo do Termo', 'amigopet-wp'); ?></h2>
                
                <div class="term-content">
                    <?php
                    // Processa o conteúdo com os dados do usuário
                    $user = wp_get_current_user();
                    echo wpautop($term_template->process_shortcodes($template['content'], array(
                        'user' => array(
                            'name' => $user->display_name,
                            'email' => $user->user_email
                        )
                    )));
                    ?>
                </div>
            </div>

            <!-- Formulário de assinatura -->
            <div class="apwp-term-signature">
                <h2><?php _e('Assinatura', 'amigopet-wp'); ?></h2>
                
                <form method="post" action="" id="sign-term-form">
                    <?php wp_nonce_field('apwp_sign_term', 'apwp_nonce'); ?>
                    <input type="hidden" name="action" value="sign_term">
                    
                    <div class="signature-pad-container">
                        <canvas id="signature-pad"></canvas>
                        <input type="hidden" name="signature" id="signature-data">
                        
                        <div class="signature-pad-actions">
                            <button type="button" class="button" id="clear-signature">
                                <?php _e('Limpar Assinatura', 'amigopet-wp'); ?>
                            </button>
                        </div>
                    </div>
                    
                    <p class="description">
                        <?php _e('Use o mouse ou toque na tela para assinar.', 'amigopet-wp'); ?>
                    </p>
                    
                    <div class="term-agreement">
                        <label>
                            <input type="checkbox" name="agree" required>
                            <?php _e('Li e concordo com os termos acima.', 'amigopet-wp'); ?>
                        </label>
                    </div>
                    
                    <?php submit_button(__('Assinar Termo', 'amigopet-wp'), 'primary', 'submit', true, array('id' => 'submit-signature')); ?>
                </form>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.5/dist/signature_pad.umd.min.js"></script>
        <script>
        jQuery(document).ready(function($) {
            // Inicializa o pad de assinatura
            const canvas = document.getElementById('signature-pad');
            const signaturePad = new SignaturePad(canvas, {
                backgroundColor: 'rgb(255, 255, 255)'
            });
            
            // Ajusta o tamanho do canvas
            function resizeCanvas() {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
                signaturePad.clear();
            }
            
            window.addEventListener("resize", resizeCanvas);
            resizeCanvas();
            
            // Limpa a assinatura
            $('#clear-signature').on('click', function() {
                signaturePad.clear();
            });
            
            // Submete o formulário
            $('#sign-term-form').on('submit', function(e) {
                if (signaturePad.isEmpty()) {
                    e.preventDefault();
                    alert('<?php _e('Por favor, assine o termo antes de continuar.', 'amigopet-wp'); ?>');
                    return false;
                }
                
                $('#signature-data').val(signaturePad.toDataURL());
            });
        });
        </script>

        <style>
        .apwp-sign-term-container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin-top: 20px;
        }

        .apwp-term-preview,
        .apwp-term-signature {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .term-content {
            max-height: 600px;
            overflow-y: auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: #f9f9f9;
            margin: 20px 0;
        }

        .signature-pad-container {
            width: 100%;
            margin: 20px 0;
        }

        #signature-pad {
            width: 100%;
            height: 200px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: #fff;
        }

        .signature-pad-actions {
            margin-top: 10px;
            text-align: right;
        }

        .term-agreement {
            margin: 20px 0;
            padding: 15px;
            background: #f0f0f1;
            border-radius: 4px;
        }

        @media screen and (max-width: 782px) {
            .apwp-sign-term-container {
                grid-template-columns: 1fr;
            }
        }
        </style>
    <?php endif; ?>
