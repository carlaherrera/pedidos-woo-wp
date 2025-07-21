<?php
namespace PedidosWoo\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Classe para gerenciar as configurações do plugin.
 */
class Settings {

    /**
     * Construtor da classe.
     */
    public function __construct() {
        add_action( 'admin_init', [ $this, 'register_settings' ] );
        add_action( 'admin_menu', [ $this, 'add_settings_page' ] );
    }

    /**
     * Adiciona a página de configurações ao menu do WordPress.
     */
    public function add_settings_page() {
        add_submenu_page(
            'pedidos-woo', // Slug do menu pai (o menu principal do seu plugin)
            __( 'Configurações do Plugin', 'pedidos-woo' ),
            __( 'Configurações', 'pedidos-woo' ),
            'manage_options',
            'pedidos-woo-settings',
            [ $this, 'render_settings_page' ]
        );
    }

    /**
     * Renderiza o conteúdo da página de configurações.
     */
    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php _e( 'Configurações do Pedidos Woo', 'pedidos-woo' ); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'pedidos_woo_settings_group' );
                do_settings_sections( 'pedidos-woo-settings' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Registra as configurações, seções e campos do plugin.
     */
    public function register_settings() {
        // Registra o grupo de configurações
        register_setting(
            'pedidos_woo_settings_group', // Nome do grupo de configurações
            'pedidos_woo_options',        // Nome da opção no banco de dados
            [ $this, 'sanitize_options' ]  // Função de sanitização
        );

        // Adiciona uma seção de configurações
        add_settings_section(
            'pedidos_woo_general_section', // ID da seção
            __( 'Configurações Gerais', 'pedidos-woo' ), // Título da seção
            null, // Callback para descrição da seção (null se não houver)
            'pedidos-woo-settings' // Página onde a seção será exibida
        );

        // Adiciona um campo de configuração (checkbox para ocultar preços)
        add_settings_field(
            'pedidos_woo_hide_prices', // ID do campo
            __( 'Ocultar Preços', 'pedidos-woo' ), // Título do campo
            [ $this, 'render_hide_prices_field' ], // Callback para renderizar o campo
            'pedidos-woo-settings', // Página onde o campo será exibido
            'pedidos_woo_general_section' // Seção à qual o campo pertence
        );
    }

    /**
     * Renderiza o campo de checkbox para ocultar preços.
     */
    public function render_hide_prices_field() {
        $options = get_option( 'pedidos_woo_options' );
        $checked = isset( $options['hide_prices'] ) ? checked( 1, $options['hide_prices'], false ) : '';
        ?>
        <input type="checkbox" name="pedidos_woo_options[hide_prices]" value="1" <?php echo $checked; ?> />
        <label for="pedidos_woo_options[hide_prices]"><?php _e( 'Habilitar a funcionalidade de ocultar preços em todo o site.', 'pedidos-woo' ); ?></label>
        <?php
    }

    /**
     * Sanitiza as opções do plugin antes de salvar no banco de dados.
     * @param array $input As opções enviadas pelo formulário.
     * @return array As opções sanitizadas.
     */
    public function sanitize_options( $input ) {
        $new_input = [];
        if ( isset( $input['hide_prices'] ) ) {
            $new_input['hide_prices'] = (int) $input['hide_prices'];
        }
        return $new_input;
    }
}
