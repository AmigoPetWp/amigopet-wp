<?php
/**
 * View do dashboard do admin
 */
?>
<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <div class="apwp-dashboard">
        <!-- Cards de estatísticas -->
        <div class="apwp-stats">
            <div class="apwp-stat-card">
                <h3><?php _e('Pets', 'amigopet-wp'); ?></h3>
                <div class="apwp-stat-numbers">
                    <div>
                        <span class="apwp-stat-label"><?php _e('Total', 'amigopet-wp'); ?></span>
                        <span class="apwp-stat-value" id="pets-total">0</span>
                    </div>
                    <div>
                        <span class="apwp-stat-label"><?php _e('Disponíveis', 'amigopet-wp'); ?></span>
                        <span class="apwp-stat-value" id="pets-available">0</span>
                    </div>
                    <div>
                        <span class="apwp-stat-label"><?php _e('Adotados', 'amigopet-wp'); ?></span>
                        <span class="apwp-stat-value" id="pets-adopted">0</span>
                    </div>
                </div>
            </div>

            <div class="apwp-stat-card">
                <h3><?php _e('Adoções', 'amigopet-wp'); ?></h3>
                <div class="apwp-stat-numbers">
                    <div>
                        <span class="apwp-stat-label"><?php _e('Total', 'amigopet-wp'); ?></span>
                        <span class="apwp-stat-value" id="adoptions-total">0</span>
                    </div>
                    <div>
                        <span class="apwp-stat-label"><?php _e('Pendentes', 'amigopet-wp'); ?></span>
                        <span class="apwp-stat-value" id="adoptions-pending">0</span>
                    </div>
                    <div>
                        <span class="apwp-stat-label"><?php _e('Concluídas', 'amigopet-wp'); ?></span>
                        <span class="apwp-stat-value" id="adoptions-completed">0</span>
                    </div>
                </div>
            </div>

            <div class="apwp-stat-card">
                <h3><?php _e('Eventos', 'amigopet-wp'); ?></h3>
                <div class="apwp-stat-numbers">
                    <div>
                        <span class="apwp-stat-label"><?php _e('Total', 'amigopet-wp'); ?></span>
                        <span class="apwp-stat-value" id="events-total">0</span>
                    </div>
                    <div>
                        <span class="apwp-stat-label"><?php _e('Próximos', 'amigopet-wp'); ?></span>
                        <span class="apwp-stat-value" id="events-upcoming">0</span>
                    </div>
                </div>
            </div>

            <div class="apwp-stat-card">
                <h3><?php _e('Doações', 'amigopet-wp'); ?></h3>
                <div class="apwp-stat-numbers">
                    <div>
                        <span class="apwp-stat-label"><?php _e('Total', 'amigopet-wp'); ?></span>
                        <span class="apwp-stat-value" id="donations-total">0</span>
                    </div>
                    <div>
                        <span class="apwp-stat-label"><?php _e('Valor Total', 'amigopet-wp'); ?></span>
                        <span class="apwp-stat-value" id="donations-amount">R$ 0,00</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="apwp-charts">
            <div class="apwp-chart-card">
                <h3><?php _e('Adoções por Mês', 'amigopet-wp'); ?></h3>
                <canvas id="adoptions-chart"></canvas>
            </div>

            <div class="apwp-chart-card">
                <h3><?php _e('Doações por Mês', 'amigopet-wp'); ?></h3>
                <canvas id="donations-chart"></canvas>
            </div>
        </div>

        <!-- Últimas atividades -->
        <div class="apwp-recent-activities">
            <h3><?php _e('Últimas Atividades', 'amigopet-wp'); ?></h3>
            <div id="recent-activities"></div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Carrega os dados do dashboard
    $.ajax({
        url: ajaxurl,
        type: 'GET',
        data: {
            action: 'apwp_get_dashboard_data',
            _ajax_nonce: apwp.nonce
        },
        success: function(response) {
            if (response.success) {
                const data = response.data;
                
                // Atualiza os cards de estatísticas
                $('#pets-total').text(data.pets.total);
                $('#pets-available').text(data.pets.available);
                $('#pets-adopted').text(data.pets.adopted);
                
                $('#adoptions-total').text(data.adoptions.total);
                $('#adoptions-pending').text(data.adoptions.pending);
                $('#adoptions-completed').text(data.adoptions.completed);
                
                $('#events-total').text(data.events.total);
                $('#events-upcoming').text(data.events.upcoming);
                
                $('#donations-total').text(data.donations.total);
                $('#donations-amount').text(
                    new Intl.NumberFormat('pt-BR', {
                        style: 'currency',
                        currency: 'BRL'
                    }).format(data.donations.amount)
                );
            }
        }
    });
});
</script>
