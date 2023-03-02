<?php

namespace CustomProductBuilderWC;

final class Admin {

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
        add_action( 'add_meta_boxes', [ $this, 'add_meta_box' ] );

        add_action( 'save_post_product', [ $this, 'save_product' ], 10, 3 );

        \CustomProductBuilderWC\Scripts::load_admin_scripts();

        add_action( 'add_meta_boxes_product', [ $this, 'remove_meta_boxes' ], 11 );

        add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );

    }

    /**
     * Add Meta Boxes
     * @version 1.0
     * @since 1.0
     */
    public function add_meta_box()
    {
        add_meta_box(
            'cpbwc-enable',
            __( 'Custom Product Builder', 'cpbwc' ),
            [ $this, 'cpbwc_enable' ],
            'product',
            'side',
            'high'
        );

        if( cpbwc_is_enabled() )
        {
            add_meta_box(
                'upload-product-images',
                __( 'Upload Product Images', 'cpbwc' ),
                [ $this, 'upload_product_images' ],
                'product',
                'side',
                'high'
            );

            add_meta_box(
                'upload-product-builders',
                __( 'Upload Product Builders', 'cpbwc' ),
                [ $this, 'upload_product_builders' ],
                'product',
                'side',
                'high'
            );
        }
    }


    /**
     * Metabox Call Back
     * @version 1.0
     * @since 1.0
     */
    public function cpbwc_enable()
    {
        $content = '';

        $checked = ( get_option( 'enable_cpbwc' ) && (int)get_option( 'enable_cpbwc' ) == get_the_ID() ) ? 'checked' : '';

        do_action( 'cpbwc_before_metabox' );

        $content = "
        <div>
            <form action='post'>
                <label for='enable_cpbwc'>
                    <input type='checkbox' id='enable_cpbwc' name='enable_cpbwc' value='1' ".$checked." />
                    ". __( 'Enable Custom Product Builder', 'cpbwc' ) ."
                </label>
            </form>
            <br>
            <br>
            <div>
                <i>".__( 'Works with one product at the time, If it\'s already enable on other product and you enable on this too, It will disable previous, and enable this one.', 'cpbwc' )."</i>
            </div>
        </div>
        ";

        do_action( 'cpbwc_after_metabox' );

        echo wp_kses( $content, array(
            'div'   =>  array(),
            'form'  =>  array(),
            'label' =>  array(),
            'input' =>  array(
                'type'      =>  array(),
                'id'        =>  array(),
                'name'      =>  array(),
                'value'     =>  array(),
                'checked'   =>  array()
            ),
            'i'     =>  array()
        ) );
    }

    /**
     * Metabox Callback
     * @version 1.0
     * @since 1.0
     */
    public function upload_product_images()
    {
        $allowed_html = array(
            'div'   =>  array(
                'class' =>  array()
            ),
            'a'     =>  array(
                'class'     =>  array(),
                'data-name' =>  array(),
                'data-id'   =>  array(),
                'title'     =>  array()
            ),
            'img'   =>  array(
                'src'       =>  array(),
                'width'     =>  array(),
                'height'    =>  array(),
                'class'     =>  array()
            ),
            'input' =>  array(
                'class' =>  array(),
                'type'  =>  array(),
                'name'  =>  array(),
                'value' =>  array()
            ),
            'ul'    =>  array(
                'class' =>  array()
            ),
            'li'    =>  array()
        );

        $content = $this->uploaded_images( 'images' );

        echo wp_kses( $content, $allowed_html );
    }

    /**
     * Metabox Callback
     * @version 1.0
     * @since 1.0
     */
    public function upload_product_builders()
    {
        $allowed_html = array(
            'div'   =>  array(
                'class' =>  array()
            ),
            'a'     =>  array(
                'class'     =>  array(),
                'data-name' =>  array(),
                'data-id'   =>  array(),
                'title'     =>  array()
            ),
            'img'   =>  array(
                'src'       =>  array(),
                'width'     =>  array(),
                'height'    =>  array(),
                'class'     =>  array()
            ),
            'input' =>  array(
                'class' =>  array(),
                'type'  =>  array(),
                'name'  =>  array(),
                'value' =>  array()
            ),
            'ul'    =>  array(
                'class' =>  array()
            ),
            'li'    =>  array()
        );

        $content = $this->uploaded_images( 'builders' );

        echo wp_kses( $content, $allowed_html );
    }

    /**
     * Get uploaded images in metaboxes
     * @param $category
     * @return string
     * @version 1.0
     * @since 1.0
     */
    public function uploaded_images( $category )
    {
        $product_images = cpbwc_get_product_images( $category );

        $content = '';

        $content .= '
        <div>
            <a href="#" class="cpbwc-upload-images" data-name="'.$category.'">Upload Images</a>';

        if( $product_images )
        {
            foreach ( $product_images as $key => $value )
            {
                $url = wp_get_attachment_image_url( $value );

                $content .= "
                    <div class='cpbwc-images-wrap image-{$value}'>
                        <img src='{$url}' width='50px' height='50px' class='cpbwc-images-preview' />
                        <input class='cpbwc-upload-images{$value}' type='hidden' name='cpbwc_uploaded_product_{$category}[]'  value='{$value}'>
                        <ul class='actions'><li><a href='#' class='remove' data-id='{$value}' data-name='{$category}' title='Remove image'>Remove</a></li></ul>
                    </div>";
            }
        }

        $content .= "
        </div>";

        return $content;
    }

    /**
     * Saves/ Updates product settings.
     * @version 1.0
     * @since 1.0
     */
    public function save_product( $post_id, $post, $update )
    {
        if( isset( $_POST['enable_cpbwc'] ) )
        {
            $enable = sanitize_text_field( $_POST['enable_cpbwc'] );

            $product_images = isset( $_POST['cpbwc_uploaded_product_images'] ) && is_array( $_POST['cpbwc_uploaded_product_images'] ) ? cpbwc_sanitize_array( $_POST['cpbwc_uploaded_product_images'] ) : array();

            $builder_images = isset( $_POST['cpbwc_uploaded_product_builders'] ) && is_array( $_POST['cpbwc_uploaded_product_builders'] ) ? cpbwc_sanitize_array( $_POST['cpbwc_uploaded_product_builders'] ) : array();

            if( $enable == '1' )
            {
                update_option( 'enable_cpbwc', $post_id );
                update_option( 'cpbwc_product_images', $product_images );
                update_option( 'cpbwc_product_builders', $builder_images );
            }
        }
        else
        {
            $is_enabled = get_option( 'enable_cpbwc' );

            if( $is_enabled && $is_enabled == $post_id )
                delete_option( 'enable_cpbwc' );
        }
    }

    /**
     * Remove WooCommerce's metaboxes
     * @version 1.0
     * @since 1.0
     */
    public function remove_meta_boxes()
    {
        if( cpbwc_is_enabled() )
        {
            remove_meta_box( 'woocommerce-product-images',  'product', 'side');
            remove_meta_box( 'postimagediv',  'product', 'side');
        }
    }

    /**
     * Adds row on Plugin page
     * 
     * @since 1.0.1
     * @version 1.0
     */
    public function plugin_row_meta( $plugin_meta, $plugin_file, $plugin_data, $status ) {

        if( $plugin_data['slug'] == 'custom-product-builder-for-woocommerce' ) {
            $plugin_meta[] = sprintf(
                '<a href="%s" style="color: green; font-weight: bold" target="_blank">%s</a>',
                esc_url( 'https://checkout.freemius.com/mode/dialog/plugin/10656/plan/18046/' ),
                __( 'Buy Pro' )
            );
            $plugin_meta[] = sprintf(
                '<a href="%s" style="color: green; font-weight: bold" target="_blank">%s</a>',
                esc_url( 'https://cpbw.coderpress.co/shop/' ),
                __( 'Demo' )
            );

        }

        return $plugin_meta;

    }
}

\CustomProductBuilderWC\Admin::get_instance();
