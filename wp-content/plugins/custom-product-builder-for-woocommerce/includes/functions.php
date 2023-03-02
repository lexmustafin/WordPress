<?php

/**
 * Checks if builder is enabled or not.
 * @param $product_id
 * @return bool
 * @since 1.0
 * @version 1.0
 */
if ( !function_exists( 'cpbwc_is_enabled' ) ):
    function cpbwc_is_enabled( $product_id = '' )
{
    $product_id = empty( $product_id ) ? get_the_ID() : $product_id;

    return get_option( 'enable_cpbwc' ) && get_option( 'enable_cpbwc' ) == $product_id;
}
endif;

/**
 * Sanitizes array.
 * @param array $args
 * @return bool
 * @since 1.0
 * @version 1.0
 */
if ( !function_exists( 'cpbwc_sanitize_array' ) ):
    function cpbwc_sanitize_array( array $args )
    {
        foreach ( $args as $key => $value )
        {
            $args[$key] = sanitize_text_field( $value );
        }

        return $args;
    }
endif;

/**
 * Gets product images.
 * @param $category
 * @return bool
 * @since 1.0
 * @version 1.0
 */
if ( !function_exists( 'cpbwc_get_product_images' ) ):
    function cpbwc_get_product_images( $category )
    {
        return get_option( "cpbwc_product_{$category}" );
    }
endif;

/**
 * Removes a product.
 * @param $category
 * @param $attachment_id
 * @return bool
 * @since 1.0
 * @version 1.0
 */
if ( !function_exists( 'cpbwc_remove_product_image' ) ):
    function cpbwc_remove_product_image( $category, $attachment_id )
    {
        $images = get_option( "cpbwc_product_{$category}" );

        if( $images && in_array( $attachment_id, $images) )
        {
            $key = array_search( $attachment_id, $images );

            unset( $images[$key] );

            return update_option( "cpbwc_product_{$category}", $images );
        }

        return false;
    }
endif;

/**
 * Gets all images of builder
 * @param $size
 * @return array[]
 * @since 1.0
 * @version 1.0
 */
if ( !function_exists( 'cpbwc_get_all_images' ) ):
    function cpbwc_get_all_images( $size = 'full' )
    {
        $images = [];
        $builders = [];

        foreach ( cpbwc_get_product_images( 'images' ) as $key => $value )
        {
            $images[] = [
                'title' =>  '',
                'url'   =>  wp_get_attachment_image_url( $value, $size )
            ];
        }

        foreach ( cpbwc_get_product_images( 'builders' ) as $key => $value )
        {
            $builders[] = [
                'title' =>  '',
                'url'   =>  wp_get_attachment_image_url( $value, $size )
            ];
        }

        return [
            'images'    =>  $images,
            'builders'  =>  $builders
        ];
    }
endif;

/**
 * Gets cart hash
 * @return bool|mixed
 * @since 1.0
 * @version 1.0
 */
if ( !function_exists( 'cpbwc_get_cart_hash' ) ):
    function cpbwc_get_cart_hash()
{
    $cart_hash = false;

    if( isset( $_COOKIE['cpbwc_cart'] ) )
    {
        $cart_hash = sanitize_text_field( $_COOKIE['cpbwc_cart'] );
    }

    return $cart_hash;
}
endif;

/**
 * Saves designed products/ cart into transient
 * @param string $product_id
 * @return bool
 * @version 1.0
 * @since 1.0
 */
if ( !function_exists( 'cpbwc_save_cart' ) ):
    function cpbwc_save_cart( $product_id = '' )
    {
        $product_id = empty( $product_id ) ? get_the_ID() : $product_id;

        if( isset( $_COOKIE['cpbwc_cart'] ) )
        {
            $cart_hash = 'cpbwc_' . sanitize_text_field( $_COOKIE['cpbwc_cart'] );

            $saved_products = get_transient( $cart_hash );

            if( $saved_products )
            {
                $saved_products[] = $product_id;

                set_transient( $cart_hash, $saved_products, 86400 );

                return true;
            }
            else
            {
                $saved_products = array( $product_id );

                set_transient( $cart_hash, $saved_products, 86400 );

                return true;
            }
        }

        return false;
    }
endif;

/**
 * Checks is product in cart.
 * @param string $product_id
 * @return bool
 * @since 1.0
 * @version 1.0
 */
if ( !function_exists( 'cpbwc_is_product_in_cart' ) ):
    function cpbwc_is_product_in_cart( $product_id = '' )
{
    $product_id = empty( $product_id ) ? get_the_ID() : $product_id;

    if( isset( $_COOKIE['cpbwc_cart'] ) )
    {
        $cart_hash = 'cpbwc_' . $_COOKIE['cpbwc_cart'];

        $saved_products = get_transient( $cart_hash );

        if ( $saved_products && in_array( $product_id, $saved_products ) )
        {
            return true;
        }
    }

    return false;
}
endif;

/**
 * Gets image URL.
 * @param string $product_id
 * @return bool
 * @since 1.0
 * @version 1.0
 */
if ( !function_exists( 'cpbwc_get_image_url' ) ):
    function cpbwc_get_image_url( $product_id = '', $cart_hash = '' )
    {
        $product_id = empty( $product_id ) ? get_the_ID() : $product_id;
        $cart_hash = empty( $cart_hash ) ? cpbwc_get_cart_hash() : $cart_hash;

        if( isset( $_COOKIE['cpbwc_cart'] ) )
        {
            $dir = wp_get_upload_dir()['baseurl'] . '/cpbwc';

            $image = "{$cart_hash}-{$product_id}.jpeg";

            return "{$dir}/{$image}";
        }

        return false;
    }
endif;

/**
 * Deletes cookie and transient
 * @return bool
 * @since 1.0
 * @version 1.0
 */
if( !function_exists( 'cpbwc_clear_cart_cookie' ) ):
    function cpbwc_clear_cart_cookie()
{
    if( isset( $_COOKIE['cpbwc_cart'] ) )
    {
        $cookie = sanitize_text_field( $_COOKIE['cpbwc_cart'] );

        unset( $_COOKIE['cpbwc_cart'] );

        delete_transient( "cpbwc_{$cookie}" );

        return true;
    }

    return false;
}
endif;
