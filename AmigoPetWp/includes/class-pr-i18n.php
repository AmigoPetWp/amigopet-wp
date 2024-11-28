<?php

/**
 * Define a funcionalidade de internacionalização.
 *
 * Carrega e define o domínio de texto do plugin para internacionalização.
 *
 * @since      1.0.0
 * @package    AmigoPet_Wp
 * @subpackage AmigoPet_Wp/includes
 */
class APWP_i18n {

    /**
     * Carrega o domínio de texto do plugin para tradução.
     *
     * @since    1.0.0
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain(
            'amigopet-wp',
            false,
            dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );
    }
}
