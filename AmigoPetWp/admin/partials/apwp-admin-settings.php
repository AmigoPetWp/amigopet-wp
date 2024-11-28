<?php
/**
 * Template para a página de configurações do plugin
 *
 * @link       https://github.com/wendelmax/amigopet-wp
 * @since      1.0.0
 *
 * @package    AmigoPet_Wp
 * @subpackage AmigoPet_Wp/admin/partials
 */

// Se este arquivo for chamado diretamente, aborte.
if (!defined('WPINC')) {
    die;
}
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="apwp-settings-wrapper">
        <form method="post" action="options.php">
            <?php
            settings_fields('apwp_options');
            do_settings_sections('apwp_settings');
            ?>
            
            <!-- Configurações Gerais -->
            <div class="apwp-settings-section">
                <h2>Configurações Gerais</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="apwp_org_name">Nome da Organização</label>
                        </th>
                        <td>
                            <input type="text" id="apwp_org_name" name="apwp_options[org_name]" class="regular-text" 
                                value="<?php echo esc_attr(get_option('apwp_options')['org_name'] ?? ''); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="apwp_org_email">Email da Organização</label>
                        </th>
                        <td>
                            <input type="email" id="apwp_org_email" name="apwp_options[org_email]" class="regular-text" 
                                value="<?php echo esc_attr(get_option('apwp_options')['org_email'] ?? ''); ?>">
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Configurações de Adoção -->
            <div class="apwp-settings-section">
                <h2>Configurações de Adoção</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="apwp_adoption_approval">Aprovação de Adoção</label>
                        </th>
                        <td>
                            <select id="apwp_adoption_approval" name="apwp_options[adoption_approval]">
                                <option value="auto" <?php selected(get_option('apwp_options')['adoption_approval'] ?? '', 'auto'); ?>>
                                    Automática
                                </option>
                                <option value="manual" <?php selected(get_option('apwp_options')['adoption_approval'] ?? '', 'manual'); ?>>
                                    Manual
                                </option>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Configurações de Email -->
            <div class="apwp-settings-section">
                <h2>Configurações de Email</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="apwp_email_notifications">Notificações por Email</label>
                        </th>
                        <td>
                            <input type="checkbox" id="apwp_email_notifications" name="apwp_options[email_notifications]" value="1"
                                <?php checked(get_option('apwp_options')['email_notifications'] ?? '', 1); ?>>
                            <span class="description">Enviar notificações por email para adotantes e administradores</span>
                        </td>
                    </tr>
                </table>
            </div>

            <?php submit_button(); ?>
        </form>
    </div>
</div>
