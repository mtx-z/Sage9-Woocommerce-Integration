<?php

namespace App;

/**
 * Theme setup
 */
add_action('after_setup_theme', function () {
	/**
	 * ...Add following to the existing add_action('after_setup_theme')
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
 */
function woocommerce_content($args = [])
{
	if (is_singular('product')) {
		while (have_posts()) : the_post();
			wc_get_template_part('content', 'single-product', null, $args);
		endwhile;
	} else { ?>

		<?php wc_get_template_part('archive-product', 'taxonomy-header', null, $args); ?>

		<?php if (apply_filters('woocommerce_show_page_title', true)) : ?>
			<h1 class="page-title"><?php woocommerce_page_title(); ?></h1>

		<?php endif; ?>

		<?php do_action('woocommerce_archive_description'); ?>

		<?php if (have_posts()) : ?>

			<?php do_action('woocommerce_before_shop_loop'); ?>

			<?php woocommerce_product_loop_start(); ?>

			<?php woocommerce_product_subcategories(); ?>

			<?php while (have_posts()) : the_post(); ?>

				<?php wc_get_template_part('content', 'product', null, $args); ?>

			<?php endwhile; // end of the loop. ?>

			<?php woocommerce_product_loop_end(); ?>

			<?php do_action('woocommerce_after_shop_loop'); ?>

		<?php elseif (!woocommerce_product_subcategories(['before' => woocommerce_product_loop_start(false), 'after' => woocommerce_product_loop_end(false)])) : ?>

			<?php do_action('woocommerce_no_products_found'); ?>

		<?php endif;
	}
}
