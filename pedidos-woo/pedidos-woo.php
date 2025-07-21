<?php
/**
 * Plugin Name:       Pedidos Woo
 * Plugin URI:        https://example.com/
 * Description:       Gerencia pedidos do WooCommerce com funcionalidades extras.
 * Version:           1.0.0
 * Author:            Carla Herrera
 * Author URI:        https://example.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       pedidos-woo
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Inclui o autoloader de classes
require_once plugin_dir_path( __FILE__ ) . 'includes/class-autoloader.php';

/**
 * A classe principal do plugin.
 */
final class PedidosWoo {

    /**
     * A única instância da classe.
     *
     * @var PedidosWoo
     */
    private static $instance;

    /**
     * Garante que apenas uma instância da classe seja carregada.
     *
     * @return PedidosWoo - A instância principal.
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Construtor.
     */
    private function __construct() {
        $this->init();
    }

    /**
     * Inicializa o plugin.
     */
    public function init() {
        // Carrega as classes de administração
        if ( is_admin() ) {
            new PedidosWoo\Admin\Menu();
            new PedidosWoo\Admin\Settings(); // Inicializa a página de configurações.
            // Inicializa a funcionalidade de ocultar preços apenas se a opção estiver habilitada.
            $options = get_option( 'pedidos_woo_options' );
            if ( isset( $options['hide_prices'] ) && $options['hide_prices'] ) {
                new PedidosWoo\Includes\Features\HidePrices\HidePrices();
            }
        }
    }
}

/**
 * Função principal para retornar a instância da classe PedidosWoo.
 *
 * @return PedidosWoo
 */
function PedidosWoo() {
    return PedidosWoo::instance();
}

// Hook de ativação
register_activation_hook( __FILE__, [ 'PedidosWoo\Includes\Install', 'activate' ] );

// Inicia o plugin.
PedidosWoo();
