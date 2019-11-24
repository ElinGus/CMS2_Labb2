<?php

/*
 * Plugin Name: Labb2 Fraktmodul
 * Description: En fraktmodul som beräknar fraktkostnaden.
 * Author: Elin Gustafsson
 */


 if (!defined("ABSPATH")) {
     // Se till att inte filen laddas direkt
     exit;
 }


 if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

 	function shippingcost_shipping_method_init() {
 		if ( ! class_exists( 'WC_Shippingcost_Method' ) ) {
 			class WC_Shippingcost_Method extends WC_Shipping_Method {

 				// Konstrutor för shipping class
 				public function __construct() {
 					$this->id                 = 'shippingcost'; // ID för shipping method. Should be uunique.
 					$this->method_title       = __( 'Fraktkostnad' );  // Title shown in admin
 					$this->method_description = __( 'Fraktkostnad utifrån kundkorgens totalvikt.' ); // Description shown in admin
 					$this->enabled            = "yes"; // This can be added as an setting but for this example its forced enabled
 					$this->title              = "Fraktkostnad (totalvikt av kundkorg)"; // This can be added as an setting but for this example its forced.
 					$this->init();
 				}
 				/**
 				 * Init your settings
 				 *
 				 * @access public
 				 * @return void
 				 */
 				function init() {
 					// Load the settings API
 					$this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings
 					$this->init_settings(); // This is part of the settings API. Loads settings you previously init.
 					// Save settings in admin if you have any defined
 					add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
 				}

 				 // calculate_shipping function. Räknar ut fraktkostnaden.
 				public function calculate_shipping( $package = array() ) {

          $weight = 0;
          $cost = 0;

          foreach ( $package['contents'] as $item_id => $values )
          {
            $_product = $values['data'];
            $weight = $weight + $_product->get_weight() * $values['quantity'];
          }

          $weight = wc_get_weight( $weight, 'kg' );

          if( $weight < 1 ) {
                $cost = 30;

            } elseif( $weight < 5 ) {
                $cost = 60;

            } elseif( $weight < 10 ) {
                $cost = 100;

            } elseif( $weight < 20 ) {
                $cost = 200;

            } else {
              $cost = $weight * 10;

            }

            $rate = array(
   						'id' => $this->id,
   						'label' => $this->title,
   						'cost' => $cost,
   					);

 					// Registrerar rate
 					$this->add_rate( $rate );
 				}
 			}
 		}
 	}
 	add_action( 'woocommerce_shipping_init', 'shippingcost_shipping_method_init' );
 	function add_shippingcost_shipping_method( $methods ) {
 		$methods['shippingcost_shipping_method'] = 'WC_Shippingcost_Method';
 		return $methods;
 	}
 	add_filter( 'woocommerce_shipping_methods', 'add_shippingcost_shipping_method' );
 }
