<?php
/**
 * Classe que gerencia as requisições AJAX do painel administrativo
 *
 * @package AmigoPet_Wp
 * @subpackage AmigoPet_Wp/admin
 */
class AmigoPet_Wp_Admin_Ajax {

    /**
     * Inicializa os hooks do WordPress
     */
    public function __construct() {
        // Hooks para requisições AJAX
        add_action('wp_ajax_apwp_filter_table', array($this, 'filter_table'));
        add_action('wp_ajax_apwp_load_page', array($this, 'load_page'));
    }

    /**
     * Filtra uma tabela baseado nos parâmetros recebidos
     */
    public function filter_table() {
        // Verifica o nonce
        check_ajax_referer('apwp_admin_nonce', 'nonce');

        // Verifica permissões
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Você não tem permissão para realizar esta ação.', 'amigopet-wp')));
        }

        // Obtém os parâmetros
        $table = sanitize_text_field($_POST['table']);
        $search = sanitize_text_field($_POST['search']);
        $filters = isset($_POST['filters']) ? $_POST['filters'] : array();

        // Sanitiza os filtros
        $sanitized_filters = array();
        foreach ($filters as $key => $value) {
            $sanitized_filters[sanitize_key($key)] = sanitize_text_field($value);
        }

        // Determina qual tabela filtrar
        switch ($table) {
            case 'pets-list':
                $data = $this->filter_pets_table($search, $sanitized_filters);
                break;
            case 'adoptions-list':
                $data = $this->filter_adoptions_table($search, $sanitized_filters);
                break;
            case 'adopters-list':
                $data = $this->filter_adopters_table($search, $sanitized_filters);
                break;
            case 'volunteers-list':
                $data = $this->filter_volunteers_table($search, $sanitized_filters);
                break;
            case 'organizations-list':
                $data = $this->filter_organizations_table($search, $sanitized_filters);
                break;
            case 'terms-list':
                $data = $this->filter_terms_table($search, $sanitized_filters);
                break;
            default:
                wp_send_json_error(array('message' => __('Tabela inválida.', 'amigopet-wp')));
        }

        // Retorna os dados filtrados
        wp_send_json_success($data);
    }

    /**
     * Carrega uma página específica de uma tabela
     */
    public function load_page() {
        // Verifica o nonce
        check_ajax_referer('apwp_admin_nonce', 'nonce');

        // Verifica permissões
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Você não tem permissão para realizar esta ação.', 'amigopet-wp')));
        }

        // Obtém os parâmetros
        $table = sanitize_text_field($_POST['table']);
        $page = intval($_POST['page']);

        // Determina qual tabela paginar
        switch ($table) {
            case 'pets-list':
                $data = $this->paginate_pets_table($page);
                break;
            case 'adoptions-list':
                $data = $this->paginate_adoptions_table($page);
                break;
            case 'adopters-list':
                $data = $this->paginate_adopters_table($page);
                break;
            case 'volunteers-list':
                $data = $this->paginate_volunteers_table($page);
                break;
            case 'organizations-list':
                $data = $this->paginate_organizations_table($page);
                break;
            case 'terms-list':
                $data = $this->paginate_terms_table($page);
                break;
            default:
                wp_send_json_error(array('message' => __('Tabela inválida.', 'amigopet-wp')));
        }

        // Retorna os dados paginados
        wp_send_json_success($data);
    }

    /**
     * Filtra a tabela de pets
     */
    private function filter_pets_table($search, $filters) {
        global $wpdb;
        
        // Constrói a query
        $query = "SELECT * FROM {$wpdb->prefix}apwp_pets WHERE 1=1";
        $args = array();

        // Adiciona condição de busca
        if ($search) {
            $query .= " AND (name LIKE %s OR species LIKE %s OR breed LIKE %s)";
            $args[] = '%' . $wpdb->esc_like($search) . '%';
            $args[] = '%' . $wpdb->esc_like($search) . '%';
            $args[] = '%' . $wpdb->esc_like($search) . '%';
        }

        // Adiciona filtros
        if (isset($filters['species'])) {
            $query .= " AND species = %s";
            $args[] = $filters['species'];
        }

        if (isset($filters['size'])) {
            $query .= " AND size = %s";
            $args[] = $filters['size'];
        }

        // Prepara e executa a query
        if (!empty($args)) {
            $query = $wpdb->prepare($query, $args);
        }

        $items = $wpdb->get_results($query);

        // Processa os resultados
        ob_start();
        if ($items) {
            foreach ($items as $item) {
                // Renderiza cada linha da tabela
                include APWP_PLUGIN_DIR . 'admin/partials/pet/apwp-admin-pet-row.php';
            }
        }
        $html = ob_get_clean();

        // Gera a paginação
        $total = count($items);
        $per_page = 10;
        $total_pages = ceil($total / $per_page);
        
        ob_start();
        for ($i = 1; $i <= $total_pages; $i++) {
            $active = $i === 1 ? ' active' : '';
            echo '<a href="#" class="apwp-admin-page-link' . $active . '" data-page="' . $i . '">' . $i . '</a>';
        }
        $pagination = ob_get_clean();

        return array(
            'html' => $html,
            'pagination' => $pagination
        );
    }

    // Implemente os outros métodos de filtragem de forma similar...

    /**
     * Pagina a tabela de pets
     */
    private function paginate_pets_table($page) {
        global $wpdb;
        
        $per_page = 10;
        $offset = ($page - 1) * $per_page;

        // Obtém o total de itens
        $total = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}apwp_pets");

        // Obtém os itens da página atual
        $items = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}apwp_pets LIMIT %d OFFSET %d",
                $per_page,
                $offset
            )
        );

        // Processa os resultados
        ob_start();
        if ($items) {
            foreach ($items as $item) {
                // Renderiza cada linha da tabela
                include APWP_PLUGIN_DIR . 'admin/partials/pet/apwp-admin-pet-row.php';
            }
        }
        $html = ob_get_clean();

        // Gera a paginação
        $total_pages = ceil($total / $per_page);
        
        ob_start();
        for ($i = 1; $i <= $total_pages; $i++) {
            $active = $i === $page ? ' active' : '';
            echo '<a href="#" class="apwp-admin-page-link' . $active . '" data-page="' . $i . '">' . $i . '</a>';
        }
        $pagination = ob_get_clean();

        return array(
            'html' => $html,
            'pagination' => $pagination
        );
    }

    // Implemente os outros métodos de paginação de forma similar...
}
