<?php

namespace App;

/**
 * Theme setup
 * THIS EXIST IN YOUR SAGE THEME ! Just add the woocommerce theme support line
 */
add_action('after_setup_theme', function () {
	/**
	 * ...Add following to the existing add_action('after_setup_theme')
     * 19.05.18: tested and required for Woocommerce 3.3.5 integration with sage 9.0.1
	 */
    add_theme_support('woocommerce');
	/**
	 * ...
	 */
}, 20);

/**
 * Output WooCommerce content.
 * This function is only used in the optional 'woocommerce.php' template.
 * which people can add to their themes to add basic woocommerce support.
 * without hooks or modifying core templates.
 *
 * 17.07.18: tested, required and updated for Woocommerce 3.4.3 integration with sage 9.0.1 (WP 4.9.7)
 * 19.05.18: tested and required for Woocommerce 3.3.5 integration with sage 9.0.1
 * Changed: added $args parameter, and edited wc_get_template_part() to use $args
 *
 * from: wp-content/plugins/woocommerce/includes/wc-template-functions.php:l863 (Woo v3.4.3)
 * from: wp-content/plugins/woocommerce/includes/wc-template-functions.php:l549 (Woo v3.3.5)
 */
function woocommerce_content($args = [])
{
	if ( is_singular( 'product' ) ) {

		while ( have_posts() ) :
			the_post();
			wc_get_template_part( 'content', 'single-product', null, $args );
		endwhile;

	} else {
		?>

		<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>

            <h1 class="page-title"><?php woocommerce_page_title(); ?></h1>

		<?php endif; ?>

		<?php do_action( 'woocommerce_archive_description' ); ?>

		<?php if ( woocommerce_product_loop() ) : ?>

			<?php do_action( 'woocommerce_before_shop_loop' ); ?>

			<?php woocommerce_product_loop_start(); ?>

			<?php if ( wc_get_loop_prop( 'total' ) ) : ?>
				<?php while ( have_posts() ) : ?>
					<?php the_post(); ?>
					<?php wc_get_template_part( 'content', 'product', null, $args ); ?>
				<?php endwhile; ?>
			<?php endif; ?>

			<?php woocommerce_product_loop_end(); ?>

			<?php do_action( 'woocommerce_after_shop_loop' ); ?>

		<?php else : ?>

			<?php do_action( 'woocommerce_no_products_found' ); ?>

		<?php
		endif;

	}
}