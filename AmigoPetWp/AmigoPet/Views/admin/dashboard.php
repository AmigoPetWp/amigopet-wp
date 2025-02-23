<?php
/**
 * View do dashboard do admin
 */
?>
<div class="wrap amigopet-dashboard">
    <div class="welcome-panel">
        <div class="welcome-panel-content">
            <!-- Logo -->
            <div class="welcome-panel-header">
                <img src="<?php echo esc_url($logo_url); ?>" alt="AmigoPet WP Logo" class="amigopet-logo">
                <h2><?php echo esc_html($welcome['title']); ?></h2>
                <p class="about-description"><?php echo esc_html($welcome['subtitle']); ?></p>
            </div>

            <!-- Mensagens -->
            <div class="welcome-panel-cards">
                <?php foreach ($welcome['message'] as $line): ?>
                    <div class="message-card">
                        <p><?php echo esc_html($line); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Estatísticas -->
    <div class="dashboard-stats">
        <div class="stat-box">
            <span class="dashicons dashicons-pets"></span>
            <h3 id="pets-available">0</h3>
            <p>Pets Disponíveis</p>
        </div>

        <div class="stat-box">
            <span class="dashicons dashicons-heart"></span>
            <h3 id="adoptions-total">0</h3>
            <p>Adoções Realizadas</p>
        </div>

        <div class="stat-box">
            <span class="dashicons dashicons-calendar"></span>
            <h3 id="events-total">0</h3>
            <p>Eventos</p>
        </div>
        <div class="stat-box">
            <span class="dashicons dashicons-groups"></span>
            <h3 id="volunteers-total">0</h3>
            <p>Voluntários</p>
        </div>
    </div>
</div>

<style>
.amigopet-dashboard .welcome-panel {
    padding: 1.5em;
    margin-top: 15px;
    border-radius: 10px;
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
}

.amigopet-dashboard .welcome-panel-header {
    text-align: center;
    margin-bottom: 1em;
}

.amigopet-dashboard .amigopet-logo {
    max-width: 180px;
    height: auto;
    margin-bottom: 0.5em;
}

.amigopet-dashboard .welcome-panel-header h2 {
    font-size: 2em;
    color: #2c3338;
    margin: 0.3em 0;
}

.amigopet-dashboard .about-description {
    font-size: 1.2em;
    color: #646970;
    margin: 0.5em 0;
}

.amigopet-dashboard .welcome-panel-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin: 2em 0;
}

.amigopet-dashboard .message-card {
    background: white;
    padding: 1.5em;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.amigopet-dashboard .message-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.amigopet-dashboard .message-card p {
    font-size: 1.1em;
    line-height: 1.6;
    color: #50575e;
    margin: 0;
    text-align: center;
}

.amigopet-dashboard .dashboard-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-top: 2em;
}

.amigopet-dashboard .stat-box {
    background: white;
    padding: 1.5em;
    border-radius: 8px;
    text-align: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    transition: transform 0.2s;
}

.amigopet-dashboard .stat-box:hover {
    transform: translateY(-5px);
}

.amigopet-dashboard .stat-box .dashicons {
    font-size: 2.5em;
    width: auto;
    height: auto;
    color: #2271b1;
}

.amigopet-dashboard .stat-box h3 {
    font-size: 2em;
    margin: 0.5em 0;
    color: #1d2327;
}

.amigopet-dashboard .stat-box p {
    color: #646970;
    margin: 0;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Atualiza os dados das estatísticas
    $.ajax({
        url: ajaxurl,
        type: 'GET',
        data: {
            action: 'apwp_get_dashboard_data',
            nonce: apwp.nonce
        },
        success: function(response) {
            if (response.success) {
                const data = response.data;
                $('#pets-available').text(data.pets.available);
                $('#adoptions-total').text(data.adoptions.total);
                $('#events-total').text(data.events.total);
                $('#volunteers-total').text(data.volunteers?.total || 0);
            }
        }
    });
});
</script>
