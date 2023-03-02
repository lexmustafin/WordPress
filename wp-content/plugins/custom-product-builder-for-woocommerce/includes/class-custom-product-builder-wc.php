<?php

namespace CustomProductBuilderWC;

final class Init {

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
        $this->init();
    }

    /**
     * Initializes plugin
     * @version 1.0
     * @since 1.0
     */
    public function init()
    {
        if ( in_array( 'woocommerce/woocommerce.php', get_option( 'active_plugins' ) ) )
        {
            $this->run();
        }
        else
        {
            add_action( 'admin_notices', [ $this, 'require_notice' ] );
        }
    }

    /**
     * Requires Notice
     * @version 1.0
     * @since 1.0
     */
    public function require_notice()
    {
        ?>
        <div class="notice notice-error">
            <p><?php esc_attr_e( 'In order to user Custom Product Builder, Make sure you\'ve', 'cpbwc' );?> <a href="https://wordpress.org/plugins/woocommerce/"><?php esc_attr_e( 'WooCommerce', 'cpbwc' ); ?></a> <?php esc_attr_e( 'Installed and Activated.', 'cpbwc' ); ?></p>
        </div>
        <?php
    }

    /**
     * Runs Plugin
     * @version 1.0
     * @since 1.0
     */
    public function run()
    {
        $this->add_actions();

        require_once dirname( CPBWC_PLUGIN_FILE ) . '/includes/functions.php';
        require_once dirname( CPBWC_PLUGIN_FILE ) . '/includes/class-scripts.php';
        require_once dirname( CPBWC_PLUGIN_FILE ) . '/includes/class-product.php';
        require_once dirname( CPBWC_PLUGIN_FILE ) . '/includes/class-product-builder.php';

        if( is_admin() )
        {
            require_once dirname( CPBWC_PLUGIN_FILE ) . '/includes/class-admin.php';
            require_once dirname( CPBWC_PLUGIN_FILE ) . '/includes/class-order.php';
        }
    }

    /**
     * Add Actions
     * @version 1.0
     * @since 1.0
     */
    public function add_actions()
    {
        add_action( 'init', [ $this, 'load_textdomain' ] );
    }

    /**
     * Load textdomain
     * @version 1.0
     * @since 1.0
     */
    public function load_textdomain()
    {
        load_plugin_textdomain( 'cpbwc', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    }
}
