<?php

/*
 * Plugin Name: Labb2 Produktlista
 * Description: En produktlista med de bäst säljade produkterna.
 * Author: Elin Gustafsson
 */


 if (!defined("ABSPATH")) {
     // Se till att inte filen laddas direkt
     exit;
 }

 // Verifiera att woocommerce är aktiverat
 if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

   // Funktion för shortcode [top_selling_products]
  function top_selling_products_shortcode( $atts ) {

    // Gör så att de 10 mest sålda produkterna visas.
    $args = array(
      'post_type' => 'product',
      'posts_per_page' => 10,
      'meta_key'  => 'total_sales',
      'orderby'   => 'meta_value_num',
      );

    // Loop som hämtar produkterna från databasen och skiver ut dem på sidan.
    $loop = new WP_Query( $args );

      if ( $loop->have_posts() ) {
        while ( $loop->have_posts() ) : $loop->the_post(); global $product;

          ?><ul><?php
            //echo $product->get_total_sales();
            ?><h4><?php echo $product->get_name(); ?></h4><?php
            ?><p><?php echo $product->get_price(); ?> kr</p><?php
            ?><p><?php echo $product->get_description(); ?></p><?php
          ?></ul><?php
        endwhile;
        } else {
            return __( 'No products found' );
        }
      wp_reset_postdata();

}

    // Läser in shortcodens funktion så den kan visas i fontend
    add_shortcode( 'top_selling_products', 'top_selling_products_shortcode' );

}

?>
