<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Security;

if (!defined('ABSPATH')) {
    exit;
}

class SecurityService {
    private static $instance = null;
    private $userPermissions = [];
    private $roleHierarchy = [
        'administrator' => ['editor', 'author', 'contributor', 'subscriber'],
        'editor' => ['author', 'contributor', 'subscriber'],
        'author' => ['contributor', 'subscriber'],
        'contributor' => ['subscriber'],
        'subscriber' => []
    ];

    private function __construct() {
        // Singleton
    }

    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Verifica se o usuário tem permissão para executar uma ação
     */
    public function validatePermission(int $userId, string $action): bool {
        if (!$this->userPermissions) {
            $this->loadUserPermissions($userId);
        }

        // Se é admin, tem todas as permissões
        if (in_array('administrator', $this->userPermissions[$userId]['roles'])) {
            return true;
        }

        // Verifica permissões específicas
        return in_array($action, $this->userPermissions[$userId]['capabilities']);
    }

    /**
     * Carrega as permissões do usuário
     */
    private function loadUserPermissions(int $userId): void {
        $user = get_userdata($userId);
        if (!$user) {
            $this->userPermissions[$userId] = [
                'roles' => [],
                'capabilities' => []
            ];
            return;
        }

        $this->userPermissions[$userId] = [
            'roles' => $user->roles,
            'capabilities' => array_keys($user->allcaps)
        ];
    }

    /**
     * Verifica se um papel tem hierarquia sobre outro
     */
    public function hasHierarchy(string $role, string $targetRole): bool {
        return in_array($targetRole, $this->roleHierarchy[$role] ?? []);
    }

    /**
     * Sanitiza e valida input
     */
    public function sanitizeInput(array $input, array $rules = []): array {
        $sanitized = [];
        
        foreach ($input as $key => $value) {
            // Regras específicas para o campo
            $rule = $rules[$key] ?? 'text';
            
            switch ($rule) {
                case 'email':
                    $sanitized[$key] = sanitize_email($value);
                    break;
                    
                case 'url':
                    $sanitized[$key] = esc_url_raw($value);
                    break;
                    
                case 'int':
                    $sanitized[$key] = (int)$value;
                    break;
                    
                case 'float':
                    $sanitized[$key] = (float)$value;
                    break;
                    
                case 'html':
                    $sanitized[$key] = wp_kses_post($value);
                    break;
                    
                case 'array':
                    $sanitized[$key] = is_array($value) ? array_map('sanitize_text_field', $value) : [];
                    break;
                    
                case 'text':
                default:
                    $sanitized[$key] = sanitize_text_field($value);
                    break;
            }
        }
        
        return $sanitized;
    }

    /**
     * Valida CSRF token
     */
    public function validateCSRF(string $action): bool {
        $nonce = $_REQUEST['_wpnonce'] ?? '';
        return wp_verify_nonce($nonce, $action);
    }

    /**
     * Registra tentativa de acesso
     */
    public function logAccess(int $userId, string $action, bool $success): void {
        $data = [
            'user_id' => $userId,
            'action' => $action,
            'success' => $success,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'timestamp' => current_time('mysql')
        ];

        global $wpdb;
        $wpdb->insert($wpdb->prefix . 'apwp_access_log', $data);
    }

    /**
     * Verifica rate limiting
     */
    public function checkRateLimit(string $key, int $limit, int $period = 3600): bool {
        $attempts = get_transient('rate_limit_' . $key);
        if ($attempts === false) {
            set_transient('rate_limit_' . $key, 1, $period);
            return true;
        }

        if ($attempts >= $limit) {
            return false;
        }

        set_transient('rate_limit_' . $key, $attempts + 1, $period);
        return true;
    }
}