<?php

namespace CustomProductBuilderWC;

final class Scripts {

    /**
     * Loads Admin's script
     * @version 1.0
     * @since 1.0
     */
    public static function load_admin_scripts()
    {
        add_action( 'admin_enqueue_scripts', [ ( new self() ), 'admin_enqueue_scripts' ] );
    }

    /**
     * Enqueue Scripts
     * @version 1.0
     * @since 1.0
     */
    public static function admin_enqueue_scripts()
    {
        wp_enqueue_script( 'cpbwc-admin', CPBWC_PLUGIN_URL . '/assets/js/admin.min.js', array( 'jquery' ), 1.0, true );
        wp_enqueue_style( 'cpbwc-admin', CPBWC_PLUGIN_URL . '/assets/css/admin.min.css', '', 1.0 );
        wp_enqueue_media();
    }
}
