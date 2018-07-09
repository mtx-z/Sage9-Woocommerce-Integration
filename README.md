# Sage 9.0.1 Woocommerce (3.3.5) Integration
How to use Woocommerce (3.3.5) with Sage 9.0.1 (Blade + SoberWP controllers)

## Features
- Includes: 
  - Blade Woocommerce templates
    - Override using default .php file, or rename to .blade.php to use blade (SoberWP controllers variable are available in both case)
    - place your woocommerce templates overrides in /your-sage-theme/resources/views/woocommerce/
      - eg: `/plugins/woocommerce/templates/content-single-product.php` => `/your-sage-theme/resources/views/woocommerce/content-single-product.blade.php`
      - eg: `/plugins/woocommerce/templates/single-product/meta.php` => `/your-sage-theme/resources/views/woocommerce/single-product/meta.blade.php`
      - still need test for sub-templates, but seems to works with `meta.blade.php`
  - SoberWP controllers with Woocommerce blade (or php) templates
    - Get \App global default Sage controller variables available in Woocommerce Blade templates
    - Create custom controllers for Woocommerce templates with same Woocommerce/SoberWP template/naming hierarchy naming logic (eg: `single-product` controller for `single-product.blade.php` then `content-single-product.blade.php` template)
  
## Sources and supports
- I haven't tested all yet, please feel free to report any bug or improvement in issues and I'll fix
- Sources:
  - [discourse.roots.io](https://discourse.roots.io/t/woocommerce-blade-sage-9/8449/17)
  - [discourse.roots.io](https://discourse.roots.io/t/any-working-example-of-sage-9-latest-sage-9-0-0-beta-4-with-woocommerce-3-1-1/10099/17)
  - [github](https://github.com/MarekVrofski/Sage-Woocommerce) (much thanks again to [@MarekVrofski](https://github.com/MarekVrofski/) for his improvements)
  
## Changelog
- 09/07/2018 - test in progress
    - Sage 9.0.1
    - Woocommerce 3.4.3
    - [SoberWP controller 9.0.0-beta.4](https://github.com/soberwp/controller/releases) ([Sage 9.0.1 uses SoberWP controller 9.0.0-beta.4](https://github.com/roots/sage/blob/master/composer.json), not latest [SoberWP controller 2.0.1](https://github.com/soberwp/controller/releases))
    - PHP 7.2.5 (fpm), Nginx, Debian (& Windows 10 - Laragon PHP 7.1)
- 18/05/2018 - tested with
    - Woocommerce 3.3.5
    - Sage 9.0.1
    - [SoberWP controller 9.0.0-beta.4](https://github.com/soberwp/controller/releases) ([Sage 9.0.1 uses SoberWP controller 9.0.0-beta.4](https://github.com/roots/sage/blob/master/composer.json), not latest [SoberWP controller 2.0.1](https://github.com/soberwp/controller/releases))
    - PHP 7.2.5 (fpm), Nginx, Debian (& Windows 10 - Laragon PHP 7.1)
    
## How
### Blade for Woocommerce
- we add_filter on 
  - `template_include`: edit `single-product.php` and `archive-product.php` template path to `/resources/views/woocommerce/*.blade.php` (then retrieve controller data and render blade as usual)
  - `wc_get_template_part`: edit woocommerce template method to look for blade files then php files
  - `wc_get_template`: tell woocommerce to get template from `resources/views/`
- `single-product.php` and `archive-product.php` includes `woocommerce.blade.php` template
- `woocommerce.blade.php` calls our overrided `App\woocommerce_content(get_defined_vars())` that output Woocommerce content

### SoberWP controllers data for Woocommerce
- SoberWP `Controller.php` (`new Loader()`) will parse all controller and methods and store them using "slugified" `$template` name as keys in `$this->instance` (the `controller::class` object is stored)
- SoberWP `controller.php loader()` will `add_filter('sage/template/' . $template . '-data/data')` for all controllers (filter string/controller) with a method that return, (among other datas) the controller data we want
- Sage/WP will normally get `single-product.php` template (changed as `single-product.blade.php` in our `add_filter('template_include')` and `add_filter('wc_get_template_part')` updates targeted in `/resources/views` thanks to our `add_filter('wc_get_template')`) as template hierarchy rules
- Sage will `apply_filter('sage/template/' . $template . '-data/data')` (it uses `body_class()` to find the `$template` name to match the correct controllers with current template) to add data to the `blade->render` (that also render sub-templates that could also need controller data) from `template_include` filter (that we override but just for the "single-product.php" and "archive-product.php" replace update, and load controller data)
- At the moment, (we are in `resources/views/woocommerce/single-product.blade.php`), data are defined in `get_defined_data()` (as rendered from filter `template_include` with controllers data), so WE HAVE THE DATA HERE
- from here, we call `helpers::template()` (We don't have HTMl here, we call a method to output Woocommerce content) using `woocommerce` as template, and we send our vars (from `get_dedined_vars()`) to it
- `woocommerce.blade.php` will call `App\woocommerce_content(get_defined_vars())` ===> HERE we lose data if we don't pass `get_defined_vars()` as parameter
    - `App\woocommerce_content()` is overrided from Woocommerce `wp-content/plugins/woocommerce/includes/wc-template-functions.php`
    - to add a parameter to pass `$args`, which are our datas
- `App\woocommerce_content()` will output correct Woocommerce template hierarchy, passing `$args` to `wc_get_template_part()` it calls

## Todo
- few test with [SoberWP 2.0.1](https://github.com/soberwp/controller/releases)
  - Few errors as default Namespace changes from `\App` to `\App\Controller`
  - should be fixable when Sage update to SoberWP Controller `2.0.1`
- Lot of Woocommerce features and templates (and controller variables passing) to test...
- controllers variables to sub templates (& nested)

## Careful
- Absolutely not tested with all Woocommerce templates and features
  - Checkout, My accounts, Emails, nested templates, Woocommerce plugins templates... need more tests
- **Test** before usage in production (I do and it runs smoothly tho, but it's not a complex store)
- still WIP, issues and improvement reports are welcomed !
- `Notice` error about theme requiring `header.php` and `sidebar.php` as it's deprecated to be without since `3.0.0`, see [roots/sage@1620](https://github.com/roots/sage/issues/1620) 
  - (`Theme without header.php is deprecated since version 3.0.0 with no alternative available. Please include a header.php/sidebar.php template in your theme. \wp-includes\functions.php`)

## DEBUG
#### SoberWP
```
@debug('dump')
@debug('hierarchy')
@debug('controller')
@debug
```

#### get hooked methods for given filter
```
function print_filters_for( $hook = "" ) {
global $wp_filter;
    if( empty( $hook ) || !isset( $wp_filter[$hook] ) )
        return;

    print "<pre>";
    print_r( $wp_filter[$hook] );
    print "</pre>";
}
```
