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

<div class="apwp-admin-wrap">
    <div class="apwp-admin-header">
        <h1 class="apwp-admin-title"><?php echo esc_html(get_admin_page_title()); ?></h1>
        <div class="apwp-admin-actions">
            <a href="<?php echo esc_url(admin_url('admin.php?page=amigopet-wp-pets-new')); ?>" class="apwp-admin-button apwp-admin-button-primary">
                <span class="dashicons dashicons-plus-alt"></span>
                <?php esc_html_e('Adicionar Pet', 'amigopet-wp'); ?>
            </a>
        </div>
    </div>

    <!-- Estatísticas -->
    <div class="apwp-admin-grid">
        <!-- Pets Disponíveis -->
        <div class="apwp-admin-card">
            <div class="apwp-admin-card-header">
                <h2 class="apwp-admin-card-title">
                    <span class="dashicons dashicons-pets"></span>
                    <?php esc_html_e('Pets', 'amigopet-wp'); ?>
                </h2>
            </div>
            <div class="apwp-admin-card-body">
                <div class="apwp-admin-stat">
                    <span class="pets-aguardando stat-number">--</span>
                    <span class="stat-label"><?php esc_html_e('Aguardando Adoção', 'amigopet-wp'); ?></span>
                </div>
                <div class="apwp-admin-stat">
                    <span class="pets-total stat-number">--</span>
                    <span class="stat-label"><?php esc_html_e('Total', 'amigopet-wp'); ?></span>
                </div>
            </div>
        </div>

        <!-- Adoções -->
        <div class="apwp-admin-card">
            <div class="apwp-admin-card-header">
                <h2 class="apwp-admin-card-title">
                    <span class="dashicons dashicons-heart"></span>
                    <?php esc_html_e('Adoções', 'amigopet-wp'); ?>
                </h2>
            </div>
            <div class="apwp-admin-card-body">
                <div class="apwp-admin-stat">
                    <span class="adocoes-em-andamento stat-number">--</span>
                    <span class="stat-label"><?php esc_html_e('Em Andamento', 'amigopet-wp'); ?></span>
                </div>
                <div class="apwp-admin-stat">
                    <span class="adocoes-total stat-number">--</span>
                    <span class="stat-label"><?php esc_html_e('Total', 'amigopet-wp'); ?></span>
                </div>
            </div>
        </div>

        <!-- Adotantes -->
        <div class="apwp-admin-card">
            <div class="apwp-admin-card-header">
                <h2 class="apwp-admin-card-title">
                    <span class="dashicons dashicons-groups"></span>
                    <?php esc_html_e('Adotantes', 'amigopet-wp'); ?>
                </h2>
            </div>
            <div class="apwp-admin-card-body">
                <div class="apwp-admin-stat">
                    <span class="adotantes-com-adocoes stat-number">--</span>
                    <span class="stat-label"><?php esc_html_e('Com Adoções', 'amigopet-wp'); ?></span>
                </div>
                <div class="apwp-admin-stat">
                    <span class="adotantes-total stat-number">--</span>
                    <span class="stat-label"><?php esc_html_e('Total', 'amigopet-wp'); ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Links Rápidos -->
    <div class="apwp-admin-card">
        <div class="apwp-admin-card-header">
            <h2 class="apwp-admin-card-title">
                <?php esc_html_e('Links Rápidos', 'amigopet-wp'); ?>
            </h2>
        </div>
        <div class="apwp-admin-card-body">
            <div class="apwp-admin-grid">
                <!-- Pets -->
                <div class="apwp-admin-widget">
                    <div class="apwp-admin-widget-header">
                        <h3 class="apwp-admin-widget-title">
                            <span class="dashicons dashicons-pets"></span>
                            <?php esc_html_e('Gerenciar Pets', 'amigopet-wp'); ?>
                        </h3>
                    </div>
                    <div class="apwp-admin-widget-body">
                        <a href="<?php echo esc_url(admin_url('admin.php?page=amigopet-wp-pets')); ?>" class="apwp-admin-button apwp-admin-button-secondary">
                            <?php esc_html_e('Lista de Pets', 'amigopet-wp'); ?>
                        </a>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=amigopet-wp-pets-new')); ?>" class="apwp-admin-button apwp-admin-button-secondary">
                            <?php esc_html_e('Adicionar Novo', 'amigopet-wp'); ?>
                        </a>
                    </div>
                </div>

                <!-- Adoções -->
                <div class="apwp-admin-widget">
                    <div class="apwp-admin-widget-header">
                        <h3 class="apwp-admin-widget-title">
                            <span class="dashicons dashicons-heart"></span>
                            <?php esc_html_e('Gerenciar Adoções', 'amigopet-wp'); ?>
                        </h3>
                    </div>
                    <div class="apwp-admin-widget-body">
                        <a href="<?php echo esc_url(admin_url('admin.php?page=amigopet-wp-adoptions')); ?>" class="apwp-admin-button apwp-admin-button-secondary">
                            <?php esc_html_e('Lista de Adoções', 'amigopet-wp'); ?>
                        </a>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=amigopet-wp-adoptions-pending')); ?>" class="apwp-admin-button apwp-admin-button-secondary">
                            <?php esc_html_e('Pendentes', 'amigopet-wp'); ?>
                        </a>
                    </div>
                </div>

                <!-- Adotantes -->
                <div class="apwp-admin-widget">
                    <div class="apwp-admin-widget-header">
                        <h3 class="apwp-admin-widget-title">
                            <span class="dashicons dashicons-groups"></span>
                            <?php esc_html_e('Gerenciar Adotantes', 'amigopet-wp'); ?>
                        </h3>
                    </div>
                    <div class="apwp-admin-widget-body">
                        <a href="<?php echo esc_url(admin_url('admin.php?page=amigopet-wp-adopters')); ?>" class="apwp-admin-button apwp-admin-button-secondary">
                            <?php esc_html_e('Lista de Adotantes', 'amigopet-wp'); ?>
                        </a>
                    </div>
                </div>

                <!-- Configurações -->
                <div class="apwp-admin-widget">
                    <div class="apwp-admin-widget-header">
                        <h3 class="apwp-admin-widget-title">
                            <span class="dashicons dashicons-admin-settings"></span>
                            <?php esc_html_e('Configurações', 'amigopet-wp'); ?>
                        </h3>
                    </div>
                    <div class="apwp-admin-widget-body">
                        <a href="<?php echo esc_url(admin_url('admin.php?page=amigopet-wp-settings')); ?>" class="apwp-admin-button apwp-admin-button-secondary">
                            <?php esc_html_e('Configurações Gerais', 'amigopet-wp'); ?>
                        </a>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=amigopet-wp-terms')); ?>" class="apwp-admin-button apwp-admin-button-secondary">
                            <?php esc_html_e('Termos e Condições', 'amigopet-wp'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Função para atualizar estatísticas
    function updateStats() {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'apwp_get_dashboard_stats',
                nonce: '<?php echo esc_js($dashboard_nonce); ?>'
            },
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    
                    // Atualiza os números
                    $('.pets-aguardando').text(data.pets_aguardando);
                    $('.pets-total').text(data.pets_total);
                    $('.adocoes-em-andamento').text(data.adocoes_pendentes);
                    $('.adocoes-total').text(data.adocoes_total);
                    $('.adotantes-com-adocoes').text(data.adotantes_com_adocoes);
                    $('.adotantes-total').text(data.adotantes_total);
                }
            }
        });
    }

    // Atualiza as estatísticas quando a página carrega
    updateStats();

    // Atualiza as estatísticas a cada 5 minutos
    setInterval(updateStats, 300000);
});
</script>
