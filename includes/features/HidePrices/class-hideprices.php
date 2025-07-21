<?php
namespace PedidosWoo\Includes\Features\HidePrices;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Sai se acessado diretamente.
}

/**
 * Classe HidePrices
 *
 * Gerencia a funcionalidade de exibir/ocultar valores (preços, totais, etc.)
 * em todo o site, incluindo frontend, backend (admin) e e-mails do WooCommerce.
 */
class HidePrices {

    /**
     * Construtor da classe.
     * Registra todos os hooks necessários para a funcionalidade.
     */
    public function __construct() {
        $this->init_hooks();
    }

    /**
     * Inicializa os hooks do WordPress e WooCommerce.
     */
    private function init_hooks() {
        // Frontend: Ocultar preços de produtos
        add_filter( 'woocommerce_variable_sale_price_html', [ $this, 'remove_prices' ], 10, 2 );
        add_filter( 'woocommerce_variable_price_html', [ $this, 'remove_prices' ], 10, 2 );
        add_filter( 'woocommerce_get_price_html', [ $this, 'remove_prices' ], 10, 2 );

        // Frontend: Ocultar preços no carrinho e checkout
        add_filter( 'woocommerce_checkout_cart_item_quantity', [ $this, 'remove_price_checkout' ], 10, 3 );
        add_filter( 'woocommerce_cart_item_price', [ $this, 'remove_price_checkout' ], 10, 3 );
        add_filter( 'woocommerce_cart_product_subtotal', '__return_empty_string', 10 );
        add_filter( 'woocommerce_cart_totals_order_total_html', '__return_empty_string', 10 );
        add_filter( 'woocommerce_cart_item_subtotal', '__return_empty_string', 10 );

        // Frontend: CSS para ocultar seções no carrinho e checkout
        add_action( 'wp_head', [ $this, 'add_frontend_css' ] );
        add_action( 'wp_footer', [ $this, 'add_frontend_css_footer' ] );

        // Backend (Admin): Ocultar campos de preço na edição de produtos
        add_action( 'admin_head', [ $this, 'add_admin_css' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'add_admin_inline_css' ] );

        // E-mails e Detalhes do Pedido
        add_filter( 'woocommerce_email_order_items_args', [ $this, 'remove_fields_email_order_items_args' ] );
        add_filter( 'woocommerce_get_order_item_totals', [ $this, 'remove_order_item_totals' ], 10, 3 );
        add_filter( 'woocommerce_get_formatted_order_total', '__return_empty_string', 10, 2 );
        add_filter( 'woocommerce_order_item_subtotal', '__return_empty_string', 10, 3 );
        add_filter( 'woocommerce_order_formatted_line_subtotal', '__return_empty_string', 10, 3 );
        add_filter( 'woocommerce_order_item_display_meta_key', [ $this, 'remove_order_item_display_meta_key' ], 10, 3 );
        add_filter( 'woocommerce_order_item_get_formatted_meta_data', [ $this, 'remove_order_item_table_total' ], 10, 2 );
        add_action( 'woocommerce_email_order_details', [ $this, 'remove_email_order_details_totals' ], 10, 4 );
        add_filter( 'woocommerce_get_order_item_totals', [ $this, 'remove_order_totals' ], 10, 3 );

        // Comportamento de compra para produtos sem preço
        add_filter( 'woocommerce_is_purchasable', '__return_true' );
        add_filter( 'woocommerce_variation_is_purchasable', '__return_true' );
        add_filter( 'woocommerce_product_get_price', [ $this, 'set_zero_price_for_free_products' ], 10, 2 );
        add_filter( 'woocommerce_product_variation_get_price', [ $this, 'set_zero_price_for_free_products' ], 10, 2 );

        // Outros ajustes
        add_filter( 'woocommerce_get_availability', [ $this, 'remove_availability_message' ], 10, 2 );
        add_filter( 'woocommerce_cart_needs_shipping_address', '__return_false' );

        // Adiciona imagem do produto nos detalhes do pedido (mantido do seu código original, mas pode ser movido se não for relacionado a preços)
        add_action( 'woocommerce_order_item_meta_start', [ $this, 'add_custom_product_image_to_order_items' ], 10, 3 );
    }

    /**
     * Remove o preço de exibição de produtos variáveis e simples.
     *
     * @param string   $price   O preço formatado.
     * @param WC_Product $product O objeto do produto.
     * @return string Uma string vazia para ocultar o preço.
     */
    public function remove_prices( $price, $product ) {
        return '';
    }

    /**
     * Remove o preço e a quantidade do item no checkout e carrinho.
     *
     * @param string $item_qty    A quantidade do item.
     * @param array  $cart_item   O item do carrinho.
     * @param string $cart_item_key A chave do item do carrinho.
     * @return string Uma string vazia.
     */
    public function remove_price_checkout( $item_qty, $cart_item, $cart_item_key ) {
        return '';
    }

    /**
     * Adiciona CSS inline ao frontend para ocultar seções de preço no carrinho e checkout.
     */
    public function add_frontend_css() {
        if ( is_cart() || is_checkout() ) {
            $custom_css = "
                /* Esconde os preços e subtotais de cada produto no carrinho */
                .woocommerce-cart-form .product-price,
                .woocommerce-cart-form .product-subtotal,
                /* Esconde os campos de subtotal e total no resumo de valores */
                .cart_totals .cart-subtotal,
                .cart_totals .order-total,
                /* Esconde o cabeçalho 'Resumo de Valores' */
                .cart_totals h2,
                /* Esconde o total do produto na tabela de revisão do checkout */
                .woocommerce-checkout-review-order-table .product-total {
                    display: none !important;
                }
            ";
            wp_add_inline_style( 'woocommerce-general', $custom_css );
        }
    }

    /**
     * Adiciona CSS inline ao footer do frontend para ocultar elementos específicos no checkout e na página de agradecimento.
     */
    public function add_frontend_css_footer() {
        // Oculta total e entrega no checkout
        if ( is_checkout() ) {
            echo '<style>.order-total, .shipping, .cart-subtotal {display: none !important;}</style>';
        }
        // Oculta total do pedido na página de confirmação de pedido
        if ( is_wc_endpoint_url( 'order-received' ) ) {
            echo '<style>.woocommerce-order-overview__total.total, .woocommerce-table__product-table.product-total { display: none !important; }</style>';
        }
    }

    /**
     * Adiciona CSS inline ao admin para ocultar campos de preço na edição de produtos.
     */
    public function add_admin_css() {
        global $pagenow, $post;

        // Oculta campos de preço na tela de edição de produto (simples e variável)
        if ( ( in_array( $pagenow, array( 'post-new.php', 'post.php' ) ) && get_post_type() == 'product' ) ) {
            echo '<style>
                #general_product_data .pricing,
                .woocommerce_variation .form-row .form-field._regular_price_field,
                .woocommerce_variation .form-row .form-field._sale_price_field,
                .woocommerce_variation .form-row .form-field._sale_price_dates_fields {
                    display: none !important;
                }
            </style>';
        }
    }

    /**
     * Adiciona CSS inline para ocultar campos de preço de variações de produtos no admin.
     * Enfileirado via `admin_enqueue_scripts` para garantir que os estilos do WooCommerce estejam carregados.
     */
    public function add_admin_inline_css() {
        $custom_css = "
            .woocommerce_variable_attributes .variable_pricing,
            .woocommerce_variable_attributes .variable_regular_price,
            .woocommerce_variable_attributes .variable_sale_price {
                display: none !important;
            }
        ";
        wp_add_inline_style( 'woocommerce_admin_styles', $custom_css );
    }

    /**
     * Ajusta os argumentos para exibição de itens em e-mails de pedido.
     * Define 'show_price', 'show_subtotal', 'show_total', 'show_shipping' como false.
     *
     * @param array $args Argumentos para exibição de itens.
     * @return array Argumentos modificados.
     */
    public function remove_fields_email_order_items_args( $args ) {
        $args['show_purchase_note'] = true; // Mantém a nota de compra
        $args['show_sku']           = false; // Oculta SKU
        $args['show_image']         = false; // Oculta imagem
        $args['image_size']         = array( 32, 32 ); // Tamanho da imagem (mesmo que oculto)
        $args['show_price']         = false; // Oculta preço
        $args['show_subtotal']      = false; // Oculta subtotal
        $args['show_total']         = false; // Oculta total
        $args['show_shipping']      = false; // Oculta entrega
        return $args;
    }

    /**
     * Filtra os totais de itens do pedido para remover linhas que fazem referência a valores.
     *
     * @param array    $totals    Array de totais do pedido.
     * @param WC_Order $order     Objeto do pedido.
     * @param string   $tax_display Tipo de exibição de imposto.
     * @return array Totais modificados.
     */
    public function remove_order_item_totals( $totals, $order, $tax_display ) {
        $fields_to_unset = array( 'cart_subtotal', 'order_total', 'shipping', 'tax', 'payment_method' );

        foreach ( $fields_to_unset as $field ) {
            if ( isset( $totals[ $field ] ) ) {
                unset( $totals[ $field ] );
            }
        }
        return $totals;
    }

    /**
     * Remove a chave 'total' da exibição de metadados do item do pedido.
     *
     * @param string $display_key A chave de exibição.
     * @param object $meta        Objeto de metadados.
     * @param object $item        Objeto do item do pedido.
     * @return string Chave de exibição modificada.
     */
    public function remove_order_item_display_meta_key( $display_key, $meta, $item ) {
        if ( $display_key === 'total' ) {
            return '';
        }
        return $display_key;
    }

    /**
     * Remove metadados de subtotal e total de linha da exibição formatada do item do pedido.
     *
     * @param array  $formatted_meta Metadados formatados.
     * @param object $item           Objeto do item do pedido.
     * @return array Metadados formatados modificados.
     */
    public function remove_order_item_table_total( $formatted_meta, $item ) {
        foreach( $formatted_meta as $key => $meta ) {
            if ( in_array( $meta->key, ['_line_subtotal', '_line_total'] ) ) {
                unset( $formatted_meta[$key] );
            }
        }
        return $formatted_meta;
    }

    /**
     * Garante que os filtros de remoção de totais sejam aplicados em e-mails do WooCommerce.
     *
     * @param WC_Order $order       Objeto do pedido.
     * @param bool     $sent_to_admin Se o e-mail foi enviado para o admin.
     * @param bool     $plain_text  Se o e-mail é em texto puro.
     * @param WC_Email $email       Objeto do e-mail.
     */
    public function remove_email_order_details_totals( $order, $sent_to_admin, $plain_text, $email ) {
        // Re-aplica os filtros para garantir que funcionem dentro do contexto do e-mail
        add_filter( 'woocommerce_get_order_item_totals', [ $this, 'remove_order_item_totals' ], 10, 3 );
        add_filter( 'woocommerce_get_formatted_order_total', '__return_empty_string', 10, 2 );
    }

    /**
     * Remove o total do pedido e subtotal dos totais exibidos.
     *
     * @param array    $totals    Array de totais do pedido.
     * @param WC_Order $order     Objeto do pedido.
     * @param string   $tax_display Tipo de exibição de imposto.
     * @return array Totais modificados.
     */
    public function remove_order_totals( $totals, $order, $tax_display ) {
        unset( $totals['order_total'] );
        unset( $totals['cart_subtotal'] );
        // Você pode descomentar e adicionar outros elementos se necessário:
        // unset($totals['shipping']);
        // unset($totals['tax']);
        return $totals;
    }

    /**
     * Define o preço de produtos sem preço como zero para permitir a compra.
     *
     * @param string   $price   O preço atual do produto.
     * @param WC_Product $product O objeto do produto.
     * @return string O preço modificado (0 se estiver vazio).
     */
    public function set_zero_price_for_free_products( $price, $product ) {
        if ( empty( $price ) ) {
            return '0';
        }
        return $price;
    }

    /**
     * Remove a mensagem de disponibilidade do produto (ex: 'Em estoque', 'Fora de estoque').
     *
     * @param array      $availability Array de disponibilidade.
     * @param WC_Product $product      Objeto do produto.
     * @return array Array de disponibilidade modificado.
     */
    public function remove_availability_message( $availability, $product ) {
        $availability['availability'] = '';
        return $availability;
    }

    /**
     * Adiciona uma imagem personalizada do produto aos itens do pedido (se o produto existir).
     * Esta função foi mantida do seu código original. Se não for diretamente relacionada
     * à ocultação de preços, pode ser movida para uma funcionalidade separada no futuro.
     *
     * @param int      $item_id ID do item do pedido.
     * @param WC_Order_Item_Product $item Objeto do item do pedido.
     * @param WC_Order $order Objeto do pedido.
     */
    public function add_custom_product_image_to_order_items( $item_id, $item, $order ) {
        $product = $item->get_product();
        if ( $product ) {
            $image_url = wp_get_attachment_image_url( $product->get_image_id(), [ 50, 50 ] );
            echo '<div class="custom-product-image" style="float: left; margin-right: 10px;"><img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $product->get_name() ) . '" style="width: 50px; height: 50px;"></div>';
        }
    }
}
