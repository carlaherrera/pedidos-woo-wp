<?php
/**
 * Arquivo de desinstalação do Pedidos Woo.
 *
 * Este arquivo é executado quando o usuário exclui o plugin.
 *
 * @package PedidosWoo
 */

// Se o arquivo não for chamado pelo WordPress, saia.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

global $wpdb;

$table_name = $wpdb->prefix . 'pwoo_pedidos_meta';

// Exclui a tabela do banco de dados.
$wpdb->query( "DROP TABLE IF EXISTS {$table_name}" );
