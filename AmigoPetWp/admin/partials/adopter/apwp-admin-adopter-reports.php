<?php
/**
 * Template para a página de relatórios de adotantes
 *
 * @package    AmigoPet_Wp
 * @subpackage AmigoPet_Wp/admin/partials
 */

// Se este arquivo for chamado diretamente, aborte.
if (!defined('WPINC')) {
    die;
}

// Verifica permissões
if (!current_user_can('manage_options')) {
    wp_die(__('Você não tem permissão para acessar esta página.', 'amigopet-wp'));
}

// Inicializa a classe de adotantes
$adopter = new APWP_Adopter();

// Obtém os filtros
$status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : 'all';
$date_start = isset($_GET['date_start']) ? sanitize_text_field($_GET['date_start']) : '';
$date_end = isset($_GET['date_end']) ? sanitize_text_field($_GET['date_end']) : '';

// Prepara os argumentos para a consulta
$args = array(
    'limit' => -1 // Sem limite para relatórios
);

if ($status !== 'all') {
    $args['status'] = $status;
}

if ($date_start && $date_end) {
    $args['date_range'] = array(
        'start' => $date_start,
        'end' => $date_end
    );
}

// Obtém os dados dos adotantes
$adopters = $adopter->list($args);

// Prepara as estatísticas
$total_adopters = count($adopters);
$active_adopters = 0;
$inactive_adopters = 0;
$pending_adopters = 0;

foreach ($adopters as $a) {
    switch ($a['status']) {
        case 'active':
            $active_adopters++;
            break;
        case 'inactive':
            $inactive_adopters++;
            break;
        case 'pending':
            $pending_adopters++;
            break;
    }
}
?>

<div class="wrap">
    <h1><?php _e('Relatórios de Adotantes', 'amigopet-wp'); ?></h1>

    <!-- Filtros -->
    <div class="tablenav top">
        <form method="get" action="">
            <input type="hidden" name="page" value="amigopet-wp-adopter-reports">
            
            <select name="status">
                <option value="all" <?php selected($status, 'all'); ?>><?php _e('Todos os Status', 'amigopet-wp'); ?></option>
                <option value="active" <?php selected($status, 'active'); ?>><?php _e('Ativos', 'amigopet-wp'); ?></option>
                <option value="inactive" <?php selected($status, 'inactive'); ?>><?php _e('Inativos', 'amigopet-wp'); ?></option>
                <option value="pending" <?php selected($status, 'pending'); ?>><?php _e('Pendentes', 'amigopet-wp'); ?></option>
            </select>

            <input type="date" name="date_start" value="<?php echo esc_attr($date_start); ?>" placeholder="<?php _e('Data Inicial', 'amigopet-wp'); ?>">
            <input type="date" name="date_end" value="<?php echo esc_attr($date_end); ?>" placeholder="<?php _e('Data Final', 'amigopet-wp'); ?>">

            <input type="submit" class="button" value="<?php _e('Filtrar', 'amigopet-wp'); ?>">
            <a href="<?php echo admin_url('admin.php?page=amigopet-wp-adopter-reports'); ?>" class="button"><?php _e('Limpar', 'amigopet-wp'); ?></a>
            
            <button type="button" class="button" onclick="exportToCsv()"><?php _e('Exportar CSV', 'amigopet-wp'); ?></button>
        </form>
    </div>

    <!-- Cards de Estatísticas -->
    <div class="apwp-stats-cards">
        <div class="apwp-stat-card">
            <h3><?php _e('Total de Adotantes', 'amigopet-wp'); ?></h3>
            <span class="stat-number"><?php echo $total_adopters; ?></span>
        </div>
        <div class="apwp-stat-card">
            <h3><?php _e('Adotantes Ativos', 'amigopet-wp'); ?></h3>
            <span class="stat-number"><?php echo $active_adopters; ?></span>
        </div>
        <div class="apwp-stat-card">
            <h3><?php _e('Adotantes Inativos', 'amigopet-wp'); ?></h3>
            <span class="stat-number"><?php echo $inactive_adopters; ?></span>
        </div>
        <div class="apwp-stat-card">
            <h3><?php _e('Adotantes Pendentes', 'amigopet-wp'); ?></h3>
            <span class="stat-number"><?php echo $pending_adopters; ?></span>
        </div>
    </div>

    <!-- Tabela de Dados -->
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php _e('Nome', 'amigopet-wp'); ?></th>
                <th><?php _e('Email', 'amigopet-wp'); ?></th>
                <th><?php _e('Telefone', 'amigopet-wp'); ?></th>
                <th><?php _e('Status', 'amigopet-wp'); ?></th>
                <th><?php _e('Data de Cadastro', 'amigopet-wp'); ?></th>
                <th><?php _e('Última Atualização', 'amigopet-wp'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($adopters)) : ?>
                <?php foreach ($adopters as $adopter) : ?>
                    <tr>
                        <td><?php echo esc_html($adopter['name']); ?></td>
                        <td><?php echo esc_html($adopter['email']); ?></td>
                        <td><?php echo esc_html($adopter['phone']); ?></td>
                        <td>
                            <?php
                            $status_labels = array(
                                'active' => __('Ativo', 'amigopet-wp'),
                                'inactive' => __('Inativo', 'amigopet-wp'),
                                'pending' => __('Pendente', 'amigopet-wp')
                            );
                            echo isset($status_labels[$adopter['status']]) ? $status_labels[$adopter['status']] : $adopter['status'];
                            ?>
                        </td>
                        <td><?php echo date_i18n(get_option('date_format'), strtotime($adopter['created_at'])); ?></td>
                        <td><?php echo date_i18n(get_option('date_format'), strtotime($adopter['updated_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="6"><?php _e('Nenhum adotante encontrado.', 'amigopet-wp'); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
function exportToCsv() {
    const table = document.querySelector('table');
    let csv = [];
    
    // Header
    const header = [];
    table.querySelectorAll('thead th').forEach(th => {
        header.push(th.textContent.trim());
    });
    csv.push(header.join(','));
    
    // Rows
    table.querySelectorAll('tbody tr').forEach(tr => {
        const row = [];
        tr.querySelectorAll('td').forEach(td => {
            row.push('"' + td.textContent.trim().replace(/"/g, '""') + '"');
        });
        csv.push(row.join(','));
    });
    
    // Download
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', 'relatorio-adotantes.csv');
    link.style.visibility = 'hidden';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>
