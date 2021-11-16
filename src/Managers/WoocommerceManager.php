<?php
/**
 * Bootstraps settings and configurations for taxonomies.
 *
 * @package Salon
 */

namespace Salon\Managers;

use Timber;

/** Class */
class WoocommerceManager {

    /**
     * Runs initialization tasks.
     *
     * @return void
     */
    public function run() {
        // Remove a bunch of WooCommerce defualts.
        remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
        remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
        remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
        remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
        remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
        remove_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20 );
        remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
        remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
        remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
        remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
		remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );
        remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
        remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
        remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
        remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

        // Add some actions.
        add_action( 'woocommerce_before_quantity_input_field', array( $this, 'add_quantity_label' ) );
        add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'manage_add_to_cart_response' ) );
        add_filter( 'is_woocommerce', array( $this, 'manage_woocommerce_screens' ) );
        add_theme_support( 'woocommerce' );
        add_action( 'woocommerce_created_customer', array( $this, 'wooc_save_extra_register_fields' ) );
    }

    /**
     * Print out a label for the quantity input.
     *
     * @return void
     */
    public function add_quantity_label() {
        echo '<span class="quantity__label">Quantity</span>';
    }

    /**
     * Add some custom fields to save to DB
     *
     * @return void
     */
    public function wooc_save_extra_register_fields( $customer_id ) {
        if ( isset( $_POST['billing_first_name'] ) ) {
            //First name field which is by default
            update_user_meta( $customer_id, 'first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
            // First name field which is used in WooCommerce
            update_user_meta( $customer_id, 'billing_first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
        }
        if ( isset( $_POST['billing_last_name'] ) ) {
            // Last name field which is by default
            update_user_meta( $customer_id, 'last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
            // Last name field which is used in WooCommerce
            update_user_meta( $customer_id, 'billing_last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
        }
    }

    /**
     * Manage the Woocommerce response to the `add_to_cart` API call.
     *
     * @return array
     */
    public function manage_add_to_cart_response() {
        global $woocommerce;

        $item_count = $woocommerce->cart->get_cart_contents_count();

        $cart = array(
            'show_flag' => ! is_cart() && ! is_checkout() && $item_count > 0,
            'count'     => $item_count,
            'subtotal'  => $woocommerce->cart->get_subtotal(),
            'link'      => wc_get_cart_url(),
        );

        $context['cart'] = $cart;

        return array(
            'markup' => Timber::compile( 'components/cart-flag.twig', $context ),
        );
    }

    /**
     * Updates the global woocommerce conditional check in certain cases.
     *
     * @param boolean $is_woocommerce Is the current page a woocommerce page.
     *
     * @return boolean
     */
    public function manage_woocommerce_screens( $is_woocommerce ) {
        if ( get_query_var( 'designer_shop' ) ) {
            return true;
        }

        return $is_woocommerce;
    }
}
