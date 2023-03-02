<?php
/**
 * Plugin Name: Custom Product Builder For WooCommerce
 * Plugin URI: https://twitter.com/CoderPress
 * Author: CoderPress
 * Description: Custom Product Builder For WooCommerce, Let customer design there own product.
 * Version: 1.0.3
 * Author: CoderPress
 * License: GPL v2 or later
 * Stable tag: 1.0.3
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Tags: woocommerce, product builder, custom, designer, customized
 * @author CoderPress
 * @url https://twitter.com/CoderPress
 */

if ( ! function_exists( 'cpbfw_fs' ) ) {
    // Create a helper function for easy SDK access.
    function cpbfw_fs() {
        global $cpbfw_fs;

        if ( ! isset( $cpbfw_fs ) ) {
            // Include Freemius SDK.
            require_once dirname(__FILE__) . '/freemius/start.php';

            $cpbfw_fs = fs_dynamic_init( array(
                'id'                  => '10782',
                'slug'                => 'custom-product-builder-for-woocommerce',
                'type'                => 'plugin',
                'public_key'          => 'pk_e669ea0b2d0e2b820d37e67a6f940',
                'is_premium'          => false,
                'has_addons'          => false,
                'has_paid_plans'      => false,
                'menu'                => array(
                    'account'        => false,
                    'support'        => false,
                ),
            ) );
        }

        return $cpbfw_fs;
    }

    // Init Freemius.
    cpbfw_fs();
    // Signal that SDK was initiated.
    do_action( 'cpbfw_fs_loaded' );
}

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'CPBWC_PLUGIN_FILE' ) ) {
    define( 'CPBWC_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'CPBWC_PLUGIN_URL' ) ) {
    define( 'CPBWC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

require dirname( CPBWC_PLUGIN_FILE ) . '/includes/class-custom-product-builder-wc.php';

\CustomProductBuilderWC\Init::get_instance();
