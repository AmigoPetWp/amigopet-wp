<?php
/**
 * Template para a página de relatórios de pets
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
?>

<div class="wrap">
    <h1><?php echo esc_html__('Relatórios de Pets', 'amigopet-wp'); ?></h1>
    
    <div class="apwp-reports-grid">
        <!-- Relatório de Pets por Status -->
        <div class="apwp-report-card">
            <h2><?php echo esc_html__('Pets por Status', 'amigopet-wp'); ?></h2>
            <div class="apwp-report-content">
                <canvas id="petsStatusChart"></canvas>
            </div>
            <div class="apwp-report-actions">
                <button class="button" onclick="exportPetsStatusReport()">
                    <span class="dashicons dashicons-download"></span>
                    <?php echo esc_html__('Exportar', 'amigopet-wp'); ?>
                </button>
            </div>
        </div>

        <!-- Relatório de Pets por Espécie -->
        <div class="apwp-report-card">
            <h2><?php echo esc_html__('Pets por Espécie', 'amigopet-wp'); ?></h2>
            <div class="apwp-report-content">
                <canvas id="petsSpeciesChart"></canvas>
            </div>
            <div class="apwp-report-actions">
                <button class="button" onclick="exportPetsSpeciesReport()">
                    <span class="dashicons dashicons-download"></span>
                    <?php echo esc_html__('Exportar', 'amigopet-wp'); ?>
                </button>
            </div>
        </div>

        <!-- Relatório de Adoções por Período -->
        <div class="apwp-report-card">
            <h2><?php echo esc_html__('Adoções por Período', 'amigopet-wp'); ?></h2>
            <div class="apwp-report-content">
                <canvas id="adoptionsTimelineChart"></canvas>
            </div>
            <div class="apwp-report-actions">
                <button class="button" onclick="exportAdoptionsTimelineReport()">
                    <span class="dashicons dashicons-download"></span>
                    <?php echo esc_html__('Exportar', 'amigopet-wp'); ?>
                </button>
            </div>
        </div>

        <!-- Relatório de Pets por Idade -->
        <div class="apwp-report-card">
            <h2><?php echo esc_html__('Pets por Idade', 'amigopet-wp'); ?></h2>
            <div class="apwp-report-content">
                <canvas id="petsAgeChart"></canvas>
            </div>
            <div class="apwp-report-actions">
                <button class="button" onclick="exportPetsAgeReport()">
                    <span class="dashicons dashicons-download"></span>
                    <?php echo esc_html__('Exportar', 'amigopet-wp'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
{{ ... }}
