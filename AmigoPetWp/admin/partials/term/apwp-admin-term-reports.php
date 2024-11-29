<?php
/**
 * Template para relatórios de termos
 */

// Verifica permissões
if (!current_user_can('manage_options')) {
    wp_die(__('Você não tem permissão para acessar esta página.', 'amigopet-wp'));
}

// Obtém os filtros
$start_date = isset($_GET['start_date']) ? sanitize_text_field($_GET['start_date']) : date('Y-m-d', strtotime('-30 days'));
$end_date = isset($_GET['end_date']) ? sanitize_text_field($_GET['end_date']) : date('Y-m-d');
$type_id = isset($_GET['type_id']) ? intval($_GET['type_id']) : 0;
$status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';

// Busca dados para o relatório
global $wpdb;

$where = array("1=1");
$where_values = array();

if ($start_date) {
    $where[] = "created_at >= %s";
    $where_values[] = $start_date . ' 00:00:00';
}

if ($end_date) {
    $where[] = "created_at <= %s";
    $where_values[] = $end_date . ' 23:59:59';
}

if ($type_id) {
    $where[] = "type_id = %d";
    $where_values[] = $type_id;
}

if ($status) {
    $where[] = "status = %s";
    $where_values[] = $status;
}

$where_clause = implode(" AND ", $where);

$query = $wpdb->prepare(
    "SELECT t.*, tt.name as type_name, COUNT(ts.id) as signatures
    FROM {$wpdb->prefix}apwp_terms t
    LEFT JOIN {$wpdb->prefix}apwp_term_types tt ON t.type_id = tt.id
    LEFT JOIN {$wpdb->prefix}apwp_term_signatures ts ON t.id = ts.term_id
    WHERE {$where_clause}
    GROUP BY t.id
    ORDER BY t.created_at DESC",
    $where_values
);

$terms = $wpdb->get_results($query);

// Busca tipos de termos para o filtro
$term_types = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}apwp_term_types WHERE status = 'active' ORDER BY name");
?>

<div class="wrap">
    <h1><?php _e('Relatórios de Termos', 'amigopet-wp'); ?></h1>

    <!-- Filtros -->
    <form method="get" action="">
        <input type="hidden" name="page" value="<?php echo esc_attr($_GET['page']); ?>">
        <div class="tablenav top">
            <div class="alignleft actions">
                <input type="date" name="start_date" value="<?php echo esc_attr($start_date); ?>">
                <input type="date" name="end_date" value="<?php echo esc_attr($end_date); ?>">
                
                <select name="type_id">
                    <option value=""><?php _e('Todos os tipos', 'amigopet-wp'); ?></option>
                    <?php foreach ($term_types as $type): ?>
                        <option value="<?php echo esc_attr($type->id); ?>" <?php selected($type_id, $type->id); ?>>
                            <?php echo esc_html($type->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select name="status">
                    <option value=""><?php _e('Todos os status', 'amigopet-wp'); ?></option>
                    <option value="active" <?php selected($status, 'active'); ?>><?php _e('Ativo', 'amigopet-wp'); ?></option>
                    <option value="inactive" <?php selected($status, 'inactive'); ?>><?php _e('Inativo', 'amigopet-wp'); ?></option>
                </select>

                <?php submit_button(__('Filtrar', 'amigopet-wp'), 'action', 'filter', false); ?>
                <a href="#" class="button" id="export-csv"><?php _e('Exportar CSV', 'amigopet-wp'); ?></a>
            </div>
        </div>
    </form>

    <!-- Resumo -->
    <div class="apwp-report-summary">
        <div class="apwp-report-card">
            <h3><?php _e('Total de Termos', 'amigopet-wp'); ?></h3>
            <p class="number"><?php echo count($terms); ?></p>
        </div>
        <div class="apwp-report-card">
            <h3><?php _e('Total de Assinaturas', 'amigopet-wp'); ?></h3>
            <p class="number"><?php echo array_sum(array_column($terms, 'signatures')); ?></p>
        </div>
        <div class="apwp-report-card">
            <h3><?php _e('Média de Assinaturas por Termo', 'amigopet-wp'); ?></h3>
            <p class="number"><?php echo count($terms) ? round(array_sum(array_column($terms, 'signatures')) / count($terms), 1) : 0; ?></p>
        </div>
    </div>

    <!-- Tabela de Resultados -->
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php _e('ID', 'amigopet-wp'); ?></th>
                <th><?php _e('Título', 'amigopet-wp'); ?></th>
                <th><?php _e('Tipo', 'amigopet-wp'); ?></th>
                <th><?php _e('Assinaturas', 'amigopet-wp'); ?></th>
                <th><?php _e('Status', 'amigopet-wp'); ?></th>
                <th><?php _e('Data de Criação', 'amigopet-wp'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($terms as $term): ?>
                <tr>
                    <td><?php echo esc_html($term->id); ?></td>
                    <td>
                        <a href="<?php echo admin_url('admin.php?page=amigopet-wp-terms&action=edit&id=' . $term->id); ?>">
                            <?php echo esc_html($term->title); ?>
                        </a>
                    </td>
                    <td><?php echo esc_html($term->type_name); ?></td>
                    <td><?php echo esc_html($term->signatures); ?></td>
                    <td><?php echo esc_html(ucfirst($term->status)); ?></td>
                    <td><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($term->created_at))); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<style>
.apwp-report-summary {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.apwp-report-card {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    padding: 20px;
    text-align: center;
}

.apwp-report-card h3 {
    margin: 0 0 10px 0;
    color: #23282d;
}

.apwp-report-card .number {
    font-size: 24px;
    font-weight: bold;
    margin: 0;
    color: #0073aa;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Exportar para CSV
    $('#export-csv').click(function(e) {
        e.preventDefault();
        
        var rows = [['ID', 'Título', 'Tipo', 'Assinaturas', 'Status', 'Data de Criação']];
        
        $('.wp-list-table tbody tr').each(function() {
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
        link.setAttribute("download", "termos-relatorio.csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });
});
</script>
