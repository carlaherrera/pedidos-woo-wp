<?php
namespace PedidosWoo\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Classe para lidar com a instalação e desinstalação do plugin.
 */
class Install {

    /**
     * Rotinas de ativação do plugin.
     * Cria as tabelas necessárias.
     */
    public static function activate() {
        global $wpdb;

        $table_name      = $wpdb->prefix . 'pwoo_pedidos_meta';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            order_id BIGINT(20) UNSIGNED NOT NULL,
            meta_key VARCHAR(255) NOT NULL,
            meta_value LONGTEXT,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY order_id (order_id)
        ) $charset_collate;";

        // Inclui o arquivo para usar a função dbDelta.
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );
    }
}
