<?php
namespace AmigoPetWp\Controllers\Admin;

use AmigoPetWp\Domain\Database\Database;

abstract class BaseAdminController {
    protected $db;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->registerHooks();
    }

    /**
     * Registra os hooks do WordPress
     */
    abstract protected function registerHooks(): void;

    /**
     * Carrega uma view
     * 
     * @param string $view Nome da view
     * @param array $data Dados para a view
     * @param bool $return Se true, retorna o conteúdo ao invés de exibi-lo
     * @return string|void
     */
    protected function loadView(string $view, array $data = [], bool $return = false) {
        if ($return) {
            ob_start();
        }

        $file = AMIGOPET_WP_PLUGIN_DIR . 'AmigoPet/Views/' . $view . '.php';
        
        if (!file_exists($file)) {
            wp_die(sprintf(__('View não encontrada: %s', 'amigopet-wp'), $view));
        }

        extract($data);
        include $file;

        if ($return) {
            return ob_get_clean();
        }
    }

    /**
     * Verifica o nonce do WordPress
     */
    protected function verifyNonce(string $action): void {
        if (
            !isset($_REQUEST['_wpnonce']) ||
            !wp_verify_nonce($_REQUEST['_wpnonce'], $action)
        ) {
            wp_die(__('Ação não autorizada', 'amigopet-wp'));
        }
    }

    /**
     * Verifica as permissões do usuário
     */
    protected function checkPermission(string $capability): void {
        if (!current_user_can($capability)) {
            wp_die(__('Você não tem permissão para realizar esta ação', 'amigopet-wp'));
        }
    }

    /**
     * Redireciona com mensagem
     */
    protected function redirect(string $url, string $message, string $type = 'success'): void {
        wp_redirect(add_query_arg([
            'message' => urlencode($message),
            'type' => $type
        ], $url));
        exit;
    }
}
