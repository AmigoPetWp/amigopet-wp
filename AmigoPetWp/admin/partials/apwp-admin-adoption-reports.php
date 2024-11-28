<?php
/**
 * Template para a página de relatórios de adoções
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

// Inicializa as classes necessárias
$adoption = new APWP_Adoption();
$pet = new APWP_Pet();
$adopter = new APWP_Adopter();

// Obtém os filtros
$status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : 'all';
$date_start = isset($_GET['date_start']) ? sanitize_text_field($_GET['date_start']) : '';
$date_end = isset($_GET['date_end']) ? sanitize_text_field($_GET['date_end']) : '';
$pet_id = isset($_GET['pet_id']) ? intval($_GET['pet_id']) : 0;
$adopter_id = isset($_GET['adopter_id']) ? intval($_GET['adopter_id']) : 0;

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

if ($pet_id) {
    $args['pet_id'] = $pet_id;
}

if ($adopter_id) {
    $args['adopter_id'] = $adopter_id;
}

// Obtém os dados das adoções
$adoptions = $adoption->list($args);

// Prepara as estatísticas
$total_adoptions = count($adoptions);
$pending_adoptions = 0;
$approved_adoptions = 0;
$rejected_adoptions = 0;

foreach ($adoptions as $a) {
    switch ($a['status']) {
        case 'pending':
            $pending_adoptions++;
            break;
        case 'approved':
            $approved_adoptions++;
            break;
        case 'rejected':
            $rejected_adoptions++;
            break;
    }
}

// Lista de pets para o filtro
$pets = $pet->list(array('limit' => -1));
$adopters = $adopter->list(array('limit' => -1));
?>

<div class="wrap">
    <h1><?php _e('Relatórios de Adoções', 'amigopet-wp'); ?></h1>

    <!-- Filtros -->
    <div class="apwp-filters">
        <form method="get" action="">
            <input type="hidden" name="page" value="amigopet-wp-adoption-reports">
            
            <div class="filter-row">
                <select name="status" class="filter-item">
                    <option value="all" <?php selected($status, 'all'); ?>><?php _e('Todos os Status', 'amigopet-wp'); ?></option>
                    <option value="pending" <?php selected($status, 'pending'); ?>><?php _e('Pendentes', 'amigopet-wp'); ?></option>
                    <option value="approved" <?php selected($status, 'approved'); ?>><?php _e('Aprovadas', 'amigopet-wp'); ?></option>
                    <option value="rejected" <?php selected($status, 'rejected'); ?>><?php _e('Rejeitadas', 'amigopet-wp'); ?></option>
                </select>

                <select name="pet_id" class="filter-item">
                    <option value=""><?php _e('Todos os Pets', 'amigopet-wp'); ?></option>
                    <?php foreach ($pets as $p) : ?>
                        <option value="<?php echo esc_attr($p['id']); ?>" <?php selected($pet_id, $p['id']); ?>>
                            <?php echo esc_html($p['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select name="adopter_id" class="filter-item">
                    <option value=""><?php _e('Todos os Adotantes', 'amigopet-wp'); ?></option>
                    <?php foreach ($adopters as $a) : ?>
                        <option value="<?php echo esc_attr($a['id']); ?>" <?php selected($adopter_id, $a['id']); ?>>
                            <?php echo esc_html($a['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-row">
                <input type="date" name="date_start" value="<?php echo esc_attr($date_start); ?>" class="filter-item" placeholder="<?php _e('Data Inicial', 'amigopet-wp'); ?>">
                <input type="date" name="date_end" value="<?php echo esc_attr($date_end); ?>" class="filter-item" placeholder="<?php _e('Data Final', 'amigopet-wp'); ?>">
                
                <div class="filter-buttons">
                    <input type="submit" class="button" value="<?php _e('Filtrar', 'amigopet-wp'); ?>">
                    <a href="<?php echo admin_url('admin.php?page=amigopet-wp-adoption-reports'); ?>" class="button"><?php _e('Limpar', 'amigopet-wp'); ?></a>
                    <button type="button" class="button button-primary" onclick="exportToCsv()"><?php _e('Exportar CSV', 'amigopet-wp'); ?></button>
                </div>
            </div>
        </form>
    </div>

    <!-- Cards de Estatísticas -->
    <div class="apwp-stats-cards">
        <div class="apwp-stat-card">
            <h3><?php _e('Total de Adoções', 'amigopet-wp'); ?></h3>
            <span class="stat-number"><?php echo $total_adoptions; ?></span>
        </div>
        <div class="apwp-stat-card status-pending">
            <h3><?php _e('Adoções Pendentes', 'amigopet-wp'); ?></h3>
            <span class="stat-number"><?php echo $pending_adoptions; ?></span>
        </div>
        <div class="apwp-stat-card status-approved">
            <h3><?php _e('Adoções Aprovadas', 'amigopet-wp'); ?></h3>
            <span class="stat-number"><?php echo $approved_adoptions; ?></span>
        </div>
        <div class="apwp-stat-card status-rejected">
            <h3><?php _e('Adoções Rejeitadas', 'amigopet-wp'); ?></h3>
            <span class="stat-number"><?php echo $rejected_adoptions; ?></span>
        </div>
    </div>

    <!-- Tabela de Dados -->
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php _e('ID', 'amigopet-wp'); ?></th>
                <th><?php _e('Pet', 'amigopet-wp'); ?></th>
                <th><?php _e('Adotante', 'amigopet-wp'); ?></th>
                <th><?php _e('Status', 'amigopet-wp'); ?></th>
                <th><?php _e('Data da Adoção', 'amigopet-wp'); ?></th>
                <th><?php _e('Notas', 'amigopet-wp'); ?></th>
                <th><?php _e('Última Atualização', 'amigopet-wp'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($adoptions)) : ?>
                <?php foreach ($adoptions as $adoption) : 
                    $pet_data = $pet->get($adoption['pet_id']);
                    $adopter_data = $adopter->get($adoption['adopter_id']);
                    ?>
                    <tr>
                        <td><?php echo esc_html($adoption['id']); ?></td>
                        <td><?php echo $pet_data ? esc_html($pet_data['name']) : __('Pet não encontrado', 'amigopet-wp'); ?></td>
                        <td><?php echo $adopter_data ? esc_html($adopter_data['name']) : __('Adotante não encontrado', 'amigopet-wp'); ?></td>
                        <td>
                            <span class="status-badge status-<?php echo esc_attr($adoption['status']); ?>">
                                <?php
                                $status_labels = array(
                                    'pending' => __('Pendente', 'amigopet-wp'),
                                    'approved' => __('Aprovada', 'amigopet-wp'),
                                    'rejected' => __('Rejeitada', 'amigopet-wp')
                                );
                                echo isset($status_labels[$adoption['status']]) ? $status_labels[$adoption['status']] : $adoption['status'];
                                ?>
                            </span>
                        </td>
                        <td><?php echo !empty($adoption['adoption_date']) ? date_i18n(get_option('date_format'), strtotime($adoption['adoption_date'])) : '-'; ?></td>
                        <td><?php echo esc_html($adoption['notes']); ?></td>
                        <td><?php echo date_i18n(get_option('date_format'), strtotime($adoption['updated_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="7"><?php _e('Nenhuma adoção encontrada.', 'amigopet-wp'); ?></td>
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
            let text = td.textContent.trim();
            // Remove status badge class names
            text = text.replace(/status-\w+/, '');
            row.push('"' + text.replace(/"/g, '""') + '"');
        });
        csv.push(row.join(','));
    });
    
    // Download
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', 'relatorio-adocoes.csv');
    link.style.visibility = 'hidden';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>
