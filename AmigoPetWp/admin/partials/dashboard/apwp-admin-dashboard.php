<?php
/**
 * Template para a página de dashboard do plugin
 *
 * @link       https://github.com/AmigoPetWp/amigopet-wp
 * @since      1.0.0
 *
 * @package    AmigoPet_Wp
 * @subpackage AmigoPet_Wp/admin/partials
 */

// Se este arquivo for chamado diretamente, aborte.
if (!defined('WPINC')) {
    die;
}

// Cria nonce para o AJAX
$dashboard_nonce = wp_create_nonce('apwp_dashboard_nonce');
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="apwp-dashboard-wrapper">
        <!-- Estatísticas Gerais -->
        <div class="apwp-dashboard-section">
            <h2>Estatísticas Gerais</h2>
            <div class="apwp-stats-grid">
                <div class="apwp-stat-box">
                    <h3>Pets</h3>
                    <p class="stat-number">
                        <span class="pets-aguardando">--</span> / <span class="pets-total">--</span>
                    </p>
                    <p class="stat-description">Aguardando Adoção / Total</p>
                </div>
                <div class="apwp-stat-box">
                    <h3>Adoções</h3>
                    <p class="stat-number">
                        <span class="adocoes-em-andamento">--</span> / <span class="adocoes-total">--</span>
                    </p>
                    <p class="stat-description">Em Andamento / Total</p>
                </div>
                <div class="apwp-stat-box">
                    <h3>Adotantes</h3>
                    <p class="stat-number">
                        <span class="adotantes-com-adocoes">--</span> / <span class="adotantes-total">--</span>
                    </p>
                    <p class="stat-description">Com Adoções / Total</p>
                </div>
                <div class="apwp-stat-box">
                    <h3>Termos Assinados</h3>
                    <p class="stat-number">
                        <span class="termos-assinados">--</span> / <span class="termos-total">--</span>
                    </p>
                    <p class="stat-description">Assinados / Total</p>
                </div>
            </div>
        </div>

        <!-- Atividades Recentes -->
        <div class="apwp-dashboard-section">
            <h2>Atividades Recentes</h2>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Atividade</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="3">Nenhuma atividade recente.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Links Rápidos -->
        <div class="apwp-dashboard-section">
            <h2><?php _e('Links Rápidos', 'amigopet-wp'); ?></h2>
            <div class="apwp-quick-links">
                <!-- Pets -->
                <div class="quick-links-group">
                    <h3><?php _e('Pets', 'amigopet-wp'); ?></h3>
                    <a href="post-new.php?post_type=animal" class="button">
                        <span class="dashicons dashicons-plus-alt"></span>
                        <?php _e('Cadastrar Pet', 'amigopet-wp'); ?>
                    </a>
                    <a href="edit.php?post_type=animal" class="button">
                        <span class="dashicons dashicons-pets"></span>
                        <?php _e('Gerenciar Pets', 'amigopet-wp'); ?>
                    </a>
                </div>

                <!-- Adoções -->
                <div class="quick-links-group">
                    <h3><?php _e('Adoções', 'amigopet-wp'); ?></h3>
                    <a href="admin.php?page=amigopet-wp-adoptions&action=add" class="button">
                        <span class="dashicons dashicons-plus-alt"></span>
                        <?php _e('Nova Adoção', 'amigopet-wp'); ?>
                    </a>
                    <a href="admin.php?page=amigopet-wp-adoption-reports" class="button">
                        <span class="dashicons dashicons-list-view"></span>
                        <?php _e('Relatório de Adoções', 'amigopet-wp'); ?>
                    </a>
                </div>

                <!-- Adotantes -->
                <div class="quick-links-group">
                    <h3><?php _e('Adotantes', 'amigopet-wp'); ?></h3>
                    <a href="admin.php?page=amigopet-wp-adopters&action=add" class="button">
                        <span class="dashicons dashicons-plus-alt"></span>
                        <?php _e('Novo Adotante', 'amigopet-wp'); ?>
                    </a>
                    <a href="admin.php?page=amigopet-wp-adopter-reports" class="button">
                        <span class="dashicons dashicons-groups"></span>
                        <?php _e('Gerenciar Adotantes', 'amigopet-wp'); ?>
                    </a>
                </div>

                <!-- Documentos -->
                <div class="quick-links-group">
                    <h3><?php _e('Documentos', 'amigopet-wp'); ?></h3>
                    <a href="admin.php?page=amigopet-wp-terms" class="button">
                        <span class="dashicons dashicons-media-document"></span>
                        <?php _e('Termos e Contratos', 'amigopet-wp'); ?>
                    </a>
                    <a href="admin.php?page=amigopet-wp-term-reports" class="button">
                        <span class="dashicons dashicons-analytics"></span>
                        <?php _e('Relatório de Assinaturas', 'amigopet-wp'); ?>
                    </a>
                </div>

                <!-- Configurações -->
                <div class="quick-links-group">
                    <h3><?php _e('Sistema', 'amigopet-wp'); ?></h3>
                    <a href="admin.php?page=amigopet-wp-settings" class="button">
                        <span class="dashicons dashicons-admin-settings"></span>
                        <?php _e('Configurações', 'amigopet-wp'); ?>
                    </a>
                    <a href="admin.php?page=amigopet-wp-help" class="button">
                        <span class="dashicons dashicons-editor-help"></span>
                        <?php _e('Ajuda', 'amigopet-wp'); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(function($) {
        // Faz requisição AJAX para buscar estatísticas
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                action: 'apwp_dashboard_stats',
                nonce: '<?php echo $dashboard_nonce; ?>'
            },
            success: function(response) {
                // Atualiza estatísticas
                $('.pets-aguardando').text(response.pets_aguardando);
                $('.pets-total').text(response.pets_total);
                $('.adocoes-em-andamento').text(response.adocoes_em_andamento);
                $('.adocoes-total').text(response.adocoes_total);
                $('.adotantes-com-adocoes').text(response.adotantes_com_adocoes);
                $('.adotantes-total').text(response.adotantes_total);
                $('.termos-assinados').text(response.termos_assinados);
                $('.termos-total').text(response.termos_total);
            }
        });
    });
</script>
