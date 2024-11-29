<?php
/**
 * Template para relatórios de organizações
 */

// Verifica permissões
if (!current_user_can('manage_options')) {
    wp_die(__('Você não tem permissão para acessar esta página.', 'amigopet-wp'));
}

// Obtém os filtros
$start_date = isset($_GET['start_date']) ? sanitize_text_field($_GET['start_date']) : date('Y-m-d', strtotime('-30 days'));
$end_date = isset($_GET['end_date']) ? sanitize_text_field($_GET['end_date']) : date('Y-m-d');
$state = isset($_GET['state']) ? sanitize_text_field($_GET['state']) : '';
$status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';

// Busca dados para o relatório
global $wpdb;

$where = array("1=1");
$where_values = array();

if ($start_date) {
    $where[] = "o.created_at >= %s";
    $where_values[] = $start_date . ' 00:00:00';
}

if ($end_date) {
    $where[] = "o.created_at <= %s";
    $where_values[] = $end_date . ' 23:59:59';
}

if ($state) {
    $where[] = "o.state = %s";
    $where_values[] = $state;
}

if ($status) {
    $where[] = "o.status = %s";
    $where_values[] = $status;
}

$where_clause = implode(" AND ", $where);

$query = $wpdb->prepare(
    "SELECT o.*, 
            COUNT(DISTINCT p.id) as total_pets,
            COUNT(DISTINCT a.id) as total_adoptions
     FROM {$wpdb->prefix}apwp_organizations o
     LEFT JOIN {$wpdb->prefix}apwp_pets p ON o.id = p.organization_id
     LEFT JOIN {$wpdb->prefix}apwp_adoptions a ON o.id = a.organization_id
     WHERE {$where_clause}
     GROUP BY o.id
     ORDER BY o.created_at DESC",
    $where_values
);

$organizations = $wpdb->get_results($query);

// Lista de estados brasileiros para o filtro
$states = array(
    'AC' => 'Acre', 'AL' => 'Alagoas', 'AP' => 'Amapá',
    'AM' => 'Amazonas', 'BA' => 'Bahia', 'CE' => 'Ceará',
    'DF' => 'Distrito Federal', 'ES' => 'Espírito Santo',
    'GO' => 'Goiás', 'MA' => 'Maranhão', 'MT' => 'Mato Grosso',
    'MS' => 'Mato Grosso do Sul', 'MG' => 'Minas Gerais',
    'PA' => 'Pará', 'PB' => 'Paraíba', 'PR' => 'Paraná',
    'PE' => 'Pernambuco', 'PI' => 'Piauí', 'RJ' => 'Rio de Janeiro',
    'RN' => 'Rio Grande do Norte', 'RS' => 'Rio Grande do Sul',
    'RO' => 'Rondônia', 'RR' => 'Roraima', 'SC' => 'Santa Catarina',
    'SP' => 'São Paulo', 'SE' => 'Sergipe', 'TO' => 'Tocantins'
);
?>

<div class="apwp-wrap">
    <div class="apwp-header">
        <h1><?php _e('Relatórios de Organizações', 'amigopet-wp'); ?></h1>
    </div>

    <!-- Filtros -->
    <div class="apwp-content">
        <form method="get" action="" class="apwp-form">
            <input type="hidden" name="page" value="<?php echo esc_attr($_GET['page']); ?>">
            <div class="apwp-form-section">
                <div class="apwp-form-field apwp-form-inline">
                    <div class="apwp-form-group">
                        <label for="start_date"><?php _e('Data Inicial', 'amigopet-wp'); ?></label>
                        <input type="date" id="start_date" name="start_date" value="<?php echo esc_attr($start_date); ?>">
                    </div>
                    
                    <div class="apwp-form-group">
                        <label for="end_date"><?php _e('Data Final', 'amigopet-wp'); ?></label>
                        <input type="date" id="end_date" name="end_date" value="<?php echo esc_attr($end_date); ?>">
                    </div>
                    
                    <div class="apwp-form-group">
                        <label for="state"><?php _e('Estado', 'amigopet-wp'); ?></label>
                        <select id="state" name="state">
                            <option value=""><?php _e('Todos os estados', 'amigopet-wp'); ?></option>
                            <?php foreach ($states as $uf => $name): ?>
                                <option value="<?php echo esc_attr($uf); ?>" <?php selected($state, $uf); ?>>
                                    <?php echo esc_html($name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="apwp-form-group">
                        <label for="status"><?php _e('Status', 'amigopet-wp'); ?></label>
                        <select id="status" name="status">
                            <option value=""><?php _e('Todos os status', 'amigopet-wp'); ?></option>
                            <option value="active" <?php selected($status, 'active'); ?>><?php _e('Ativo', 'amigopet-wp'); ?></option>
                            <option value="inactive" <?php selected($status, 'inactive'); ?>><?php _e('Inativo', 'amigopet-wp'); ?></option>
                        </select>
                    </div>

                    <div class="apwp-form-actions">
                        <?php submit_button(__('Filtrar', 'amigopet-wp'), 'secondary', 'filter', false); ?>
                        <a href="#" class="button" id="export-csv"><?php _e('Exportar CSV', 'amigopet-wp'); ?></a>
                    </div>
                </div>
            </div>
        </form>

        <!-- Resumo -->
        <div class="apwp-stats-cards">
            <div class="apwp-stat-card">
                <h3><?php _e('Total de Organizações', 'amigopet-wp'); ?></h3>
                <p class="stat-number"><?php echo count($organizations); ?></p>
            </div>
            <div class="apwp-stat-card">
                <h3><?php _e('Total de Pets', 'amigopet-wp'); ?></h3>
                <p class="stat-number"><?php echo array_sum(array_column($organizations, 'total_pets')); ?></p>
            </div>
            <div class="apwp-stat-card">
                <h3><?php _e('Total de Adoções', 'amigopet-wp'); ?></h3>
                <p class="stat-number"><?php echo array_sum(array_column($organizations, 'total_adoptions')); ?></p>
            </div>
            <div class="apwp-stat-card">
                <h3><?php _e('Média de Pets por Organização', 'amigopet-wp'); ?></h3>
                <p class="stat-number"><?php echo count($organizations) ? round(array_sum(array_column($organizations, 'total_pets')) / count($organizations), 1) : 0; ?></p>
            </div>
        </div>

        <!-- Tabela de Resultados -->
        <div class="apwp-table-wrapper">
            <table class="apwp-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('ID', 'amigopet-wp'); ?></th>
                        <th><?php _e('Nome', 'amigopet-wp'); ?></th>
                        <th><?php _e('Cidade/Estado', 'amigopet-wp'); ?></th>
                        <th><?php _e('Total de Pets', 'amigopet-wp'); ?></th>
                        <th><?php _e('Total de Adoções', 'amigopet-wp'); ?></th>
                        <th><?php _e('Status', 'amigopet-wp'); ?></th>
                        <th><?php _e('Data de Cadastro', 'amigopet-wp'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($organizations as $org): ?>
                        <tr>
                            <td><?php echo esc_html($org->id); ?></td>
                            <td>
                                <a href="<?php echo admin_url('admin.php?page=amigopet-wp-organizations&action=edit&id=' . $org->id); ?>">
                                    <?php echo esc_html($org->name); ?>
                                </a>
                            </td>
                            <td><?php echo esc_html($org->city . '/' . $org->state); ?></td>
                            <td><?php echo esc_html($org->total_pets); ?></td>
                            <td><?php echo esc_html($org->total_adoptions); ?></td>
                            <td><span class="status-<?php echo esc_attr($org->status); ?>"><?php echo esc_html(ucfirst($org->status)); ?></span></td>
                            <td><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($org->created_at))); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Exportar para CSV
    $('#export-csv').click(function(e) {
        e.preventDefault();
        
        var rows = [['ID', 'Nome', 'Cidade/Estado', 'Total de Pets', 'Total de Adoções', 'Status', 'Data de Cadastro']];
        
        $('.apwp-table tbody tr').each(function() {
            var row = [];
            $(this).find('td').each(function() {
                row.push($(this).text().trim());
            });
            rows.push(row);
        });
        
        var csvContent = "data:text/csv;charset=utf-8,";
        rows.forEach(function(rowArray) {
            var row = rowArray.join(",");
            csvContent += row + "\r\n";
        });
        
        var encodedUri = encodeURI(csvContent);
        var link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "organizacoes-relatorio.csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });
});
</script>
