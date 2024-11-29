<?php
/**
 * Template para a página de ajuda
 */

// Verifica permissões
if (!current_user_can('manage_options')) {
    wp_die(__('Você não tem permissão para acessar esta página.', 'amigopet-wp'));
}

// Define as abas
$tabs = array(
    'help' => array(
        'title' => __('Ajuda', 'amigopet-wp'),
        'icon' => 'dashicons-editor-help'
    ),
    'shortcodes' => array(
        'title' => __('Shortcodes', 'amigopet-wp'),
        'icon' => 'dashicons-editor-code'
    )
);

// Obtém a aba atual
$current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'help';
?>

<div class="wrap">
    <h1><?php _e('Ajuda', 'amigopet-wp'); ?></h1>

    <nav class="nav-tab-wrapper">
        <?php foreach ($tabs as $tab_id => $tab): ?>
            <a href="<?php echo add_query_arg('tab', $tab_id); ?>" 
               class="nav-tab <?php echo $current_tab === $tab_id ? 'nav-tab-active' : ''; ?>">
                <span class="dashicons <?php echo esc_attr($tab['icon']); ?>"></span>
                <?php echo esc_html($tab['title']); ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="tab-content">
        <?php if ($current_tab === 'help'): ?>
            <div class="apwp-help-content">
                <h2><?php _e('Bem-vindo ao AmigoPet WP', 'amigopet-wp'); ?></h2>
                
                <div class="apwp-help-section">
                    <h3><?php _e('Começando', 'amigopet-wp'); ?></h3>
                    <p><?php _e('O AmigoPet WP é um plugin WordPress para gerenciamento de pets e adoções. Aqui está um guia rápido para começar:', 'amigopet-wp'); ?></p>
                    <ol>
                        <li><?php _e('Configure as informações básicas em "Configurações"', 'amigopet-wp'); ?></li>
                        <li><?php _e('Adicione pets através do menu "Pets"', 'amigopet-wp'); ?></li>
                        <li><?php _e('Gerencie adoções no menu "Adoções"', 'amigopet-wp'); ?></li>
                        <li><?php _e('Cadastre adotantes em "Adotantes"', 'amigopet-wp'); ?></li>
                    </ol>
                </div>

                <div class="apwp-help-section">
                    <h3><?php _e('Recursos Principais', 'amigopet-wp'); ?></h3>
                    <ul>
                        <li><?php _e('Gerenciamento completo de pets', 'amigopet-wp'); ?></li>
                        <li><?php _e('Sistema de adoção integrado', 'amigopet-wp'); ?></li>
                        <li><?php _e('Cadastro de adotantes', 'amigopet-wp'); ?></li>
                        <li><?php _e('Termos e contratos personalizáveis', 'amigopet-wp'); ?></li>
                        <li><?php _e('Shortcodes para exibição de conteúdo', 'amigopet-wp'); ?></li>
                    </ul>
                </div>

                <div class="apwp-help-section">
                    <h3><?php _e('Suporte', 'amigopet-wp'); ?></h3>
                    <p>
                        <?php _e('Para suporte técnico ou dúvidas, entre em contato através do GitHub:', 'amigopet-wp'); ?>
                        <a href="https://github.com/jacksonsalopek/amigopet-wp" target="_blank">AmigoPet WP no GitHub</a>
                    </p>
                </div>
            </div>

        <?php elseif ($current_tab === 'shortcodes'): ?>
            <div class="apwp-shortcodes-content">
                <h2><?php _e('Shortcodes Disponíveis', 'amigopet-wp'); ?></h2>
                
                <div class="apwp-shortcode-item">
                    <h3>[apwp_pets_grid]</h3>
                    <p><?php _e('Exibe uma grade de pets disponíveis para adoção.', 'amigopet-wp'); ?></p>
                    <h4><?php _e('Atributos:', 'amigopet-wp'); ?></h4>
                    <ul>
                        <li><code>limit</code>: <?php _e('Número de pets a exibir (padrão: 12)', 'amigopet-wp'); ?></li>
                        <li><code>species</code>: <?php _e('Filtrar por espécie (ex: dog, cat)', 'amigopet-wp'); ?></li>
                        <li><code>status</code>: <?php _e('Filtrar por status (padrão: available)', 'amigopet-wp'); ?></li>
                    </ul>
                </div>

                <div class="apwp-shortcode-item">
                    <h3>[apwp_adoption_form]</h3>
                    <p><?php _e('Exibe o formulário de adoção.', 'amigopet-wp'); ?></p>
                    <h4><?php _e('Atributos:', 'amigopet-wp'); ?></h4>
                    <ul>
                        <li><code>pet_id</code>: <?php _e('ID do pet (opcional)', 'amigopet-wp'); ?></li>
                        <li><code>title</code>: <?php _e('Título do formulário (opcional)', 'amigopet-wp'); ?></li>
                    </ul>
                </div>

                <div class="apwp-shortcode-item">
                    <h3>[apwp_featured_pets]</h3>
                    <p><?php _e('Exibe uma lista de pets em destaque.', 'amigopet-wp'); ?></p>
                    <h4><?php _e('Atributos:', 'amigopet-wp'); ?></h4>
                    <ul>
                        <li><code>limit</code>: <?php _e('Número de pets a exibir (padrão: 4)', 'amigopet-wp'); ?></li>
                        <li><code>layout</code>: <?php _e('Estilo de layout (grid, carousel)', 'amigopet-wp'); ?></li>
                    </ul>
                </div>

                <div class="apwp-shortcode-item">
                    <h3>[apwp_pet_counter]</h3>
                    <p><?php _e('Exibe contadores de pets por status.', 'amigopet-wp'); ?></p>
                    <h4><?php _e('Atributos:', 'amigopet-wp'); ?></h4>
                    <ul>
                        <li><code>show</code>: <?php _e('Status a exibir (available, adopted, all)', 'amigopet-wp'); ?></li>
                    </ul>
                </div>

                <div class="apwp-shortcode-item">
                    <h3>[apwp_pet_search]</h3>
                    <p><?php _e('Exibe um formulário de busca de pets.', 'amigopet-wp'); ?></p>
                    <h4><?php _e('Atributos:', 'amigopet-wp'); ?></h4>
                    <ul>
                        <li><code>filters</code>: <?php _e('Filtros a exibir (species, breed, age, size)', 'amigopet-wp'); ?></li>
                    </ul>
                </div>

                <div class="apwp-shortcode-item">
                    <h3>[apwp_single_pet]</h3>
                    <p><?php _e('Exibe informações detalhadas de um pet específico.', 'amigopet-wp'); ?></p>
                    <h4><?php _e('Atributos:', 'amigopet-wp'); ?></h4>
                    <ul>
                        <li><code>id</code>: <?php _e('ID do pet (obrigatório)', 'amigopet-wp'); ?></li>
                        <li><code>show_adoption_button</code>: <?php _e('Exibir botão de adoção (true/false)', 'amigopet-wp'); ?></li>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
