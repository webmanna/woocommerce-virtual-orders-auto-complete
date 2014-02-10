<?php
/*
Plugin Name: WooCommerce Auto-complete Orders (Virtual Only)
Description: Orders with virtual products or subscirptions don't update as Complete after payment is processed. This plugin will fix that.
*/
class WooCommerceAutoComplete {
	function __construct() {
		add_filter( 'woocommerce_payment_complete_order_status', array($this, 'virtual_order_payment_complete_order_status'), 10, 2);
	}

	function virtual_order_payment_complete_order_status( $order_status, $order_id ) {

	 $order = new WC_Order( $order_id );

	  if ( 'processing' == $order_status &&
	  	( 'on-hold' == $order->status || 'pending' == $order->status || 'failed' == $order->status )) {
	 
	    $virtual_order = null;
	 
	    if ( count( $order->get_items() ) > 0 ) {
	 
	      foreach( $order->get_items() as $item ) {
	 
	        if ( 'line_item' == $item['type'] ) {
	 
	          $_product = $order->get_product_from_item( $item );

	          // debug($_product);
	 
	          if ( ! $_product->is_virtual() ) {
	            // once we've found one non-virtual product we know we're done, break out of the loop
	            $virtual_order = false;
	            break;
	          } else {
	            $virtual_order = true;
	          }
	        }
	      }
	    }
	 
	    // virtual order, mark as completed
	    if ( $virtual_order ) {
	      return 'completed';
	    }
	  }
	  // non-virtual order, return original status
	  return $order_status;
	}
}
$WooCommerceAutoComplete = new WooCommerceAutoComplete();
?>