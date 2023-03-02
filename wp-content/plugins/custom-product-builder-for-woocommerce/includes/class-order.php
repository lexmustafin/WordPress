<?php

namespace CustomProductBuilderWC;

class Order {

    private static $_instance;

    /**
     * Gets Instance
     * @return Admin
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
        add_filter( 'woocommerce_admin_order_item_thumbnail', [ $this, 'ordered_image' ], 10, 3 );
    }

    /**
     * Show Ordered image to admin
     * @param $image
     * @param $item_id
     * @param $item
     * @since 1.0
     * @version 1.0
     */
    public function ordered_image( $image, $item_id, $item )
    {
        $product_id = $item->get_product_id();

        if ( cpbwc_is_enabled( $product_id ) )
        {
           if( wc_get_order_item_meta( $item_id, 'cpbwc_order' ) )
           {
               add_filter( 'woocommerce_admin_html_order_item_class', [ $this, 'add_class' ] );

               $hash = wc_get_order_item_meta( $item_id, 'cpbwc_order' );

               $image = cpbwc_get_image_url( $product_id, $hash );

               $image = "<img src='{$image}' />";
           }
        }

        return $image;
    }

    /**
     * Adds class to designed product
     * @since 1.0
     * @version 1.0
     */
    public function add_class()
    {
        return 'cpbwc-zoom';
    }
}

\CustomProductBuilderWC\Order::get_instance();
