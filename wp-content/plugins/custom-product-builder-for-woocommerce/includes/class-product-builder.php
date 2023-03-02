<?php

namespace CustomProductBuilderWC;

final class ProductBuilder {

    /**
     * Loads Builder's script
     * @version 1.0
     * @since 1.0
     */
    public static function load_front_scripts()
    {
        $_this = self::wp_enqueue_scripts();

        add_action( 'wp_enqueue_scripts', 'self::wp_enqueue_scripts' );
    }

    /**
     * Enqueue Scripts
     * @version 1.0
     * @since 1.0
     */
    public static function wp_enqueue_scripts()
    {
        $custom_css = "
        .moreOption {
            background-image: url(".CPBWC_PLUGIN_URL."/assets/images/gear.svg)!important;
        }
        .delete-box-amm {
            background-image: url(".CPBWC_PLUGIN_URL."/assets/images/x.svg)!important;
        }
        ";
        $data = cpbwc_get_all_images();

        $data['data'] = [
            'token'     =>  wp_create_nonce( 'cpbwc-security' ),
            'ajaxurl'   =>  admin_url( 'admin-ajax.php' )
        ];

        wp_enqueue_script( 'cpbwc-jquery-ui-touch', CPBWC_PLUGIN_URL . '/assets/js/jquery-ui-touch-punch.min.js', array( 'jquery-ui-draggable', 'jquery-ui-resizable' ), 1.0, true );
        wp_enqueue_script( 'cpbwc-builder', CPBWC_PLUGIN_URL . '/assets/js/builder.min.js', array( 'cpbwc-jquery-ui-touch' ), 1.0, true );
        wp_enqueue_script( 'cpbwc-builder-front', CPBWC_PLUGIN_URL . '/assets/js/front.min.js', array( 'cpbwc-builder' ), 1.0, true );
        wp_enqueue_style( 'cpbwc-builder', CPBWC_PLUGIN_URL . '/assets/css/builder.min.css', '', 1.0 );
        wp_add_inline_style( 'cpbwc-builder', $custom_css );
        wp_localize_script( 'cpbwc-builder-front', 'cpbwc', $data );
    }
}
