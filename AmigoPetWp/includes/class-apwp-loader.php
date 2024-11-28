<?php

/**
 * Registra todas as ações e filtros para o plugin.
 *
 * Mantém uma lista de todos os hooks que são registrados em todo
 * o plugin e os registra com o WordPress API. Chama a função
 * run para executar a lista de ações e filtros.
 *
 * @package    AmigoPet_Wp
 * @subpackage AmigoPet_Wp/includes
 */
class APWP_Loader {

    /**
     * O array de ações registradas com WordPress.
     *
     * @since    1.0.0
     * @access   protected
     * @var      array    $actions    As ações registradas com WordPress para execução.
     */
    protected $actions;

    /**
     * O array de filtros registrados com WordPress.
     *
     * @since    1.0.0
     * @access   protected
     * @var      array    $filters    Os filtros registrados com WordPress para execução.
     */
    protected $filters;

    /**
     * Inicializa as coleções usadas para manter as ações e filtros.
     *
     * @since    1.0.0
     */
    public function __construct() {
        $this->actions = array();
        $this->filters = array();
    }

    /**
     * Adiciona uma nova ação à coleção para ser registrada com WordPress.
     *
     * @since    1.0.0
     * @param    string    $hook             O nome da ação do WordPress que está sendo registrada.
     * @param    object    $component        Uma referência à instância do objeto no qual a ação é definida.
     * @param    string    $callback         O nome da função definida no $component.
     * @param    int       $priority         Opcional. A prioridade em que a função deve ser disparada. Default é 10.
     * @param    int       $accepted_args    Opcional. O número de argumentos que devem ser passados para o callback. Default é 1.
     */
    public function add_action($hook, $component, $callback, $priority = 10, $accepted_args = 1) {
        $this->actions = $this->add($this->actions, $hook, $component, $callback, $priority, $accepted_args);
    }

    /**
     * Adiciona um novo filtro à coleção para ser registrado com WordPress.
     *
     * @since    1.0.0
     * @param    string    $hook             O nome do filtro do WordPress que está sendo registrado.
     * @param    object    $component        Uma referência à instância do objeto no qual o filtro é definido.
     * @param    string    $callback         O nome da função definida no $component.
     * @param    int       $priority         Opcional. A prioridade em que a função deve ser disparada. Default é 10.
     * @param    int       $accepted_args    Opcional. O número de argumentos que devem ser passados para o callback. Default é 1
     */
    public function add_filter($hook, $component, $callback, $priority = 10, $accepted_args = 1) {
        $this->filters = $this->add($this->filters, $hook, $component, $callback, $priority, $accepted_args);
    }

    /**
     * Uma função utilitária que é usada para registrar as ações e hooks em uma única coleção.
     *
     * @since    1.0.0
     * @access   private
     * @param    array     $hooks            A coleção de hooks que está sendo registrada (ações ou filtros).
     * @param    string    $hook             O nome do filtro do WordPress que está sendo registrado.
     * @param    object    $component        Uma referência à instância do objeto no qual o filtro é definido.
     * @param    string    $callback         O nome da função definida no $component.
     * @param    int       $priority         A prioridade em que a função deve ser disparada.
     * @param    int       $accepted_args    O número de argumentos que devem ser passados para o callback.
     * @return   array                       A coleção de ações e filtros registrados com WordPress.
     */
    private function add($hooks, $hook, $component, $callback, $priority, $accepted_args) {
        $hooks[] = array(
            'hook'          => $hook,
            'component'     => $component,
            'callback'      => $callback,
            'priority'      => $priority,
            'accepted_args' => $accepted_args
        );

        return $hooks;
    }

    /**
     * Registra os filtros e ações com WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        foreach ($this->filters as $hook) {
            add_filter($hook['hook'], array($hook['component'], $hook['callback']), $hook['priority'], $hook['accepted_args']);
        }

        foreach ($this->actions as $hook) {
            add_action($hook['hook'], array($hook['component'], $hook['callback']), $hook['priority'], $hook['accepted_args']);
        }
    }
}
