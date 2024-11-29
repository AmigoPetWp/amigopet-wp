<?php
/**
 * Template para a página de termos assinados
 */

// Se este arquivo for chamado diretamente, aborte.
if (!defined('WPINC')) {
    die;
}

// Verifica permissões
if (!current_user_can('manage_options')) {
    wp_die(__('Você não tem permissão para acessar esta página.', 'amigopet-wp'));
}

// Inicializa as classes
$signed_term = new APWP_Signed_Term();
$term_type = new APWP_Term_Type();

// Obtém os parâmetros de filtro
$type_id = isset($_GET['type']) ? intval($_GET['type']) : 0;
$search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
$page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
$per_page = 20;

// Lista os termos assinados
$terms = $signed_term->list(array(
    'type_id' => $type_id,
    'search' => $search,
    'per_page' => $per_page,
    'offset' => ($page - 1) * $per_page
));

// Obtém as estatísticas
$stats = APWP_Signed_Term::get_stats($type_id);

// Lista os tipos de termos para o filtro
$types = $term_type->list();
?>

<div class="wrap">
    <h1>
        <?php _e('Termos Assinados', 'amigopet-wp'); ?>
        <a href="<?php echo admin_url('admin.php?page=amigopet-wp-term-types'); ?>" class="page-title-action">
            <?php _e('Gerenciar Tipos', 'amigopet-wp'); ?>
        </a>
    </h1>
    
    <!-- Estatísticas -->
    <div class="apwp-stats-cards">
        <div class="stats-card">
            <h3><?php _e('Total de Assinaturas', 'amigopet-wp'); ?></h3>
            <div class="stats-number">
                <?php
                $total = 0;
                foreach ($stats as $stat) {
                    $total += $stat['total'];
                }
                echo number_format_i18n($total);
                ?>
            </div>
        </div>
        
        <div class="stats-card">
            <h3><?php _e('Assinaturas Ativas', 'amigopet-wp'); ?></h3>
            <div class="stats-number">
                <?php
                $active = 0;
                foreach ($stats as $stat) {
                    $active += $stat['active'];
                }
                echo number_format_i18n($active);
                ?>
            </div>
        </div>
        
        <div class="stats-card">
            <h3><?php _e('Últimos 30 Dias', 'amigopet-wp'); ?></h3>
            <div class="stats-number">
                <?php
                $last_30_days = 0;
                foreach ($stats as $stat) {
                    if (strtotime($stat['date']) >= strtotime('-30 days')) {
                        $last_30_days += $stat['total'];
                    }
                }
                echo number_format_i18n($last_30_days);
                ?>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="apwp-filters">
        <form method="get" action="">
            <input type="hidden" name="page" value="amigopet-wp-signed-terms">
            
            <select name="type">
                <option value=""><?php _e('Todos os tipos', 'amigopet-wp'); ?></option>
                <?php foreach ($types as $type) : ?>
                    <option value="<?php echo esc_attr($type['id']); ?>" <?php selected($type_id, $type['id']); ?>>
                        <?php echo esc_html($type['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <input type="search" name="s" value="<?php echo esc_attr($search); ?>" placeholder="<?php esc_attr_e('Pesquisar por nome ou email...', 'amigopet-wp'); ?>">
            
            <?php submit_button(__('Filtrar', 'amigopet-wp'), 'secondary', 'filter', false); ?>
            
            <?php if ($type_id || $search) : ?>
                <a href="<?php echo admin_url('admin.php?page=amigopet-wp-signed-terms'); ?>" class="button">
                    <?php _e('Limpar', 'amigopet-wp'); ?>
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Lista de termos -->
    <?php if (!empty($terms)) : ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('Usuário', 'amigopet-wp'); ?></th>
                    <th><?php _e('Template', 'amigopet-wp'); ?></th>
                    <th><?php _e('Tipo', 'amigopet-wp'); ?></th>
                    <th><?php _e('Data', 'amigopet-wp'); ?></th>
                    <th><?php _e('Status', 'amigopet-wp'); ?></th>
                    <th><?php _e('Ações', 'amigopet-wp'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($terms as $term) : ?>
                    <tr>
                        <td>
                            <strong><?php echo esc_html($term['user_name']); ?></strong>
                            <br>
                            <small><?php echo esc_html($term['user_email']); ?></small>
                        </td>
                        <td><?php echo esc_html($term['template_title']); ?></td>
                        <td><?php echo esc_html($term['term_type_name']); ?></td>
                        <td>
                            <?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($term['signed_at'])); ?>
                        </td>
                        <td>
                            <span class="status-badge status-<?php echo esc_attr($term['status']); ?>">
                                <?php echo esc_html($term['status']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="row-actions">
                                <span class="view">
                                    <a href="#" class="view-term" data-id="<?php echo esc_attr($term['id']); ?>">
                                        <?php _e('Visualizar', 'amigopet-wp'); ?>
                                    </a> |
                                </span>
                                <span class="download">
                                    <a href="#" class="download-term" data-id="<?php echo esc_attr($term['id']); ?>">
                                        <?php _e('Download PDF', 'amigopet-wp'); ?>
                                    </a> |
                                </span>
                                <span class="email">
                                    <a href="#" class="email-term" data-id="<?php echo esc_attr($term['id']); ?>">
                                        <?php _e('Enviar por Email', 'amigopet-wp'); ?>
                                    </a>
                                </span>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Paginação -->
        <?php
        $total_items = count($terms);
        $total_pages = ceil($total_items / $per_page);
        
        if ($total_pages > 1) :
            echo '<div class="tablenav"><div class="tablenav-pages">';
            echo paginate_links(array(
                'base' => add_query_arg('paged', '%#%'),
                'format' => '',
                'prev_text' => __('&laquo;'),
                'next_text' => __('&raquo;'),
                'total' => $total_pages,
                'current' => $page
            ));
            echo '</div></div>';
        endif;
        ?>
    <?php else : ?>
        <p><?php _e('Nenhum termo assinado encontrado.', 'amigopet-wp'); ?></p>
    <?php endif; ?>
</div>

<!-- Modal para visualização do termo -->
<div id="term-preview-modal" class="apwp-modal" style="display: none;">
    <div class="apwp-modal-content">
        <span class="apwp-modal-close">&times;</span>
        <div id="term-preview-content"></div>
    </div>
</div>

<style>
.apwp-stats-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.stats-card {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    text-align: center;
}

.stats-card h3 {
    margin: 0 0 10px;
    color: #1d2327;
    font-size: 14px;
}

.stats-number {
    font-size: 24px;
    font-weight: 600;
    color: #2271b1;
}

.apwp-filters {
    margin: 20px 0;
    padding: 15px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.apwp-filters form {
    display: flex;
    gap: 10px;
    align-items: center;
}

.apwp-modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.apwp-modal-content {
    position: relative;
    background-color: #fff;
    margin: 5% auto;
    padding: 20px;
    width: 80%;
    max-width: 800px;
    max-height: 80vh;
    overflow-y: auto;
    border-radius: 8px;
}

.apwp-modal-close {
    position: absolute;
    right: 20px;
    top: 10px;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

@media screen and (max-width: 782px) {
    .apwp-filters form {
        flex-direction: column;
        align-items: stretch;
    }
    
    .apwp-filters form > * {
        margin-bottom: 10px;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Modal de visualização
    $('.view-term').on('click', function(e) {
        e.preventDefault();
        const termId = $(this).data('id');
        
        // Implementar carregamento via AJAX
        $('#term-preview-modal').show();
    });
    
    $('.apwp-modal-close').on('click', function() {
        $('#term-preview-modal').hide();
    });
    
    $(window).on('click', function(e) {
        if ($(e.target).is('.apwp-modal')) {
            $('.apwp-modal').hide();
        }
    });
    
    // Download do PDF
    $('.download-term').on('click', function(e) {
        e.preventDefault();
        const termId = $(this).data('id');
        
        // Implementar download via AJAX
    });
    
    // Envio por email
    $('.email-term').on('click', function(e) {
        e.preventDefault();
        const termId = $(this).data('id');
        
        if (confirm('<?php _e('Deseja enviar uma cópia do termo por email?', 'amigopet-wp'); ?>')) {
            // Implementar envio via AJAX
        }
    });
});
</script>
