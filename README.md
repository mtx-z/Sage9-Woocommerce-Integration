# Sage 9.0.1 Woocommerce (3.3.5) Integration
How to use Woocommerce (3.3.5) with Sage 9.0.1 (Blade + SoberWP controllers)

## Introduction
- This repo contain needed edits/adds to make Sage 9 works with Woocommerce
- This includes: 
  - Working Blade Woocommerce templates
    - Override using classic .php file, or rename to .blade.php to use some blade (SoberWP controllers variable are available in both case)
  - Working SoberWP controllers with Woocommerce templates
    - Get \App global default Sage controller variables available in Woocommerce Blade templates
    - Create custom controllers for Woocommerce templates with same SoberWP/Template hierarchy naming logic (eg: single-product controller)
  
## Sources and supports
- I haven't tested all yet, please feel free to report any bug or improvement in issues and I'll fix
- Sources:
  - [discourse.roots.io](https://discourse.roots.io/t/woocommerce-blade-sage-9/8449/17)
  - [discourse.roots.io](https://discourse.roots.io/t/any-working-example-of-sage-9-latest-sage-9-0-0-beta-4-with-woocommerce-3-1-1/10099/17)
  - [github](https://github.com/MarekVrofski/Sage-Woocommerce) (Big thanks again at [@MarekVrofski](https://github.com/MarekVrofski/))
  
## Changelog
- 18/05/2018 - tested with
    - Woocommerce 3.3.5
    - Sage 9.0.1
    - [SoberWP controller 9.0.0-beta.4](https://github.com/soberwp/controller/releases) ([Sage 9.0.1 uses SoberWP controller 9.0.0-beta.4](https://github.com/roots/sage/blob/master/composer.json), not latest [SoberWP controller 2.0.1](https://github.com/soberwp/controller/releases))
    - PHP 7.2.5 (fpm), Nginx, Debian (& Windows 10 - Laragon PHP 7.1)
    
## How
### Blade for Woocommerce
- we add_filter on 'template_include' to edit 'single-product' and 'archive-product' template path to .blade
- those templates includes woocommerce.blade.php templates
- woocommerce.blade.php calls our overrided App\woocommerce_content(get_defined_vars()) that output woocommerce content

### SoberWP controllers data for Woocommerce
- SoberWP `Controller.php` (`new Loader()`) will parse all controller and methods and store them using "slugified" `$template` name as keys in `$this->instance` (the `controller::class` object is stored)
- SoberWP `controller.php loader()` will `add_filter('sage/template/' . $template . '-data/data')` for all controllers (filter string/controller) with a method that return, (among other datas) the controller data we want
- Sage/WP will normally get `single-product.php` template (changed as `single-product.blade.php` in our `add_filter('template_include')` update) as template hierarchy rules
- Sage will `apply_filter('sage/template/' . $template . '-data/data')` (it uses `body_class()` to find the `$template` name to match the correct controllers with current template) to add data to the `blade->render` from `template_include` filter (that we override but just for the "single-product" and "archive-product" replace update)
- ATM (we are in `resources/views/woocommerce/single-product.blade.php`), the data are defined in `get_defined_data()` (as rendered from filter `template_include` with controllers data), so WE HAVE THE DATA HERE
- from here, we call `helpers::template()` [We don't have HTMl here! we call a method to output Woocommerce content] using `woocommerce` as template, and we send our vars (from `get_dedined_vars()`)
- `woocommerce.blade.php` will call `App\woocommerce_content(get_defined_vars())` ===> HERE we lose data if we don't pass `get_defined_vars()` as parameter
    - `App\woocommerce_content()` is overrided from Woocommerce `wp-content/plugins/woocommerce/includes/wc-template-functions.php`
    - to add a parameter to pass `$args`, which are our datas
- `App\woocommerce_content()` will output correct Woocommerce template hierarchy, passing `$args` to `wc_get_template_part()` it calls

## Future notes
- few test with [SoberWP 2.0.1](https://github.com/soberwp/controller/releases)
  - Few errors as default Namespace changes from `\App` to `\App\Controller`
  - But it should be fixable when Sage update to SoberWP Controller `2.0.1`
  - Will make a new release when Sage includes SoberWP controllers `^2.0.0`

## Disclaimer
 - Absolutely not tested with all Woocommerce templates and features
  - Checkout, My accounts, Emails... may needs more edits
  - Please test before using in production (I do and it runs smoothly tho)






