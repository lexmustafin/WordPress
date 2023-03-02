<?php

namespace CustomProductBuilderWC;

final class Product {

    private static $_instance;

    /**
     * Gets Instance
     * @return Init
     * @version 1.0
     * @since 1.0
     */
    public static function get_instance()
    {
        if ( self::$_instance == null )
            self::$_instance = new self();

        return self::$_instance;
    }

    /**
     * Init constructor.
     * @version 1.0
     * @since 1.0
     */
    public function __construct()
    {
        $this->add_actions();
    }

    /**
     * Add Actions
     * @version 1.0
     * @since 1.0
     */
    public function add_actions()
    {
        add_action( 'wp_head', [ $this, 'load_designer' ] );

        add_action( 'wp_ajax_cpbwc-save-canvas', [ $this, 'save_canvas' ] );

        add_action( 'wp_ajax_nopriv_cpbwc-save-canvas', [ $this, 'save_canvas' ] );

        add_action( 'init', [ $this, 'set_cookie' ] );

        add_filter( 'woocommerce_cart_item_thumbnail', [ $this, 'get_designed_product_image' ], 10, 3 );

        add_action( 'woocommerce_checkout_order_created', [ $this, 'order_design' ] );

        add_action( 'woocommerce_remove_cart_item', [ $this, 'update_cart' ], 10, 2 );

    }

    /**
     * Loads Builder on product
     * @version 1.0
     * @since 1.0
     */
    public function load_designer()
    {
        if( !cpbwc_is_enabled() )
            return;

        \CustomProductBuilderWC\ProductBuilder::load_front_scripts();

        add_filter( 'woocommerce_single_product_image_thumbnail_html', [ $this, 'render_designer' ], 10, 2 );

    }

    /**
     * Renders builder
     * @param $html
     * @param $post_thumbnail_id
     * @version 1.0
     * @since 1.0
     * @return string
     */
    public function render_designer( $html, $post_thumbnail_id )
    {
        $content = '';

        $product_id = get_the_ID();

        if( !cpbwc_is_product_in_cart( $product_id ) )
        {
            do_action( 'cpbwc_ac_before_designer_html' );

            $content .= '<div id="product-designer"></div>';
        }
        else
        {
            $image_url = cpbwc_get_image_url( $product_id );

            $content .= "<img src='{$image_url}' />";
        }

        return apply_filters( 'cpbwc_f_designer_html', $content );
    }

    /**
     * Saves Designed Product
     * @since 1.0
     * @version 1.0
     */
    public function save_canvas()
    {
        //check_ajax_referer( 'cpbwc-security', 'token' );

        if( isset( $_POST['action'] ) && $_POST['action'] == 'cpbwc-save-canvas' )
        {
            $product_id = sanitize_text_field( $_POST['product_id'] );

            if( !cpbwc_is_enabled( $product_id ) )
                return;

            $image_url = sanitize_text_field( $_POST['image_url'] );

            $cart_hash = cpbwc_get_cart_hash();

            cpbwc_save_cart( $product_id );

            $upload_dir = trailingslashit( wp_upload_dir()['basedir'] ) . 'cpbwc';

            wp_mkdir_p( $upload_dir );

            $upload_dir = wp_upload_dir();

            $upload_path = str_replace( '/', DIRECTORY_SEPARATOR, $upload_dir['basedir'] ) . DIRECTORY_SEPARATOR . '/cpbwc/';

            $filename = "{$cart_hash}-{$product_id}.jpeg";
            $img = str_replace( 'data:image/png;base64,', '', $image_url );
            $img = str_replace( ' ', '+', $img );
            $decoded = base64_decode( $img );


            // Save the image in the uploads directory.
            $upload_file = file_put_contents( $upload_path . $filename, $decoded );

            if( $upload_file )
            {
                wp_send_json_success( array(), 200 );
            }
            else
            {
                wp_send_json_error( array(), 403 );
            }
        }
    }

    /**
     * Sets Cookie
     * @since 1.0
     * @version 1.0
     */
    public function set_cookie()
    {
        if ( !isset( $_COOKIE['cpbwc_cart'] ) )
        {
            $cart_hash = wp_generate_password( 12, false, false );

            setcookie( 'cpbwc_cart', $cart_hash, time() + 86400, '/' );
        }
    }

    /**
     * Gets desgined product image on cart
     * @since 1.0
     * @version 1.0
     */
    public function get_designed_product_image( $image, $cart_item, $cart_item_key )
    {
        $is_enabled = cpbwc_is_enabled( $cart_item['product_id'] );

        if ( $is_enabled )
        {
            $image_url = cpbwc_get_image_url( $cart_item['product_id'] );

            $image = "<img src='{$image_url}' />";
        }

        return $image;
    }


    /**
     * Orders the designed product
     * @param $order
     * @throws \Exception
     * @since 1.0
     * @version 1.0
     */
    public function order_design($order )
    {
        $items = $order->get_items();

        foreach ( $items as $item_id => $item )
        {
            $product_id = $item->get_product_id();

            if( cpbwc_is_enabled( $product_id ) )
            {
                wc_add_order_item_meta( $item_id, 'cpbwc_order', cpbwc_get_cart_hash() );

                cpbwc_clear_cart_cookie();
            }
        }
    }

    /**
     * Clears product from cart
     * @param $cart_item_key
     * @param $cart
     * @throws \Exception
     * @since 1.0
     * @version 1.0
     */
    public function update_cart( $cart_item_key, $cart )
    {
        $product_id = $cart->cart_contents[ $cart_item_key ]['product_id'];

        if( cpbwc_is_enabled( $product_id ) )
        {
            cpbwc_clear_cart_cookie();
        }
    }
}

\CustomProductBuilderWC\Product::get_instance();
