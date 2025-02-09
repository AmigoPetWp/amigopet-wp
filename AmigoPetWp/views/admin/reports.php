<?php
/**
 * Template para página de relatórios no admin
 */
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php _e('Relatórios', 'amigopet-wp'); ?></h1>
    
    <!-- Filtros -->
    <div class="apwp-reports-filters">
        <select id="apwp-date-range">
            <option value="7"><?php _e('Últimos 7 dias', 'amigopet-wp'); ?></option>
            <option value="30" selected><?php _e('Últimos 30 dias', 'amigopet-wp'); ?></option>
            <option value="90"><?php _e('Últimos 90 dias', 'amigopet-wp'); ?></option>
            <option value="365"><?php _e('Último ano', 'amigopet-wp'); ?></option>
            <option value="custom"><?php _e('Personalizado', 'amigopet-wp'); ?></option>
        </select>
        
        <div id="apwp-custom-date-range" style="display: none;">
            <input type="date" id="apwp-date-start" name="date_start">
            <input type="date" id="apwp-date-end" name="date_end">
            <button type="button" class="button" id="apwp-apply-date-range">
                <?php _e('Aplicar', 'amigopet-wp'); ?>
            </button>
        </div>
    </div>
    
    <!-- Cards de resumo -->
    <div class="apwp-summary-cards">
        <div class="apwp-card">
            <h3><?php _e('Adoções', 'amigopet-wp'); ?></h3>
            <div class="apwp-card-content">
                <div class="apwp-stat">
                    <span class="apwp-stat-value" id="adoptions-total">0</span>
                    <span class="apwp-stat-label"><?php _e('Total', 'amigopet-wp'); ?></span>
                </div>
                <div class="apwp-stat">
                    <span class="apwp-stat-value" id="adoptions-pending">0</span>
                    <span class="apwp-stat-label"><?php _e('Pendentes', 'amigopet-wp'); ?></span>
                </div>
                <div class="apwp-stat">
                    <span class="apwp-stat-value" id="adoptions-approved">0</span>
                    <span class="apwp-stat-label"><?php _e('Aprovadas', 'amigopet-wp'); ?></span>
                </div>
            </div>
        </div>
        
        <div class="apwp-card">
            <h3><?php _e('Doações', 'amigopet-wp'); ?></h3>
            <div class="apwp-card-content">
                <div class="apwp-stat">
                    <span class="apwp-stat-value" id="donations-total">R$ 0,00</span>
                    <span class="apwp-stat-label"><?php _e('Total em Dinheiro', 'amigopet-wp'); ?></span>
                </div>
                <div class="apwp-stat">
                    <span class="apwp-stat-value" id="donations-count">0</span>
                    <span class="apwp-stat-label"><?php _e('Número de Doações', 'amigopet-wp'); ?></span>
                </div>
                <div class="apwp-stat">
                    <span class="apwp-stat-value" id="donations-recurring">0</span>
                    <span class="apwp-stat-label"><?php _e('Doações Recorrentes', 'amigopet-wp'); ?></span>
                </div>
            </div>
        </div>
        
        <div class="apwp-card">
            <h3><?php _e('Voluntários', 'amigopet-wp'); ?></h3>
            <div class="apwp-card-content">
                <div class="apwp-stat">
                    <span class="apwp-stat-value" id="volunteers-total">0</span>
                    <span class="apwp-stat-label"><?php _e('Total', 'amigopet-wp'); ?></span>
                </div>
                <div class="apwp-stat">
                    <span class="apwp-stat-value" id="volunteers-active">0</span>
                    <span class="apwp-stat-label"><?php _e('Ativos', 'amigopet-wp'); ?></span>
                </div>
                <div class="apwp-stat">
                    <span class="apwp-stat-value" id="volunteers-hours">0</span>
                    <span class="apwp-stat-label"><?php _e('Horas Trabalhadas', 'amigopet-wp'); ?></span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Gráficos -->
    <div class="apwp-charts">
        <!-- Gráfico de Adoções -->
        <div class="apwp-chart-card">
            <h3><?php _e('Adoções por Período', 'amigopet-wp'); ?></h3>
            <canvas id="adoptions-chart"></canvas>
        </div>
        
        <!-- Gráfico de Doações -->
        <div class="apwp-chart-card">
            <h3><?php _e('Doações por Tipo', 'amigopet-wp'); ?></h3>
            <canvas id="donations-chart"></canvas>
        </div>
        
        <!-- Gráfico de Pets -->
        <div class="apwp-chart-card">
            <h3><?php _e('Status dos Pets', 'amigopet-wp'); ?></h3>
            <canvas id="pets-chart"></canvas>
        </div>
    </div>
    
    <!-- Tabelas de Top 5 -->
    <div class="apwp-top-tables">
        <!-- Top 5 Espécies mais adotadas -->
        <div class="apwp-table-card">
            <h3><?php _e('Espécies Mais Adotadas', 'amigopet-wp'); ?></h3>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('Espécie', 'amigopet-wp'); ?></th>
                        <th><?php _e('Total', 'amigopet-wp'); ?></th>
                        <th><?php _e('Porcentagem', 'amigopet-wp'); ?></th>
                    </tr>
                </thead>
                <tbody id="top-species"></tbody>
            </table>
        </div>
        
        <!-- Top 5 Eventos com mais inscritos -->
        <div class="apwp-table-card">
            <h3><?php _e('Eventos Mais Populares', 'amigopet-wp'); ?></h3>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('Evento', 'amigopet-wp'); ?></th>
                        <th><?php _e('Inscritos', 'amigopet-wp'); ?></th>
                        <th><?php _e('Ocupação', 'amigopet-wp'); ?></th>
                    </tr>
                </thead>
                <tbody id="top-events"></tbody>
            </table>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Configuração dos gráficos
    var adoptionsChart, donationsChart, petsChart;
    
    function initCharts() {
        // Gráfico de Adoções
        var adoptionsCtx = document.getElementById('adoptions-chart').getContext('2d');
        adoptionsChart = new Chart(adoptionsCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: '<?php _e('Adoções', 'amigopet-wp'); ?>',
                    data: [],
                    borderColor: '#2271b1',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
        
        // Gráfico de Doações
        var donationsCtx = document.getElementById('donations-chart').getContext('2d');
        donationsChart = new Chart(donationsCtx, {
            type: 'pie',
            data: {
                labels: [
                    '<?php _e('Dinheiro', 'amigopet-wp'); ?>',
                    '<?php _e('Ração', 'amigopet-wp'); ?>',
                    '<?php _e('Medicamentos', 'amigopet-wp'); ?>',
                    '<?php _e('Suprimentos', 'amigopet-wp'); ?>'
                ],
                datasets: [{
                    data: [],
                    backgroundColor: ['#2271b1', '#3582c4', '#4f94d4', '#72aee6']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
        
        // Gráfico de Pets
        var petsCtx = document.getElementById('pets-chart').getContext('2d');
        petsChart = new Chart(petsCtx, {
            type: 'doughnut',
            data: {
                labels: [
                    '<?php _e('Disponíveis', 'amigopet-wp'); ?>',
                    '<?php _e('Adotados', 'amigopet-wp'); ?>',
                    '<?php _e('Indisponíveis', 'amigopet-wp'); ?>'
                ],
                datasets: [{
                    data: [],
                    backgroundColor: ['#00a32a', '#2271b1', '#d63638']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }
    
    // Carrega os dados dos relatórios
    function loadReports() {
        var data = {
            action: 'apwp_get_reports',
            _ajax_nonce: apwp.nonce,
            date_range: $('#apwp-date-range').val(),
            date_start: $('#apwp-date-start').val(),
            date_end: $('#apwp-date-end').val()
        };
        
        $.post(apwp.ajax_url, data, function(response) {
            if (response.success) {
                // Atualiza os cards de resumo
                $('#adoptions-total').text(response.data.adoptions.total);
                $('#adoptions-pending').text(response.data.adoptions.pending);
                $('#adoptions-approved').text(response.data.adoptions.approved);
                
                $('#donations-total').text(response.data.donations.total_money);
                $('#donations-count').text(response.data.donations.total_count);
                $('#donations-recurring').text(response.data.donations.recurring);
                
                $('#volunteers-total').text(response.data.volunteers.total);
                $('#volunteers-active').text(response.data.volunteers.active);
                $('#volunteers-hours').text(response.data.volunteers.hours);
                
                // Atualiza os gráficos
                adoptionsChart.data.labels = response.data.charts.adoptions.labels;
                adoptionsChart.data.datasets[0].data = response.data.charts.adoptions.data;
                adoptionsChart.update();
                
                donationsChart.data.datasets[0].data = response.data.charts.donations;
                donationsChart.update();
                
                petsChart.data.datasets[0].data = response.data.charts.pets;
                petsChart.update();
                
                // Atualiza as tabelas de top 5
                var speciesHtml = '';
                $.each(response.data.top_species, function(i, item) {
                    speciesHtml += '<tr>' +
                        '<td>' + item.name + '</td>' +
                        '<td>' + item.total + '</td>' +
                        '<td>' + item.percentage + '%</td>' +
                        '</tr>';
                });
                $('#top-species').html(speciesHtml);
                
                var eventsHtml = '';
                $.each(response.data.top_events, function(i, item) {
                    eventsHtml += '<tr>' +
                        '<td>' + item.title + '</td>' +
                        '<td>' + item.registered + '</td>' +
                        '<td>' + item.occupation + '%</td>' +
                        '</tr>';
                });
                $('#top-events').html(eventsHtml);
            } else {
                alert(response.data.message);
            }
        });
    }
    
    // Eventos
    $('#apwp-date-range').on('change', function() {
        if ($(this).val() === 'custom') {
            $('#apwp-custom-date-range').show();
        } else {
            $('#apwp-custom-date-range').hide();
            loadReports();
        }
    });
    
    $('#apwp-apply-date-range').on('click', function() {
        loadReports();
    });
    
    // Inicialização
    initCharts();
    loadReports();
});
</script>

<style>
.apwp-summary-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.apwp-card {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    padding: 20px;
}

.apwp-card h3 {
    margin: 0 0 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
}

.apwp-card-content {
    display: flex;
    justify-content: space-between;
}

.apwp-stat {
    text-align: center;
}

.apwp-stat-value {
    display: block;
    font-size: 24px;
    font-weight: bold;
    color: #2271b1;
}

.apwp-stat-label {
    font-size: 13px;
    color: #646970;
}

.apwp-charts {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.apwp-chart-card {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    padding: 20px;
}

.apwp-chart-card h3 {
    margin: 0 0 15px;
}

.apwp-chart-card canvas {
    height: 300px;
}

.apwp-top-tables {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.apwp-table-card {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    padding: 20px;
}

.apwp-table-card h3 {
    margin: 0 0 15px;
}

.apwp-reports-filters {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    padding: 15px;
    margin: 20px 0;
}

#apwp-custom-date-range {
    margin-top: 10px;
}

#apwp-custom-date-range input[type="date"] {
    margin-right: 10px;
}
</style>
