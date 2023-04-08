<?php
/**
 * Child theme functions
 */

function oceanwp_child_enqueue_parent_style() {
 	// Dynamically get version number of the parent stylesheet (lets browsers re-cache your stylesheet when you update your theme)
 	$theme   = wp_get_theme( 'OceanWP' );
 	$version = $theme->get( 'Version' );
 	// Load the stylesheet
 	wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'oceanwp-style' ), $version );
}
add_action( 'wp_enqueue_scripts', 'oceanwp_child_enqueue_parent_style', 100 );


add_filter( 'the_title', 'fws_title_order_received', 10, 2 );
function fws_title_order_received( $title, $id = null ) {
    if ( function_exists( 'is_order_received_page' ) && is_order_received_page() && get_the_ID() === $id ) {
        $title = __( 'Bestellng ontvangen', 'woocommerce' );
    } elseif ( function_exists( 'is_checkout_pay_page' ) && is_checkout_pay_page() && get_the_ID() === $id) {
		$title = __( 'Je bestellng betalen', 'woocommerce' );
	}
    return $title;
}
add_filter( 'woocommerce_order_button_text', 'fws_custom_button_text' );
function fws_custom_button_text( $button_text ) {
	return __( 'Bestellen en betalen', 'woocommerce' ); // new text is here
}


add_filter( 'woocommerce_product_add_to_cart_text', 'fws_custom_add_to_cart_text' );
function fws_custom_add_to_cart_text() {
	return __( 'In winkelwagen', 'woocommerce' );
}


add_filter( 'woocommerce_cart_item_thumbnail', '__return_false' );


function fws_remove_cart_product_link( $product_link, $cart_item, $cart_item_key ) {
    $product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
    return $product->get_title();
}
add_filter( 'woocommerce_cart_item_name', 'fws_remove_cart_product_link', 10, 3 );

add_action( 'woocommerce_before_shop_loop_item_title', 'fws_new_product_badge_shop_page', 3 );
function fws_new_product_badge_shop_page() {
   	global $product;
   	$newness_days = 30;
   	$created = strtotime( $product->get_date_created() );
   	if ( ( time() - ( 60 * 60 * 24 * $newness_days ) ) < $created ) {
      	echo '<span class="itsnew onsale">' . esc_html__( 'Nieuw', 'woocommerce' ) . '</span>';
   	}
}

/**
 * Hide shipping rates when free shipping is available.
 * Updated to support WooCommerce 2.6 Shipping Zones.
 *
 * @param array $rates Array of rates found for the package.
 * @return array
 */
function my_hide_shipping_when_free_is_available( $rates ) {
	$free = array();
	foreach ( $rates as $rate_id => $rate ) {
		if ( 'free_shipping' === $rate->method_id ) {
			$free[ $rate_id ] = $rate;
			break;
		}
	}
	return ! empty( $free ) ? $free : $rates;
}
add_filter( 'woocommerce_package_rates', 'my_hide_shipping_when_free_is_available', 100 );
