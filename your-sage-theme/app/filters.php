<?php

namespace App;

/**
 * Render page using Blade
 * 19.25.18: tested and required for Woocommerce 3.3.5 integration with sage 9.0.1
 *
 * Changed: fix for single-product.php and archive-product
 */
add_filter('template_include', function ($template) {

	//fix for single-product.php and archive-product
	$template = str_contains($template, 'single-product.php') ? get_stylesheet_directory() . '/views/woocommerce/single-product.blade.php' : $template;
	$template = str_contains($template, 'archive-product.php') ? get_stylesheet_directory() . '/views/woocommerce/archive-product.blade.php' : $template;

	$data = collect(get_body_class())->reduce(function ($data, $class) use ($template) { //use body class to get correct controller data
		return apply_filters("sage/template/{$class}/data", $data, $template); //get data from controller
	}, []);
	if ($template) {
		echo template($template, $data);
		return get_stylesheet_directory().'/index.php';
	}
	return $template;
}, PHP_INT_MAX);

/**
 * Render page using Blade
 * 19.25.18: tested and required for Woocommerce 3.3.5 integration with sage 9.0.1
 *
 * Changed: Woocommerce support to use .balde.php templates from /resources/views or .php file
 */
add_filter('wc_get_template_part', function ($template, $slug, $name, $args) {
	$bladeTemplate = false;
	// Look in yourtheme/slug-name.blade.php and yourtheme/woocommerce/slug-name.blade.php
	if ($name && !WC_TEMPLATE_DEBUG_MODE) {
		$bladeTemplate = locate_template(["{$slug}-{$name}.blade.php", WC()->template_path() . "{$slug}-{$name}.blade.php"]);
	}
	// If template file doesn't exist, look in yourtheme/slug.blade.php and yourtheme/woocommerce/slug.blade.php
	if (!$template && !WC_TEMPLATE_DEBUG_MODE) {
		$bladeTemplate = locate_template(["{$slug}.blade.php", WC()->template_path() . "{$slug}.blade.php"]);
	}
	if ($bladeTemplate) {
		echo template($bladeTemplate, $args);
		// Return a blank file to make WooCommerce happy
		//return get_theme_file_path('index.php');
		return null;
	}
	//try to look for PHP files within resources/views/woocommerce
	$normalTemplate = false;
	if ($name && !WC_TEMPLATE_DEBUG_MODE) {
		$normalTemplate = locate_template(["{$slug}-{$name}.php", WC()->template_path() . "{$slug}-{$name}.php"]);
	}
	if (!$normalTemplate && !WC_TEMPLATE_DEBUG_MODE) {
		$normalTemplate = locate_template(["{$slug}.php", WC()->template_path() . "{$slug}.php"]);
	}
	if ($normalTemplate) {
		return get_theme_file_path($normalTemplate); //work even without
	}
	return $template;
}, PHP_INT_MAX, 4);