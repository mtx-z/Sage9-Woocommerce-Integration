<?php

/**
 * 19.05.18: tested and required for Woocommerce 3.3.5 integration with sage 9.0.1
 *
 * changes: added 'resources/views/woocommerce' to paths array
 *
 * @param string|string[] $templates Possible template files
 * @return array
 */
function filter_templates($templates)
{
	$paths = apply_filters('sage/filter_templates/paths', [
		'views',
		'resources/views',
		'resources/views/woocommerce'
	]);
	$paths_pattern = "#^(" . implode('|', $paths) . ")/#";

	return collect($templates)
		->map(function ($template) use ($paths_pattern) {
			/** Remove .blade.php/.blade/.php from template names */
			$template = preg_replace('#\.(blade\.?)?(php)?$#', '', ltrim($template));

			/** Remove partial $paths from the beginning of template names */
			if (strpos($template, '/')) {
				$template = preg_replace($paths_pattern, '', $template);
			}

			return $template;
		})
		->flatMap(function ($template) use ($paths) {
			return collect($paths)
				->flatMap(function ($path) use ($template) {
					return [
						"{$path}/{$template}.blade.php",
						"{$path}/{$template}.php",
						"{$template}.blade.php",
						"{$template}.php",
					];
				});
		})
		->filter()
		->unique()
		->all();
}