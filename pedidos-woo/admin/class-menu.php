<?php
namespace PedidosWoo\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Classe para gerenciar o menu do admin.
 */
class Menu {

    /**
     * Construtor.
     */
    public function __construct() {
        add_action( 'admin_menu', [ $this, 'register_menu' ] );
    }

    /**
     * Registra a página do menu principal.
     */
    public function register_menu() {
        $main_page = add_menu_page(
            __( 'Pedidos Woo', 'pedidos-woo' ),
            __( 'Pedidos Woo', 'pedidos-woo' ),
            'manage_options',
            'pedidos-woo',
            [ $this, 'render_page' ],
            'dashicons-cart',
            56
        );

        // Carrega os scripts apenas na nossa página
        add_action( 'admin_print_styles-' . $main_page, [ $this, 'enqueue_styles' ] );
    }

    /**
     * Renderiza o conteúdo da página.
     */
    public function render_page() {
        ?>
        <div class="wrap">
            <h1 class="pwoo-text-2xl pwoo-font-bold pwoo-mb-4"><?php _e( 'Painel Pedidos Woo', 'pedidos-woo' ); ?></h1>
            <div class="pwoo-bg-white pwoo-p-6 pwoo-rounded-lg pwoo-shadow-md">
                <p class="pwoo-text-gray-700">
                    <?php _e( 'Bem-vindo à página de configuração do Pedidos Woo. Use o menu à esquerda para navegar pelas funcionalidades.', 'pedidos-woo' ); ?>
                </p>
            </div>
        </div>
        <?php
    }

    /**
     * Carrega os arquivos de estilo.
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            'pedidos-woo-admin-style',
            plugin_dir_url( __DIR__ ) . 'assets/css/admin-style.css',
            [],
            '1.0.0'
        );
    }
}
