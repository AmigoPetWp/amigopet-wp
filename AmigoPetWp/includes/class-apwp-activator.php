<?php

/**
 * Acionado durante a ativação do plugin.
 *
 * Esta classe define tudo o que precisa acontecer durante
 * a ativação do plugin.
 *
 * @since      1.0.0
 * @package    AmigoPet_Wp
 * @subpackage AmigoPet_Wp/includes
 */
class APWP_Activator {

    /**
     * Mensagens de log da ativação
     */
    private static $activation_messages = array();

    /**
     * Método executado na ativação do plugin
     */
    public static function activate() {
        self::$activation_messages = array();
        self::log_message('Iniciando ativação do plugin AmigoPetWp...');

        // Criar tabelas do banco de dados
        self::log_message('Criando tabelas do banco de dados...');
        $db_success = APWP_Database::create_tables();
        
        if ($db_success) {
            self::log_message('✓ Tabelas criadas com sucesso!');
        } else {
            self::log_message('✗ Erro ao criar algumas tabelas. Verifique os logs do WordPress para mais detalhes.');
        }

        // Registrar capabilities
        self::log_message('Configurando permissões de usuários...');
        self::setup_capabilities();
        self::log_message('✓ Permissões configuradas com sucesso!');

        // Configurar opções padrão
        self::log_message('Configurando opções padrão...');
        self::setup_default_options();
        self::log_message('✓ Opções configuradas com sucesso!');

        // Cria as páginas padrão do plugin
        self::create_default_pages();

        // Adicionar action para mostrar mensagem de ativação
        add_action('activated_plugin', array(__CLASS__, 'show_activation_message'));

        self::log_message('Ativação concluída!');
    }

    /**
     * Configura as capabilities dos usuários
     */
    private static function setup_capabilities() {
        $admin = get_role('administrator');
        
        // Capabilities para pets
        $admin->add_cap('create_pets');
        $admin->add_cap('edit_pets');
        $admin->add_cap('delete_pets');
        $admin->add_cap('list_pets');
        
        // Capabilities para adoções
        $admin->add_cap('manage_adoptions');
        $admin->add_cap('approve_adoptions');
        
        // Capabilities para organizações
        $admin->add_cap('manage_organizations');
        
        // Capabilities para termos
        $admin->add_cap('manage_terms');
    }

    /**
     * Configura as opções padrão do plugin
     */
    private static function setup_default_options() {
        // Opções gerais
        add_option('apwp_enable_adoptions', 'yes');
        add_option('apwp_enable_organizations', 'yes');
        add_option('apwp_enable_advertisers', 'yes');
        
        // Opções de e-mail
        add_option('apwp_email_notifications', 'yes');
        add_option('apwp_email_from_name', get_bloginfo('name'));
        add_option('apwp_email_from_address', get_bloginfo('admin_email'));
        
        // Opções de upload
        add_option('apwp_max_photos_per_pet', 5);
        add_option('apwp_allowed_photo_types', array('jpg', 'jpeg', 'png'));
        add_option('apwp_max_photo_size', 2048); // em KB
    }

    /**
     * Cria as páginas padrão do plugin
     */
    private static function create_default_pages() {
        $pages = array(
            'pets' => array(
                'title' => __('Pets para Adoção', 'amigopet-wp'),
                'content' => '[apwp_pets_grid]',
                'order' => 1
            ),
            'adoption-form' => array(
                'title' => __('Formulário de Adoção', 'amigopet-wp'),
                'content' => '[apwp_adoption_form]',
                'order' => 2
            ),
            'adoption-process' => array(
                'title' => __('Processo de Adoção', 'amigopet-wp'),
                'content' => self::get_adoption_process_content(),
                'order' => 3
            ),
            'adoption-policy' => array(
                'title' => __('Política de Adoção', 'amigopet-wp'),
                'content' => self::get_adoption_policy_content(),
                'order' => 4
            ),
            'faq' => array(
                'title' => __('Perguntas Frequentes', 'amigopet-wp'),
                'content' => self::get_faq_content(),
                'order' => 5
            ),
            'terms' => array(
                'title' => __('Termos e Condições', 'amigopet-wp'),
                'content' => self::get_terms_content(),
                'order' => 6
            ),
            'adopter-area' => array(
                'title' => __('Área do Adotante', 'amigopet-wp'),
                'content' => '[apwp_adopter_area]',
                'order' => 7
            ),
            'about' => array(
                'title' => __('Sobre Nós', 'amigopet-wp'),
                'content' => self::get_about_content(),
                'order' => 8
            ),
            'contact' => array(
                'title' => __('Contato', 'amigopet-wp'),
                'content' => self::get_contact_content(),
                'order' => 9
            )
        );

        foreach ($pages as $slug => $page) {
            // Verifica se a página já existe
            $existing_page = get_page_by_path($slug);

            if (!$existing_page) {
                wp_insert_post(array(
                    'post_title' => $page['title'],
                    'post_content' => $page['content'],
                    'post_status' => 'publish',
                    'post_type' => 'page',
                    'post_name' => $slug,
                    'menu_order' => $page['order']
                ));
            }
        }

        // Criar menu principal se não existir
        $menu_name = 'AmigoPet Menu';
        $menu_exists = wp_get_nav_menu_object($menu_name);
        
        if (!$menu_exists) {
            $menu_id = wp_create_nav_menu($menu_name);
            
            // Adicionar páginas ao menu na ordem especificada
            foreach ($pages as $slug => $page) {
                $page_obj = get_page_by_path($slug);
                if ($page_obj) {
                    wp_update_nav_menu_item($menu_id, 0, array(
                        'menu-item-title' => $page['title'],
                        'menu-item-object' => 'page',
                        'menu-item-object-id' => $page_obj->ID,
                        'menu-item-type' => 'post_type',
                        'menu-item-status' => 'publish',
                        'menu-item-position' => $page['order']
                    ));
                }
            }

            // Definir como menu principal se o tema suportar
            if (get_theme_support('menus')) {
                $locations = get_theme_mod('nav_menu_locations');
                $locations['primary'] = $menu_id;
                set_theme_mod('nav_menu_locations', $locations);
            }
        }
    }

    /**
     * Conteúdo da página Processo de Adoção
     */
    private static function get_adoption_process_content() {
        return '
            <h2>' . __('Como Funciona o Processo de Adoção', 'amigopet-wp') . '</h2>
            
            <div class="adoption-steps">
                <div class="step">
                    <h3>1. ' . __('Escolha seu Pet', 'amigopet-wp') . '</h3>
                    <p>' . __('Navegue por nossa galeria de pets e encontre aquele que mais combina com você.', 'amigopet-wp') . '</p>
                </div>

                <div class="step">
                    <h3>2. ' . __('Preencha o Formulário', 'amigopet-wp') . '</h3>
                    <p>' . __('Complete nosso formulário de adoção com suas informações.', 'amigopet-wp') . '</p>
                </div>

                <div class="step">
                    <h3>3. ' . __('Entrevista', 'amigopet-wp') . '</h3>
                    <p>' . __('Faremos uma entrevista para conhecer melhor você e sua família.', 'amigopet-wp') . '</p>
                </div>

                <div class="step">
                    <h3>4. ' . __('Visita', 'amigopet-wp') . '</h3>
                    <p>' . __('Agende uma visita para conhecer o pet pessoalmente.', 'amigopet-wp') . '</p>
                </div>

                <div class="step">
                    <h3>5. ' . __('Documentação', 'amigopet-wp') . '</h3>
                    <p>' . __('Assinatura do termo de adoção e verificação de documentos.', 'amigopet-wp') . '</p>
                </div>

                <div class="step">
                    <h3>6. ' . __('Acompanhamento', 'amigopet-wp') . '</h3>
                    <p>' . __('Faremos visitas de acompanhamento para garantir o bem-estar do pet.', 'amigopet-wp') . '</p>
                </div>
            </div>

            [apwp_adoption_form]
        ';
    }

    /**
     * Conteúdo da página Política de Adoção
     */
    private static function get_adoption_policy_content() {
        return '
            <h2>' . __('Nossa Política de Adoção', 'amigopet-wp') . '</h2>
            
            <p>' . __('Para garantir o bem-estar dos nossos pets e o sucesso das adoções, estabelecemos algumas diretrizes importantes:', 'amigopet-wp') . '</p>

            <h3>' . __('Requisitos Básicos', 'amigopet-wp') . '</h3>
            <ul>
                <li>' . __('Ser maior de 18 anos', 'amigopet-wp') . '</li>
                <li>' . __('Apresentar documento de identidade e comprovante de residência', 'amigopet-wp') . '</li>
                <li>' . __('Ter concordância de todos os membros da família', 'amigopet-wp') . '</li>
                <li>' . __('Ter condições de arcar com os custos do pet', 'amigopet-wp') . '</li>
                <li>' . __('Residir em local adequado para o pet', 'amigopet-wp') . '</li>
            </ul>

            <h3>' . __('Compromissos do Adotante', 'amigopet-wp') . '</h3>
            <ul>
                <li>' . __('Manter o pet em ambiente seguro', 'amigopet-wp') . '</li>
                <li>' . __('Fornecer alimentação adequada', 'amigopet-wp') . '</li>
                <li>' . __('Garantir acompanhamento veterinário', 'amigopet-wp') . '</li>
                <li>' . __('Manter a vacinação em dia', 'amigopet-wp') . '</li>
                <li>' . __('Permitir visitas de acompanhamento', 'amigopet-wp') . '</li>
            </ul>
        ';
    }

    /**
     * Conteúdo da página FAQ
     */
    private static function get_faq_content() {
        return '
            <h2>' . __('Perguntas Frequentes', 'amigopet-wp') . '</h2>
            
            <div class="faq-section">
                <h3>' . __('Quanto custa adotar um pet?', 'amigopet-wp') . '</h3>
                <p>' . __('A adoção é gratuita, mas o adotante deve estar ciente dos custos de manutenção do pet.', 'amigopet-wp') . '</p>

                <h3>' . __('Posso devolver o pet?', 'amigopet-wp') . '</h3>
                <p>' . __('Sim, mas esperamos que isso seja evitado. Por isso fazemos uma avaliação criteriosa.', 'amigopet-wp') . '</p>

                <h3>' . __('Os pets são vacinados?', 'amigopet-wp') . '</h3>
                <p>' . __('Sim, todos os pets são entregues vacinados e vermifugados.', 'amigopet-wp') . '</p>

                <h3>' . __('Quanto tempo leva o processo?', 'amigopet-wp') . '</h3>
                <p>' . __('Em média, o processo leva de 1 a 2 semanas.', 'amigopet-wp') . '</p>

                <h3>' . __('Preciso ter experiência?', 'amigopet-wp') . '</h3>
                <p>' . __('Não é obrigatório, mas é importante estar disposto a aprender.', 'amigopet-wp') . '</p>
            </div>

            [apwp_pet_search]
        ';
    }

    /**
     * Conteúdo da página Termos
     */
    private static function get_terms_content() {
        return '
            <h2>' . __('Termos e Condições', 'amigopet-wp') . '</h2>
            
            <div class="terms-section">
                <h3>1. ' . __('Termos Gerais', 'amigopet-wp') . '</h3>
                <p>' . __('Ao utilizar nossa plataforma, você concorda com estes termos.', 'amigopet-wp') . '</p>

                <h3>2. ' . __('Responsabilidades', 'amigopet-wp') . '</h3>
                <p>' . __('O adotante se compromete a cuidar do pet com responsabilidade.', 'amigopet-wp') . '</p>

                <h3>3. ' . __('Privacidade', 'amigopet-wp') . '</h3>
                <p>' . __('Seus dados serão protegidos conforme nossa política de privacidade.', 'amigopet-wp') . '</p>

                <h3>4. ' . __('Direitos e Deveres', 'amigopet-wp') . '</h3>
                <p>' . __('Lista completa de direitos e deveres do adotante e da organização.', 'amigopet-wp') . '</p>
            </div>
        ';
    }

    /**
     * Conteúdo da página Sobre
     */
    private static function get_about_content() {
        return '
            <h2>' . __('Sobre o AmigoPet', 'amigopet-wp') . '</h2>
            
            <div class="about-section">
                <h3>' . __('Nossa Missão', 'amigopet-wp') . '</h3>
                <p>' . __('Conectar pets a lares amorosos, promovendo adoções responsáveis.', 'amigopet-wp') . '</p>

                <h3>' . __('Nossa História', 'amigopet-wp') . '</h3>
                <p>' . __('Começamos em 2023 com o objetivo de facilitar o processo de adoção.', 'amigopet-wp') . '</p>

                <h3>' . __('Nossos Valores', 'amigopet-wp') . '</h3>
                <ul>
                    <li>' . __('Respeito aos animais', 'amigopet-wp') . '</li>
                    <li>' . __('Transparência', 'amigopet-wp') . '</li>
                    <li>' . __('Responsabilidade', 'amigopet-wp') . '</li>
                    <li>' . __('Comprometimento', 'amigopet-wp') . '</li>
                </ul>
            </div>

            [apwp_pet_counter show="all" layout="grid"]
        ';
    }

    /**
     * Conteúdo da página Contato
     */
    private static function get_contact_content() {
        return '
            <h2>' . __('Entre em Contato', 'amigopet-wp') . '</h2>
            
            <div class="contact-section">
                <p>' . __('Estamos aqui para ajudar! Entre em contato conosco:', 'amigopet-wp') . '</p>

                <div class="contact-info">
                    <h3>' . __('Informações de Contato', 'amigopet-wp') . '</h3>
                    <ul>
                        <li>' . __('Email: contato@amigopet.org', 'amigopet-wp') . '</li>
                        <li>' . __('Telefone: (11) 1234-5678', 'amigopet-wp') . '</li>
                        <li>' . __('WhatsApp: (11) 98765-4321', 'amigopet-wp') . '</li>
                    </ul>
                </div>

                <div class="social-media">
                    <h3>' . __('Redes Sociais', 'amigopet-wp') . '</h3>
                    <ul>
                        <li>Instagram: @amigopet</li>
                        <li>Facebook: /amigopet</li>
                        <li>Twitter: @amigopet</li>
                    </ul>
                </div>
            </div>
        ';
    }

    /**
     * Adiciona uma mensagem ao log de ativação
     */
    private static function log_message($message) {
        self::$activation_messages[] = $message;
    }

    /**
     * Mostra a mensagem de ativação
     */
    public static function show_activation_message() {
        $message = '<div class="updated">';
        $message .= '<h3>AmigoPetWp - Log de Ativação</h3>';
        $message .= '<pre style="background: #f1f1f1; padding: 10px; border-radius: 4px;">';
        
        foreach (self::$activation_messages as $log) {
            $message .= htmlspecialchars($log) . "\n";
        }
        
        $message .= '</pre>';
        $message .= '<p>O plugin AmigoPetWp foi ativado com sucesso! ';
        $message .= '<a href="' . admin_url('admin.php?page=amigopet-wp-settings') . '">Ir para configurações</a></p>';
        $message .= '</div>';
        
        add_action('admin_notices', function() use ($message) {
            echo $message;
        });
    }

    /**
     * Método executado na desativação do plugin
     */
    public static function deactivate() {
        // Remover capabilities
        $admin = get_role('administrator');
        
        // Capabilities para pets
        $admin->remove_cap('create_pets');
        $admin->remove_cap('edit_pets');
        $admin->remove_cap('delete_pets');
        $admin->remove_cap('list_pets');
        
        // Capabilities para adoções
        $admin->remove_cap('manage_adoptions');
        $admin->remove_cap('approve_adoptions');
        
        // Capabilities para organizações
        $admin->remove_cap('manage_organizations');
        
        // Capabilities para termos
        $admin->remove_cap('manage_terms');
    }
}
