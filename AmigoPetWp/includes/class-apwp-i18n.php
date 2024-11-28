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
        $domain = 'amigopet-wp';
        $locale = apply_filters('plugin_locale', determine_locale(), $domain);
        $mofile = $domain . '-' . $locale . '.mo';

        // Tenta carregar do diretório languages do plugin
        $mopath = dirname(dirname(plugin_basename(__FILE__))) . '/languages/';
        $loaded = load_textdomain($domain, WP_PLUGIN_DIR . '/' . $mopath . $mofile);

        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('AmigoPet WP - Loading textdomain:');
            error_log('Locale: ' . $locale);
            error_log('MO file: ' . $mofile);
            error_log('MO path: ' . WP_PLUGIN_DIR . '/' . $mopath . $mofile);
            error_log('Loaded: ' . ($loaded ? 'true' : 'false'));
        }

        // Se falhou, tenta carregar do diretório languages do WordPress
        if (!$loaded) {
            load_plugin_textdomain(
                $domain,
                false,
                $mopath
            );
        }
    }
}
