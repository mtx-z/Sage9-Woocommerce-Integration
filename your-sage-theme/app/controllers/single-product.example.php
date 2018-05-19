<?php

namespace App;

use Sober\Controller\Controller;

/**
 * EXAMPLE ONLY
 *
 * ** Variables of singleProduct controller will be attached to
 * ** single-product.blade.php and /views/woocommerce/content-single-product.blade.php templates: {{$my_var}}
 *
 * Class singleProduct
 * @package App
 */
class singleProduct extends Controller {

	public function my_var() {
		return 'we got you houston!';
	}
}
