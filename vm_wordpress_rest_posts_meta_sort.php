<?php
/**
 * Plugin Name:     vmpublishing/vm_wordpress_rest_posts_meta_sort
 * Plugin URI:      github.com/vmpublishing/vm_wordpress_rest_posts_meta_sort
 * Description:     PLUGIN DESCRIPTION HERE
 * Author:          Dirk Gustke
 * Text Domain:     vmpublishing/vm_wordpress_rest_posts_meta_sort
 * Version:         0.1.0
 *
 * @package         vmpublishing/vm_wordpress_rest_posts_meta_sort
 */


// taken from https://github.com/WP-API/WP-API/issues/2308#issuecomment-262886432
add_filter(
    'rest_endpoints',
    function ($routes) {
        // I'm modifying multiple types here, you won't need the loop if you're just doing posts
        foreach (['posts'] as $type) {
            if (!($route =& $routes['/wp/v2/' . $type])) {
                continue;
            }

            // Allow ordering by my meta value
            $route[0]['args']['orderby']['enum'][] = 'meta_value_num';

            // Allow only the meta keys that I want
            $route[0]['args']['meta_key'] = array(
                'description'       => 'The meta key to query.',
                'type'              => 'string',
                'enum'              => ['article_score'],
                'validate_callback' => 'rest_validate_request_arg',
            );
        }

        return $routes;
    }
);

// additionally taken from https://github.com/WP-API/WP-API/issues/2308#issuecomment-265875108
add_filter(
    'rest_posts_query',
    function ($args, $request) {
        if ($key = $request->get_param('meta_key')) {
            $args['meta_key'] = $key;
        }
        return $args;
    },
    10,
    2
);
