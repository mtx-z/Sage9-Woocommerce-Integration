<?php

namespace App;

/**
 * Render page using Blade (and get data from controller from sage/template/{$class}/data filter)
 * 19.25.18: tested and required for Woocommerce 3.3.5 integration with sage 9.0.1
 * Changed: fix for single-product.php and archive-product to get correct template
 * todo: is there other "woocommerce root" template that should be hot fixed here ?
 */
add_filter( 'template_include', function ( $template ) {

	//fix for single-product.php and archive-product templates
	$template = str_contains( $template, 'single-product.php' ) ? get_stylesheet_directory() . '/views/woocommerce/single-product.blade.php' : $template;
	$template = str_contains( $template, 'archive-product.php' ) ? get_stylesheet_directory() . '/views/woocommerce/archive-product.blade.php' : $template;

	$data = collect( get_body_class() )->reduce( function ( $data, $class ) use ( $template ) { //use body class to get correct controller data
		return apply_filters( "sage/template/{$class}/data", $data, $template ); //get data from controller
	}, [] );
	if ( $template ) {
		echo template( $template, $data );

		return get_stylesheet_directory() . '/index.php';
	}

	return $template;
}, PHP_INT_MAX );

/**
 * Render page using Blade
 * 19.25.18: tested and required for Woocommerce 3.3.5 integration with sage 9.0.1
 * Changed: locate blade or php template file and render it
 * Triggers just before woocommerce actually render PHP template, but return blade and null, or php template like woocommerce would do
 * wp-content/plugins/woocommerce/includes/wc-core-functions.php:l158
 */
add_filter( 'wc_get_template_part', function ( $template, $slug, $name, $args = [] ) {
	$bladeTemplate = false;
	// Look in yourtheme/slug-name.blade.php and yourtheme/woocommerce/slug-name.blade.php
	if ( $name && ! WC_TEMPLATE_DEBUG_MODE ) {
		$bladeTemplate = locate_template( [ "{$slug}-{$name}.blade.php", WC()->template_path() . "{$slug}-{$name}.blade.php" ] );
	}
	// If template file doesn't exist, look in yourtheme/slug.blade.php and yourtheme/woocommerce/slug.blade.php
	if ( ! $template && ! WC_TEMPLATE_DEBUG_MODE ) {
		$bladeTemplate = locate_template( [ "{$slug}.blade.php", WC()->template_path() . "{$slug}.blade.php" ] );
	}
	if ( $bladeTemplate ) {
		echo template( $bladeTemplate, $args );

		return null;
	}
	//try to look for PHP files within resources/views/woocommerce
	$normalTemplate = false;
	if ( $name && ! WC_TEMPLATE_DEBUG_MODE ) {
		$normalTemplate = locate_template( [ "{$slug}-{$name}.php", WC()->template_path() . "{$slug}-{$name}.php" ] );
	}
	if ( ! $normalTemplate && ! WC_TEMPLATE_DEBUG_MODE ) {
		$normalTemplate = locate_template( [ "{$slug}.php", WC()->template_path() . "{$slug}.php" ] );
	}
	if ( $normalTemplate ) {
		return get_theme_file_path( $normalTemplate ); //work even without
	}

	return $template;
}, PHP_INT_MAX, 4 );

/**
 * Render woocommerce template parts using Blade
 * 19.25.18: tested and required for Woocommerce 3.3.5 integration with sage 9.0.1
 * Changed: Woocommerce support to use resources/views/woocommerce/* templates when using template parts
 * (wc_get_template_part & template_include filters will search for .blade.php)
 */
add_filter( 'wc_get_template', function ( $located, $template_name, $args, $template_path, $default_path ) {
	$bladeTemplate = locate_template( [ $template_name, 'resources/views/' . WC()->template_path() . $template_name ] );
	if ( $bladeTemplate ) {
		return template_path( $bladeTemplate, $args );
	}

	return $located;
}, PHP_INT_MAX, 5 );