<?php
/**
 * Template para a página de adotantes do plugin
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

// Obtém a página atual
$page = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : '';
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Gerenciar Adotantes', 'amigopet-wp'); ?></h1>
    
    <?php
    $current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'list';
    $tabs = array(
        'list' => array(
            'title' => __('Listar', 'amigopet-wp'),
            'icon' => 'dashicons-list-view'
        ),
        'add' => array(
            'title' => __('Adicionar', 'amigopet-wp'),
            'icon' => 'dashicons-plus'
        ),
        'reports' => array(
            'title' => __('Relatórios', 'amigopet-wp'),
            'icon' => 'dashicons-analytics'
        )
    );
    ?>

    <nav class="nav-tab-wrapper wp-clearfix">
        <?php
        foreach ($tabs as $tab_id => $tab) {
            $class = ($tab_id === $current_tab) ? 'nav-tab nav-tab-active' : 'nav-tab';
            printf(
                '<a href="?page=%s&tab=%s" class="%s"><span class="dashicons %s"></span>%s</a>',
                esc_attr($_REQUEST['page']),
                esc_attr($tab_id),
                esc_attr($class),
                esc_attr($tab['icon']),
                esc_html($tab['title'])
            );
        }
        ?>
    </nav>

    <?php
    // Exibe mensagens de sucesso ou erro
    if ($message = get_transient('apwp_adopter_message')) {
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html($message) . '</p></div>';
        delete_transient('apwp_adopter_message');
    }

    $errors = get_transient('apwp_adopter_form_errors');
    if (!empty($errors)) {
        echo '<div class="notice notice-error is-dismissible">';
        foreach ($errors as $error) {
            echo '<p>' . esc_html($error) . '</p>';
        }
        echo '</div>';
        delete_transient('apwp_adopter_form_errors');
    }

    // Renderiza a página correta baseada na tab selecionada
    switch ($current_tab) {
        case 'add':
            // Formulário de adição de adotante
            ?>
            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                <?php wp_nonce_field('apwp_save_adopter', 'apwp_adopter_nonce'); ?>
                <input type="hidden" name="action" value="apwp_save_adopter">
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="adopter_name"><?php _e('Nome Completo', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="adopter_name" name="adopter_name" class="regular-text" required>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="adopter_email"><?php _e('Email', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <input type="email" id="adopter_email" name="adopter_email" class="regular-text" required>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="adopter_phone"><?php _e('Telefone', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <input type="tel" id="adopter_phone" name="adopter_phone" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="adopter_address"><?php _e('Endereço', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <textarea id="adopter_address" name="adopter_address" class="large-text" rows="3"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="adopter_city"><?php _e('Cidade', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="adopter_city" name="adopter_city" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="adopter_state"><?php _e('Estado', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="adopter_state" name="adopter_state" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="adopter_zip_code"><?php _e('CEP', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="adopter_zip_code" name="adopter_zip_code" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="adopter_household_type"><?php _e('Tipo de Família', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="adopter_household_type" name="adopter_household_type" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="adopter_has_yard"><?php _e('Possui Quintal', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <input type="checkbox" id="adopter_has_yard" name="adopter_has_yard">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="adopter_other_pets"><?php _e('Possui Outros Animais', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <input type="checkbox" id="adopter_other_pets" name="adopter_other_pets">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="adopter_children_at_home"><?php _e('Possui Filhos em Casa', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <input type="checkbox" id="adopter_children_at_home" name="adopter_children_at_home">
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(__('Cadastrar Adotante', 'amigopet-wp')); ?>
            </form>
            <?php
            break;
        case 'reports':
            // Relatórios de adotantes
            global $wpdb;
            $table_name = $wpdb->prefix . 'apwp_adopters';

            // Relatório básico
            $report = [
                'total_adopters' => $wpdb->get_var("SELECT COUNT(*) FROM $table_name"),
                'active_adopters' => $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'active'"),
                'recent_adopters' => $wpdb->get_results(
                    "SELECT name, email, created_at 
                     FROM $table_name 
                     ORDER BY created_at DESC 
                     LIMIT 10", 
                    ARRAY_A
                )
            ];
            ?>
            <div class="apwp-reports-container">
                <h2><?php _e('Relatório de Adotantes', 'amigopet-wp'); ?></h2>
                
                <div class="apwp-report-summary">
                    <div class="apwp-report-card">
                        <h3><?php _e('Total de Adotantes', 'amigopet-wp'); ?></h3>
                        <p class="apwp-report-number"><?php echo esc_html($report['total_adopters']); ?></p>
                    </div>
                    <div class="apwp-report-card">
                        <h3><?php _e('Adotantes Ativos', 'amigopet-wp'); ?></h3>
                        <p class="apwp-report-number"><?php echo esc_html($report['active_adopters']); ?></p>
                    </div>
                </div>

                <h3><?php _e('10 Adotantes Mais Recentes', 'amigopet-wp'); ?></h3>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e('Nome', 'amigopet-wp'); ?></th>
                            <th><?php _e('Email', 'amigopet-wp'); ?></th>
                            <th><?php _e('Data de Cadastro', 'amigopet-wp'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($report['recent_adopters'] as $adopter): ?>
                        <tr>
                            <td><?php echo esc_html($adopter['name']); ?></td>
                            <td><?php echo esc_html($adopter['email']); ?></td>
                            <td><?php echo esc_html($adopter['created_at']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php
            break;
        default:
            // Configurações de paginação
            $per_page = 20;
            $page_number = $_GET['paged'] ?? 1;

            // Obtém lista de adotantes
            $adopters_list = $this->get_adopters_list($per_page, $page_number);
            $total_adopters = $this->count_adopters();
            ?>
            <div class="wrap">
                <h1 class="wp-heading-inline"><?php _e('Adotantes', 'amigopet-wp'); ?></h1>
                <a href="<?php echo admin_url('admin.php?page=amigopet-wp-adopters&tab=add'); ?>" class="page-title-action"><?php _e('Adicionar Novo Adotante', 'amigopet-wp'); ?></a>

                <?php 
                // Exibe a barra de filtros
                $status_links = apply_filters('views_toplevel_page_amigopet-wp-adopters', []);
                if (!empty($status_links)) {
                    echo '<ul class="subsubsub">';
                    foreach ($status_links as $key => $link) {
                        echo '<li class="' . esc_attr($key) . '">' . $link . '</li>';
                    }
                    echo '</ul>';
                }
                ?>

                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e('Nome', 'amigopet-wp'); ?></th>
                            <th><?php _e('Email', 'amigopet-wp'); ?></th>
                            <th><?php _e('Telefone', 'amigopet-wp'); ?></th>
                            <th><?php _e('Status', 'amigopet-wp'); ?></th>
                            <th><?php _e('Data de Cadastro', 'amigopet-wp'); ?></th>
                            <th><?php _e('Ações', 'amigopet-wp'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (empty($adopters_list)) {
                            echo '<tr><td colspan="6">' . __('Nenhum adotante encontrado.', 'amigopet-wp') . '</td></tr>';
                        } else {
                            foreach ($adopters_list as $adopter): 
                                $status_classes = [
                                    'active' => 'status-green',
                                    'inactive' => 'status-red',
                                    'pending_verification' => 'status-yellow'
                                ];
                        ?>
                        <tr>
                            <td><?php echo esc_html($adopter['name']); ?></td>
                            <td><?php echo esc_html($adopter['email']); ?></td>
                            <td><?php echo esc_html($adopter['phone']); ?></td>
                            <td>
                                <span class="status-badge <?php echo esc_attr($status_classes[$adopter['status']] ?? ''); ?>">
                                    <?php echo esc_html(ucfirst(__($adopter['status'], 'amigopet-wp'))); ?>
                                </span>
                            </td>
                            <td><?php echo esc_html($adopter['created_at']); ?></td>
                            <td>
                                <a href="<?php echo admin_url('admin.php?page=amigopet-wp-adopters&action=edit&id=' . $adopter['id']); ?>" class="button button-small"><?php _e('Editar', 'amigopet-wp'); ?></a>
                                <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=amigopet-wp-adopters&action=delete&id=' . $adopter['id']), 'apwp_delete_adopter_' . $adopter['id']); ?>" class="button button-small button-link-delete" onclick="return confirm('<?php _e('Tem certeza que deseja excluir este adotante?', 'amigopet-wp'); ?>');"><?php _e('Excluir', 'amigopet-wp'); ?></a>
                            </td>
                        </tr>
                        <?php 
                            endforeach; 
                        } 
                        ?>
                    </tbody>
                </table>

                <?php 
                // Renderiza paginação
                $this->render_adopters_pagination($total_adopters, $per_page);
                ?>
            </div>

            <?php
    }
    ?>
</div>

<?php
// Processa formulário de adotante
function apwp_process_adopter_form() {
    // Verifica nonce para segurança
    if (!isset($_POST['apwp_adopter_nonce']) || 
        !wp_verify_nonce($_POST['apwp_adopter_nonce'], 'apwp_save_adopter')) {
        wp_die(__('Erro de segurança. Por favor, tente novamente.', 'amigopet-wp'));
    }

    // Valida e sanitiza dados
    $name = sanitize_text_field($_POST['adopter_name'] ?? '');
    $email = sanitize_email($_POST['adopter_email'] ?? '');
    $phone = sanitize_text_field($_POST['adopter_phone'] ?? '');
    $address = sanitize_textarea_field($_POST['adopter_address'] ?? '');
    $city = sanitize_text_field($_POST['adopter_city'] ?? '');
    $state = sanitize_text_field($_POST['adopter_state'] ?? '');
    $zip_code = sanitize_text_field($_POST['adopter_zip_code'] ?? '');
    $household_type = sanitize_text_field($_POST['adopter_household_type'] ?? '');
    $has_yard = isset($_POST['adopter_has_yard']) ? 1 : 0;
    $other_pets = isset($_POST['adopter_other_pets']) ? 1 : 0;
    $children_at_home = isset($_POST['adopter_children_at_home']) ? 1 : 0;

    // Validações
    $errors = [];

    if (empty($name)) {
        $errors[] = __('Nome do adotante é obrigatório.', 'amigopet-wp');
    }

    if (empty($email) || !is_email($email)) {
        $errors[] = __('Email inválido.', 'amigopet-wp');
    }

    if (!empty($phone) && !preg_match('/^[0-9\-\(\)\s]+$/', $phone)) {
        $errors[] = __('Número de telefone inválido.', 'amigopet-wp');
    }

    if (!empty($zip_code) && !preg_match('/^[0-9]{5}(-[0-9]{4})?$/', $zip_code)) {
        $errors[] = __('CEP inválido.', 'amigopet-wp');
    }

    // Se houver erros, retorna para o formulário
    if (!empty($errors)) {
        // Armazena erros na sessão para exibição
        set_transient('apwp_adopter_form_errors', $errors, 60);
        wp_redirect(admin_url('admin.php?page=amigopet-wp-adopters&tab=add'));
        exit;
    }

    // Salva adotante no banco de dados
    global $wpdb;
    $table_name = $wpdb->prefix . 'apwp_adopters';

    $result = $wpdb->insert(
        $table_name,
        [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'city' => $city,
            'state' => $state,
            'zip_code' => $zip_code,
            'household_type' => $household_type,
            'has_yard' => $has_yard,
            'other_pets' => $other_pets,
            'children_at_home' => $children_at_home,
            'created_at' => current_time('mysql'),
            'status' => 'active'
        ],
        ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%s', '%s']
    );

    if ($result) {
        // Mensagem de sucesso
        set_transient('apwp_adopter_message', __('Adotante cadastrado com sucesso!', 'amigopet-wp'), 60);
        wp_redirect(admin_url('admin.php?page=amigopet-wp-adopters'));
    } else {
        // Erro ao salvar
        set_transient('apwp_adopter_form_errors', 
            [__('Erro ao salvar adotante. Por favor, tente novamente.', 'amigopet-wp')], 
            60
        );
        wp_redirect(admin_url('admin.php?page=amigopet-wp-adopters&tab=add'));
    }
    exit;
}
add_action('admin_post_apwp_save_adopter', 'apwp_process_adopter_form');

// Funções de processamento de ações
function apwp_handle_adopter_actions() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'apwp_adopters';

    // Verifica se é uma ação de edição
    if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
        $adopter_id = intval($_GET['id']);
        $adopter = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d", 
            $adopter_id
        ), ARRAY_A);

        if (!$adopter) {
            wp_die(__('Adotante não encontrado.', 'amigopet-wp'));
        }

        // Renderiza o formulário de edição
        ?>
        <div class="wrap">
            <h1><?php _e('Editar Adotante', 'amigopet-wp'); ?></h1>
            
            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                <?php wp_nonce_field('apwp_update_adopter', 'apwp_adopter_nonce'); ?>
                <input type="hidden" name="action" value="apwp_update_adopter">
                <input type="hidden" name="adopter_id" value="<?php echo esc_attr($adopter_id); ?>">
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="adopter_name"><?php _e('Nome Completo', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="adopter_name" name="adopter_name" class="regular-text" 
                                value="<?php echo esc_attr($adopter['name']); ?>" required>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="adopter_email"><?php _e('Email', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <input type="email" id="adopter_email" name="adopter_email" class="regular-text" 
                                value="<?php echo esc_attr($adopter['email']); ?>" required>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="adopter_phone"><?php _e('Telefone', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <input type="tel" id="adopter_phone" name="adopter_phone" class="regular-text" 
                                value="<?php echo esc_attr($adopter['phone']); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="adopter_address"><?php _e('Endereço', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <textarea id="adopter_address" name="adopter_address" class="large-text" rows="3"><?php 
                                echo esc_textarea($adopter['address']); 
                            ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="adopter_status"><?php _e('Status', 'amigopet-wp'); ?></label>
                        </th>
                        <td>
                            <select id="adopter_status" name="adopter_status">
                                <option value="active" <?php selected($adopter['status'], 'active'); ?>>
                                    <?php _e('Ativo', 'amigopet-wp'); ?>
                                </option>
                                <option value="inactive" <?php selected($adopter['status'], 'inactive'); ?>>
                                    <?php _e('Inativo', 'amigopet-wp'); ?>
                                </option>
                                <option value="pending_verification" <?php selected($adopter['status'], 'pending_verification'); ?>>
                                    <?php _e('Pendente de Verificação', 'amigopet-wp'); ?>
                                </option>
                            </select>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(__('Atualizar Adotante', 'amigopet-wp')); ?>
            </form>
        </div>
        <?php
        exit;
    }

    // Verifica se é uma ação de exclusão
    if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
        $adopter_id = intval($_GET['id']);

        // Verifica o nonce de segurança
        if (!wp_verify_nonce($_GET['nonce'], 'apwp_delete_adopter_' . $adopter_id)) {
            wp_die(__('Erro de segurança. Não foi possível excluir o adotante.', 'amigopet-wp'));
        }

        // Executa a exclusão
        $result = $wpdb->delete(
            $table_name, 
            ['id' => $adopter_id], 
            ['%d']
        );

        if ($result) {
            // Redireciona com mensagem de sucesso
            set_transient('apwp_adopter_message', __('Adotante excluído com sucesso!', 'amigopet-wp'), 60);
        } else {
            // Redireciona com mensagem de erro
            set_transient('apwp_adopter_form_errors', 
                [__('Erro ao excluir adotante. Por favor, tente novamente.', 'amigopet-wp')], 
                60
            );
        }

        wp_redirect(admin_url('admin.php?page=amigopet-wp-adopters'));
        exit;
    }
}
add_action('admin_init', 'apwp_handle_adopter_actions');

// Função para atualizar adotante
function apwp_update_adopter() {
    // Verifica nonce para segurança
    if (!isset($_POST['apwp_adopter_nonce']) || 
        !wp_verify_nonce($_POST['apwp_adopter_nonce'], 'apwp_update_adopter')) {
        wp_die(__('Erro de segurança. Por favor, tente novamente.', 'amigopet-wp'));
    }

    // Valida e sanitiza dados
    $adopter_id = intval($_POST['adopter_id']);
    $name = sanitize_text_field($_POST['adopter_name'] ?? '');
    $email = sanitize_email($_POST['adopter_email'] ?? '');
    $phone = sanitize_text_field($_POST['adopter_phone'] ?? '');
    $address = sanitize_textarea_field($_POST['adopter_address'] ?? '');
    $status = sanitize_text_field($_POST['adopter_status'] ?? 'active');

    // Validações
    $errors = [];

    if (empty($name)) {
        $errors[] = __('Nome do adotante é obrigatório.', 'amigopet-wp');
    }

    if (empty($email) || !is_email($email)) {
        $errors[] = __('Email inválido.', 'amigopet-wp');
    }

    if (!empty($phone) && !preg_match('/^[0-9\-\(\)\s]+$/', $phone)) {
        $errors[] = __('Número de telefone inválido.', 'amigopet-wp');
    }

    // Se houver erros, retorna para o formulário
    if (!empty($errors)) {
        // Armazena erros na sessão para exibição
        set_transient('apwp_adopter_form_errors', $errors, 60);
        wp_redirect(admin_url('admin.php?page=amigopet-wp-adopters&action=edit&id=' . $adopter_id));
        exit;
    }

    // Atualiza adotante no banco de dados
    global $wpdb;
    $table_name = $wpdb->prefix . 'apwp_adopters';

    $result = $wpdb->update(
        $table_name,
        [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'status' => $status
        ],
        ['id' => $adopter_id],
        ['%s', '%s', '%s', '%s', '%s'],
        ['%d']
    );

    if ($result !== false) {
        // Mensagem de sucesso
        set_transient('apwp_adopter_message', __('Adotante atualizado com sucesso!', 'amigopet-wp'), 60);
        wp_redirect(admin_url('admin.php?page=amigopet-wp-adopters'));
    } else {
        // Erro ao salvar
        set_transient('apwp_adopter_form_errors', 
            [__('Erro ao atualizar adotante. Por favor, tente novamente.', 'amigopet-wp')], 
            60
        );
        wp_redirect(admin_url('admin.php?page=amigopet-wp-adopters&action=edit&id=' . $adopter_id));
    }
    exit;
}
add_action('admin_post_apwp_update_adopter', 'apwp_update_adopter');

// Função para adicionar filtros de pesquisa
function apwp_adopters_list_table_filter($views) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'apwp_adopters';

    // Contagem de adotantes por status
    $status_counts = $wpdb->get_results(
        "SELECT status, COUNT(*) as count 
         FROM $table_name 
         GROUP BY status", 
        ARRAY_A
    );

    // Adiciona links de filtro
    $status_links = [];
    $current_status = $_GET['status'] ?? 'all';

    // Link para todos os adotantes
    $total_count = array_sum(wp_list_pluck($status_counts, 'count'));
    $status_links['all'] = sprintf(
        '<a href="%s" class="%s">%s <span class="count">(%d)</span></a>',
        admin_url('admin.php?page=amigopet-wp-adopters'),
        $current_status == 'all' ? 'current' : '',
        __('Todos', 'amigopet-wp'),
        $total_count
    );

    // Links para cada status
    foreach ($status_counts as $status_data) {
        $status = $status_data['status'];
        $count = $status_data['count'];
        $status_links[$status] = sprintf(
            '<a href="%s" class="%s">%s <span class="count">(%d)</span></a>',
            admin_url('admin.php?page=amigopet-wp-adopters&status=' . $status),
            $current_status == $status ? 'current' : '',
            ucfirst(__($status, 'amigopet-wp')),
            $count
        );
    }

    return $status_links;
}
add_filter('views_toplevel_page_amigopet-wp-adopters', 'apwp_adopters_list_table_filter');

// Função para adicionar campo de busca
function apwp_adopters_search_box() {
    $search = $_GET['s'] ?? '';
    ?>
    <form method="get">
        <input type="hidden" name="page" value="<?php echo esc_attr($_GET['page']); ?>"/>
        <input type="search" name="s" value="<?php echo esc_attr($search); ?>" 
               placeholder="<?php _e('Buscar adotantes', 'amigopet-wp'); ?>"/>
        <?php submit_button(__('Buscar', 'amigopet-wp'), 'button', false, false); ?>
    </form>
    <?php
}
add_action('admin_footer', 'apwp_adopters_search_box');

// Função para filtrar resultados de adotantes
function apwp_filter_adopters_query($query) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'apwp_adopters';

    // Verifica se está na página de adotantes
    if (!is_admin() || !isset($_GET['page']) || $_GET['page'] !== 'amigopet-wp-adopters') {
        return $query;
    }

    // Filtro por status
    $status = $_GET['status'] ?? null;
    
    // Filtro de busca
    $search = $_GET['s'] ?? '';

    // Construção da query base
    $where_clauses = [];
    $params = [];

    // Filtro de status
    if ($status && $status !== 'all') {
        $where_clauses[] = "status = %s";
        $params[] = $status;
    }

    // Filtro de busca
    if (!empty($search)) {
        $where_clauses[] = "(name LIKE %s OR email LIKE %s OR phone LIKE %s)";
        $search_param = '%' . $wpdb->esc_like($search) . '%';
        $params[] = $search_param;
        $params[] = $search_param;
        $params[] = $search_param;
    }

    // Monta a query completa
    $where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';
    
    $query = $wpdb->prepare(
        "SELECT * FROM $table_name $where_sql ORDER BY created_at DESC",
        $params
    );

    return $query;
}

// Função para obter lista de adotantes
function get_adopters_list($per_page, $page_number) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'apwp_adopters';

    // Filtro por status
    $status = $_GET['status'] ?? null;
    
    // Filtro de busca
    $search = $_GET['s'] ?? '';

    // Construção da query base
    $where_clauses = [];
    $params = [];

    // Filtro de status
    if ($status && $status !== 'all') {
        $where_clauses[] = "status = %s";
        $params[] = $status;
    }

    // Filtro de busca
    if (!empty($search)) {
        $where_clauses[] = "(name LIKE %s OR email LIKE %s OR phone LIKE %s)";
        $search_param = '%' . $wpdb->esc_like($search) . '%';
        $params[] = $search_param;
        $params[] = $search_param;
        $params[] = $search_param;
    }

    // Monta a query completa
    $where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';
    
    $query = $wpdb->prepare(
        "SELECT * FROM $table_name $where_sql ORDER BY created_at DESC LIMIT %d, %d",
        $params
    );

    $query .= ' OFFSET ' . (($page_number - 1) * $per_page);

    return $wpdb->get_results($query, ARRAY_A);
}

// Função para contar adotantes
function count_adopters() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'apwp_adopters';

    // Filtro por status
    $status = $_GET['status'] ?? null;
    
    // Filtro de busca
    $search = $_GET['s'] ?? '';

    // Construção da query base
    $where_clauses = [];
    $params = [];

    // Filtro de status
    if ($status && $status !== 'all') {
        $where_clauses[] = "status = %s";
        $params[] = $status;
    }

    // Filtro de busca
    if (!empty($search)) {
        $where_clauses[] = "(name LIKE %s OR email LIKE %s OR phone LIKE %s)";
        $search_param = '%' . $wpdb->esc_like($search) . '%';
        $params[] = $search_param;
        $params[] = $search_param;
        $params[] = $search_param;
    }

    // Monta a query completa
    $where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';
    
    $query = $wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name $where_sql",
        $params
    );

    return $wpdb->get_var($query);
}

// Função para renderizar paginação
function render_adopters_pagination($total_adopters, $per_page) {
    $page_number = $_GET['paged'] ?? 1;

    $total_pages = ceil($total_adopters / $per_page);

    if ($total_pages > 1) {
        ?>
        <div class="tablenav bottom">
            <div class="tablenav-pages">
                <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                <span class="pagination-links">
                    <a href="<?php echo admin_url('admin.php?page=amigopet-wp-adopters&paged=' . $i); ?>" 
                       class="<?php echo ($page_number == $i) ? 'current' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                </span>
                <?php endfor; ?>
            </div>
        </div>
        <?php
    }
}
