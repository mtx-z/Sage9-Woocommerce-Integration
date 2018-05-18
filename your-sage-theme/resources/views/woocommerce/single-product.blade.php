{{--

How blade for Woocommerce:

- we add_filter on 'template_include' to edit 'single-product' and 'archive-product' template path to .blade
- those templates includes woocommerce.blade.php templates
- woocommerce.blade.php calls our overrided App\woocommerce_content(get_defined_vars()) that output woocommerce content

How soberWP data for Woocommerce :

- SoberWP Controller.php (new Loader()) will parse all controller and methods and store them using "slugified" $template name as keys in $this->instance (the controlle::class)
- SoberWP controller.php loader() will add filter "sage/template/' . $template . '-data/data'" for template (filter string) with a method that return, (with other) the controller data we want
- Sage/WP will normally get single-product.php (changed as single-product.blade.php in our add_filter 'template_include') as template hierarchy rules
- Sage will apply_filter "sage/template/' . $template . '-data/data'" (it uses body_class to find the $template name to match the current template) to add data to the blade->render from 'template_include' filter (that we overrided but just for the "single-product" and "archive-product" replace update)
- ATM (we are in THIS file), the data are defined in get_defined_data() (as rendered from filter template_include with data), so WE HAVE THE DATA HERE
- from here, we call helpers::template() [its not directly our template!, we call a method to output woocommerce content] using 'woocommerce' as template, and we send our vars
- woocommerce.blade.php will call App\woocommerce_content(get_defined_vars()) ===> HERE we lose data if we don't pass get_defined_vars() as parameter
    - App\woocommerce_content() is overrided from Woocommerce "wp-content/plugins/woocommerce/includes/wc-template-functions.php"
    - to add a parameter to pass $args, which are our datas
- App\woocommerce_content() will output correct Woocommerce template hierarchy, passing $args to wc_get_template_part() it calls
--}}

{!! \App\Template('woocommerce', get_defined_vars()) !!}
