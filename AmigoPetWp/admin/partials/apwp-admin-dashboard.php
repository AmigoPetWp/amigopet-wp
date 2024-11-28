<?php
/**
 * Template para a página de dashboard do plugin
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
    
    <div class="apwp-dashboard-wrapper">
        <!-- Estatísticas Gerais -->
        <div class="apwp-dashboard-section">
            <h2>Estatísticas Gerais</h2>
            <div class="apwp-stats-grid">
                <div class="apwp-stat-box">
                    <h3>Animais</h3>
                    <p class="stat-number"><?php echo wp_count_posts('animal')->publish; ?></p>
                </div>
                <div class="apwp-stat-box">
                    <h3>Adoções</h3>
                    <p class="stat-number">0</p>
                </div>
                <div class="apwp-stat-box">
                    <h3>Adotantes</h3>
                    <p class="stat-number">0</p>
                </div>
                <div class="apwp-stat-box">
                    <h3>Contratos</h3>
                    <p class="stat-number">0</p>
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
            <h2>Links Rápidos</h2>
            <div class="apwp-quick-links">
                <a href="post-new.php?post_type=animal" class="button button-primary">Adicionar Animal</a>
                <a href="admin.php?page=amigopet-wp-adoptions" class="button">Nova Adoção</a>
                <a href="admin.php?page=amigopet-wp-adopters" class="button">Novo Adotante</a>
                <a href="admin.php?page=amigopet-wp-contracts" class="button">Novo Contrato</a>
            </div>
        </div>
    </div>
</div>
