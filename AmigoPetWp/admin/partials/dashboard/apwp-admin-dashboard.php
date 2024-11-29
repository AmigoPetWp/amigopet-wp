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
            <h2><?php _e('Estatísticas Gerais', 'amigopet-wp'); ?></h2>
            <div class="apwp-stats-grid">
                <div class="apwp-stat-box">
                    <h3><?php _e('Pets', 'amigopet-wp'); ?></h3>
                    <p class="stat-number">
                        <span class="pets-aguardando">--</span> / <span class="pets-total">--</span>
                    </p>
                    <p class="stat-description"><?php _e('Aguardando Adoção / Total', 'amigopet-wp'); ?></p>
                </div>
                <div class="apwp-stat-box">
                    <h3><?php _e('Adoções', 'amigopet-wp'); ?></h3>
                    <p class="stat-number">
                        <span class="adocoes-em-andamento">--</span> / <span class="adocoes-total">--</span>
                    </p>
                    <p class="stat-description"><?php _e('Em Andamento / Total', 'amigopet-wp'); ?></p>
                </div>
                <div class="apwp-stat-box">
                    <h3><?php _e('Adotantes', 'amigopet-wp'); ?></h3>
                    <p class="stat-number">
                        <span class="adotantes-com-adocoes">--</span> / <span class="adotantes-total">--</span>
                    </p>
                    <p class="stat-description"><?php _e('Com Adoções / Total', 'amigopet-wp'); ?></p>
                </div>
                <div class="apwp-stat-box">
                    <h3><?php _e('Termos', 'amigopet-wp'); ?></h3>
                    <p class="stat-number">
                        <span class="termos-assinados">--</span> / <span class="termos-total">--</span>
                    </p>
                    <p class="stat-description"><?php _e('Assinados / Total', 'amigopet-wp'); ?></p>
                </div>
            </div>
        </div>

        <!-- Atividade Recente -->
        <div class="apwp-dashboard-section">
            <h2><?php _e('Atividade Recente', 'amigopet-wp'); ?></h2>
            <div class="apwp-activity-grid">
                <!-- Pets Recentes -->
                <div class="apwp-activity-box">
                    <h3><?php _e('Pets Recentes', 'amigopet-wp'); ?></h3>
                    <ul class="recent-pets-list">
                        <li class="loading"><?php _e('Carregando...', 'amigopet-wp'); ?></li>
                    </ul>
                </div>
                
                <!-- Adoções Recentes -->
                <div class="apwp-activity-box">
                    <h3><?php _e('Adoções Recentes', 'amigopet-wp'); ?></h3>
                    <ul class="recent-adoptions-list">
                        <li class="loading"><?php _e('Carregando...', 'amigopet-wp'); ?></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Atividade Pendentes -->
        <div class="apwp-dashboard-section">
            <h2><?php _e('Atividade Pendentes', 'amigopet-wp'); ?></h2>
            <div class="apwp-activity-grid">
                <!-- to be implemented -->
            </div>
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
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'get_dashboard_stats',
                nonce: '<?php echo wp_create_nonce('apwp_dashboard_nonce'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    
                    // Atualiza estatísticas de pets
                    $('.pets-aguardando').text(data.pets.available);
                    $('.pets-total').text(data.pets.total);
                    
                    // Atualiza estatísticas de adoções
                    $('.adocoes-em-andamento').text(data.adoptions.in_progress);
                    $('.adocoes-total').text(data.adoptions.total);
                    
                    // Atualiza estatísticas de adotantes
                    $('.adotantes-com-adocoes').text(data.adopters.with_adoptions);
                    $('.adotantes-total').text(data.adopters.total);
                    
                    // Atualiza estatísticas de termos
                    $('.termos-assinados').text(data.terms.signed);
                    $('.termos-total').text(data.terms.total);
                    
                    // Atualiza lista de pets recentes
                    const recentPetsList = $('.recent-pets-list');
                    recentPetsList.empty();
                    
                    data.recent_pets.forEach(pet => {
                        recentPetsList.append(`
                            <li>
                                <a href="admin.php?page=amigopet-wp-pets&action=edit&id=${pet.id}">
                                    ${pet.name} - ${pet.species} - ${pet.status}
                                </a>
                            </li>
                        `);
                    });
                    
                    // Atualiza lista de adoções recentes
                    const recentAdoptionsList = $('.recent-adoptions-list');
                    recentAdoptionsList.empty();
                    
                    data.recent_adoptions.forEach(adoption => {
                        recentAdoptionsList.append(`
                            <li>
                                <a href="admin.php?page=amigopet-wp-adoptions&action=edit&id=${adoption.id}">
                                    ${adoption.pet_name} - ${adoption.adopter_name} - ${adoption.status}
                                </a>
                            </li>
                        `);
                    });
                }
            }
        });
    });
</script>
