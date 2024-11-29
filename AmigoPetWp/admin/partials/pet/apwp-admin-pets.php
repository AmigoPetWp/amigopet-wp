<?php
/**
 * Página de administração dos pets
 */

// Verifica permissões
if (!current_user_can('manage_options')) {
    wp_die(__('Você não tem permissão para acessar esta página.', 'amigopet-wp'));
}

$pet = new APWP_Pet();

// Processa formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!isset($_POST['apwp_nonce']) || !wp_verify_nonce($_POST['apwp_nonce'], 'apwp_pet_action')) {
        wp_die(__('Nonce inválido', 'amigopet-wp'));
    }

    $pet_data = array(
        'name' => sanitize_text_field($_POST['name']),
        'species' => sanitize_text_field($_POST['species']),
        'breed' => sanitize_text_field($_POST['breed']),
        'age' => intval($_POST['age']),
        'gender' => sanitize_text_field($_POST['gender']),
        'size' => sanitize_text_field($_POST['size']),
        'weight' => floatval($_POST['weight']),
        'description' => sanitize_textarea_field($_POST['description']),
        'status' => sanitize_text_field($_POST['status'])
    );

    if ($_POST['action'] === 'add') {
        if ($pet->add($pet_data)) {
            add_settings_error('apwp_messages', 'apwp_pet_added', __('Pet adicionado com sucesso!', 'amigopet-wp'), 'success');
        }
    } elseif ($_POST['action'] === 'edit' && isset($_POST['id'])) {
        if ($pet->update(intval($_POST['id']), $pet_data)) {
            add_settings_error('apwp_messages', 'apwp_pet_updated', __('Pet atualizado com sucesso!', 'amigopet-wp'), 'success');
        }
    }
}

// Filtros
$current_status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';
$current_species = isset($_GET['species']) ? sanitize_text_field($_GET['species']) : '';

// Lista de pets
$pets = $pet->list(array(
    'status' => $current_status,
    'species' => $current_species
));

// Status totais
$status_counts = $pet->count_by_status();
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php echo esc_html__('Pets Management', 'amigopet-wp'); ?></h1>
    <hr class="wp-header-end">

    <?php
    $current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'list';
    $tabs = array(
        'list' => __('Listar', 'amigopet-wp'),
        'add' => __('Adicionar', 'amigopet-wp'),
        'species' => __('Espécies', 'amigopet-wp'),
        'breeds' => __('Raças', 'amigopet-wp'),
        'reports' => __('Relatórios', 'amigopet-wp')
    );
    ?>

    <nav class="nav-tab-wrapper wp-clearfix">
        <?php
        foreach ($tabs as $tab => $name) {
            $class = ($tab === $current_tab) ? 'nav-tab nav-tab-active' : 'nav-tab';
            printf(
                '<a href="?page=%s&tab=%s" class="%s">%s</a>',
                esc_attr($_REQUEST['page']),
                esc_attr($tab),
                esc_attr($class),
                esc_html($name)
            );
        }
        ?>
    </nav>

    <div class="tab-content">
        <?php
        switch ($current_tab) {
            case 'list':
                ?>
                <a href="<?php echo admin_url('admin.php?page=amigopet-wp-add-pet'); ?>" class="page-title-action"><?php _e('Adicionar Novo', 'amigopet-wp'); ?></a>
                
                <?php settings_errors('apwp_messages'); ?>

                <!-- Filtros -->
                <div class="tablenav top">
                    <div class="alignleft actions">
                        <form method="get">
                            <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']); ?>">
                            
                            <select name="status">
                                <option value=""><?php _e('Todos os status', 'amigopet-wp'); ?></option>
                                <option value="available" <?php selected($current_status, 'available'); ?>><?php _e('Disponível', 'amigopet-wp'); ?></option>
                                <option value="adopted" <?php selected($current_status, 'adopted'); ?>><?php _e('Adotado', 'amigopet-wp'); ?></option>
                                <option value="pending" <?php selected($current_status, 'pending'); ?>><?php _e('Pendente', 'amigopet-wp'); ?></option>
                                <option value="unavailable" <?php selected($current_status, 'unavailable'); ?>><?php _e('Indisponível', 'amigopet-wp'); ?></option>
                            </select>
                            
                            <select name="species">
                                <option value=""><?php _e('Todas as espécies', 'amigopet-wp'); ?></option>
                                <option value="dog" <?php selected($current_species, 'dog'); ?>><?php _e('Cachorro', 'amigopet-wp'); ?></option>
                                <option value="cat" <?php selected($current_species, 'cat'); ?>><?php _e('Gato', 'amigopet-wp'); ?></option>
                            </select>
                            
                            <?php submit_button(__('Filtrar', 'amigopet-wp'), 'action', '', false); ?>
                        </form>
                    </div>
                </div>

                <!-- Status summary -->
                <ul class="subsubsub">
                    <?php
                    $total = 0;
                    foreach ($status_counts as $count) {
                        $total += $count->total;
                    }
                    ?>
                    <li>
                        <a href="<?php echo remove_query_arg('status'); ?>" <?php echo empty($current_status) ? 'class="current"' : ''; ?>>
                            <?php _e('Todos', 'amigopet-wp'); ?> <span class="count">(<?php echo $total; ?>)</span>
                        </a> |
                    </li>
                    <?php foreach ($status_counts as $status): ?>
                    <li>
                        <a href="<?php echo add_query_arg('status', $status->status); ?>" <?php echo $current_status === $status->status ? 'class="current"' : ''; ?>>
                            <?php echo esc_html(ucfirst($status->status)); ?> <span class="count">(<?php echo $status->total; ?>)</span>
                        </a> <?php echo $status !== end($status_counts) ? '|' : ''; ?>
                    </li>
                    <?php endforeach; ?>
                </ul>

                <!-- Lista de pets -->
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e('ID', 'amigopet-wp'); ?></th>
                            <th><?php _e('Nome', 'amigopet-wp'); ?></th>
                            <th><?php _e('Espécie', 'amigopet-wp'); ?></th>
                            <th><?php _e('Raça', 'amigopet-wp'); ?></th>
                            <th><?php _e('Idade', 'amigopet-wp'); ?></th>
                            <th><?php _e('Status', 'amigopet-wp'); ?></th>
                            <th><?php _e('Adotante', 'amigopet-wp'); ?></th>
                            <th><?php _e('Ações', 'amigopet-wp'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pets)): ?>
                            <tr>
                                <td colspan="8"><?php _e('Nenhum pet encontrado.', 'amigopet-wp'); ?></td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($pets as $item): ?>
                                <tr>
                                    <td><?php echo esc_html($item->id); ?></td>
                                    <td>
                                        <strong>
                                            <a href="<?php echo add_query_arg(array('action' => 'edit', 'id' => $item->id)); ?>">
                                                <?php echo esc_html($item->name); ?>
                                            </a>
                                        </strong>
                                    </td>
                                    <td><?php echo esc_html($item->species); ?></td>
                                    <td><?php echo esc_html($item->breed); ?></td>
                                    <td><?php echo esc_html($item->age); ?></td>
                                    <td>
                                        <span class="status-<?php echo esc_attr($item->status); ?>">
                                            <?php echo esc_html(ucfirst($item->status)); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        if ($item->adopter_id) {
                                            $adopter = get_userdata($item->adopter_id);
                                            echo $adopter ? esc_html($adopter->display_name) : __('Adotante não encontrado', 'amigopet-wp');
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo add_query_arg(array('action' => 'edit', 'id' => $item->id)); ?>" class="button button-small">
                                            <?php _e('Editar', 'amigopet-wp'); ?>
                                        </a>
                                        <?php if ($item->status === 'available'): ?>
                                            <a href="<?php echo add_query_arg(array('action' => 'adopt', 'id' => $item->id)); ?>" class="button button-small">
                                                <?php _e('Adotar', 'amigopet-wp'); ?>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                <?php
                break;
                
            case 'add':
                include_once plugin_dir_path(__FILE__) . 'apwp-admin-pet-form.php';
                break;
                
            case 'species':
                include_once plugin_dir_path(__FILE__) . 'apwp-admin-species.php';
                break;
                
            case 'breeds':
                include_once plugin_dir_path(__FILE__) . 'apwp-admin-breeds.php';
                break;
                
            case 'reports':
                include_once plugin_dir_path(__FILE__) . 'apwp-admin-pets-reports.php';
                break;
                
            default:
                include_once plugin_dir_path(__FILE__) . 'apwp-admin-pet-form.php';
                break;
        }
        ?>
    </div>
</div>
