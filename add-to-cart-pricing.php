<?php
/*
Plugin Name: FFL Cockpit Add-to-Cart Pricing Add-On
Plugin URI: https://garidium.com
Description: Add-to-Cart pricing when list price is below MAP
Version: 1.0.0
Author: Garidium LLC
Author URI: https://garidium.com
*/

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Prevent direct access
if (!defined('ABSPATH')) exit;

add_filter('woocommerce_get_price_html', 'custom_map_price_check', 10, 2);

function custom_map_price_check($price, $product) {
    // Exit early if we are in the admin area to prevent changes in the backend
    if (is_admin()) {
        return $price;
    }

    // Get the product ID
    $product_id = $product->get_id();

    // Get the map_price meta value
    $map_price = get_post_meta($product_id, 'map_price', true);

    // Get the regular price and sale price of the product
    $regular_price = floatval($product->get_regular_price());
    $sale_price = floatval($product->get_sale_price());

    // Determine the display price (sale price if available, otherwise regular price)
    $display_price = $sale_price ?: $regular_price;

    // Check if the display price is below the MAP price
    if ($map_price && $display_price < floatval($map_price)) {
        // Add unique identifiers for each product
        $unique_id = 'product-' . $product_id;

        // Custom message with MAP price
        return sprintf(
            '<div id="%s" class="cockpit-map-section">
                <span class="cockpit-map-price" style="display: none;">%s</span>
                <span class="cockpit-map-message">%s <span class="cockpit-map-message-inner">is the lowest price we can show publicly,</span> <a href="#" class="cockpit-map-link" style="text-decoration: underline;">Click Here</a> <span class="cockpit-map-message-inner">to show the real price.</span></span>
            </div>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    const mapSection = document.querySelector("#%s");
                    const mapLink = mapSection.querySelector(".cockpit-map-link");
                    const mapMessage = mapSection.querySelector(".cockpit-map-message");
                    const mapPrice = mapSection.querySelector(".cockpit-map-price");
                    
                    mapLink.addEventListener("click", function(event) {
                        event.preventDefault(); // Prevent the default anchor behavior
                        mapMessage.style.display = "none";
                        mapPrice.style.display = "inline";
                    });
                });
            </script>',
            $unique_id,
            wc_price($display_price),
            wc_price($map_price),
            $unique_id
        );
    }

    // Return the original price if it doesn't meet the condition
    return $price;
}


/*
add_filter('woocommerce_get_price_html', 'custom_map_price_check', 10, 2);

function custom_map_price_check($price, $product) {
    // Get the product ID
    $product_id = $product->get_id();

    // Get the map_price meta value
    $map_price = get_post_meta($product_id, 'map_price', true);

    // Get the regular price and sale price of the product
    $regular_price = floatval($product->get_regular_price());
    $sale_price = floatval($product->get_sale_price());

    // Determine the display price (sale price if available, otherwise regular price)
    $display_price = $sale_price ?: $regular_price;

    // Check if the display price is below the MAP price
    if ($map_price && $display_price < floatval($map_price)) {
        // Custom message with MAP price
        return sprintf(
            '<div class="cockpit-map-section"><span class="cockpit-map-price">%s</span><span class="cockpit-map-message"> is the lowest price we can show. Click Here to see the real price.</span></div>',
            wc_price($map_price)
        );
    }

    // Return the original price if it doesn't meet the condition
    return $price;
}
*/
/*
// Add a custom message to the product grid as well
add_action('woocommerce_after_shop_loop_item_title', 'custom_map_price_on_product_grid', 15);

function custom_map_price_on_product_grid() {
    global $product;

    // Get the map_price meta value
    $map_price = get_post_meta($product->get_id(), 'map_price', true);

    // Get the regular price and sale price of the product
    $regular_price = floatval($product->get_regular_price());
    $sale_price = floatval($product->get_sale_price());

    // Determine the display price (sale price if available, otherwise regular price)
    $display_price = $sale_price ?: $regular_price;

    // Check if the display price is below the MAP price
    if ($map_price && $display_price < floatval($map_price)) {
        echo sprintf(
            '<span class="map-message">%s is the lowest price we can show. Add to cart to see the real price.</span>',
            wc_price($map_price)
        );
    }
}

*/
