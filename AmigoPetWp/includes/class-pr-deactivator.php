<?php

/**
 * Acionado durante a desativação do plugin.
 *
 * Esta classe define tudo o que precisa acontecer durante
 * a desativação do plugin.
 *
 * @since      1.0.0
 * @package    AmigoPet_Wp
 * @subpackage AmigoPet_Wp/includes
 */
class APWP_Deactivator {

    /**
     * Método executado durante a desativação do plugin.
     *
     * Remove as configurações temporárias e limpa o cache.
     * Não remove dados permanentes como tabelas do banco de dados.
     *
     * @since    1.0.0
     */
    public static function deactivate() {
        // Remove as configurações temporárias
        delete_option('amigopet_wp_flush_rewrite_rules');
        
        // Limpa o cache de rewrite rules
        flush_rewrite_rules();
        
        // Remove os arquivos temporários
        self::cleanup_temp_files();
    }
    
    /**
     * Remove os arquivos temporários criados pelo plugin.
     *
     * @since    1.0.0
     */
    private static function cleanup_temp_files() {
        $upload_dir = wp_upload_dir();
        $temp_dir = $upload_dir['basedir'] . '/amigopet-wp-temp';
        
        if (file_exists($temp_dir)) {
            self::delete_directory($temp_dir);
        }
    }
    
    /**
     * Remove recursivamente um diretório e seu conteúdo.
     *
     * @since    1.0.0
     * @param    string    $dir    Caminho do diretório a ser removido.
     */
    private static function delete_directory($dir) {
        if (!file_exists($dir)) {
            return;
        }
        
        $files = array_diff(scandir($dir), array('.', '..'));
        
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            
            if (is_dir($path)) {
                self::delete_directory($path);
            } else {
                unlink($path);
            }
        }
        
        return rmdir($dir);
    }
}
