<?php

namespace App;

use Sober\Controller\Controller;

/**
 * EXAMPLE ONLY
 *
 * ** Variables of App controller will be attached to all Woocommerce template: {{$siteName}}
 *
 * Class App
 * @package App
 */
class App extends Controller {

	public function __construct() {
		//
	}

    /**
     * @return string|mixed
     */
	public function siteName() {
		return get_bloginfo( 'name' );
	}
}
