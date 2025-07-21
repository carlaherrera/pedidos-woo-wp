<?php
/**
 * Autoloader de classes para o plugin.
 *
 * @package PedidosWoo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

spl_autoload_register( function ( $class ) {
    // Apenas carrega classes do nosso namespace
    $prefix = 'PedidosWoo\\';
    if ( strpos( $class, $prefix ) !== 0 ) {
        return;
    }

    // Remove o prefixo do namespace
    $class_name = str_replace( $prefix, '', $class );

    // Converte o nome da classe para o nome do arquivo (ex: Admin\Menu -> admin/class-menu.php)
    $file_parts = explode( '\\', strtolower( $class_name ) );
    $file_name  = 'class-' . array_pop( $file_parts ) . '.php';
    $file_path  = implode( '/', $file_parts );
    $file_path  = ! empty( $file_path ) ? $file_path . '/' : '';

    $full_path = plugin_dir_path( __DIR__ ) . $file_path . $file_name;

    if ( file_exists( $full_path ) ) {
        require_once $full_path;
    }
} );
