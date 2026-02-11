<?php declare(strict_types=1);
namespace AmigoPetWp\Controllers\Admin;

if (!defined('ABSPATH')) {
    exit;
}

use AmigoPetWp\Domain\Database\Database;

abstract class BaseAdminController
{
    protected $db;

    public function __construct()
    {
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
    protected function loadView(string $view, array $data = [], bool $return = false)
    {
        if ($return) {
            ob_start();
        }

        $file = AMIGOPET_PLUGIN_DIR . 'AmigoPet/Views/' . $view . '.php';

        if (!file_exists($file)) {
            // translators: %s: view name
            // translators: %s: placeholder
            wp_die(sprintf(esc_html__('View não encontrada: %s', 'amigopet'), esc_html($view)));
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
    protected function verifyNonce(string $action): void
    {
        if (
            !isset($_REQUEST['_wpnonce']) ||
            !wp_verify_nonce($_REQUEST['_wpnonce'], $action)
        ) {
            wp_die(esc_html__('Ação não autorizada', 'amigopet'));
        }
    }

    /**
     * Verifica as permissões do usuário
     */
    protected function checkPermission(string $capability): void
    {
        if (!current_user_can($capability)) {
            wp_die(esc_html__('Você não tem permissão para realizar esta ação', 'amigopet'));
        }
    }

    /**
     * Redireciona com mensagem
     */
    protected function redirect(string $url, string $message, string $type = 'success'): void
    {
        wp_safe_redirect(add_query_arg([
            'message' => urlencode($message),
            'type' => urlencode($type)
        ], $url));
        exit;
    }
}