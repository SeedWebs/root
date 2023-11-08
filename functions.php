<?php

require_once('config.php');

add_theme_support('post-thumbnails');


/* REST API */
add_action('rest_api_init', 'root_register_rest');
function root_register_rest()
{
    register_rest_route('root/v1', '/' . CPT_1, [
        'methods'  => 'GET',
        'callback' => 'root_rest_cpt_1'
    ]);
    register_rest_route('root/v1', '/' . CPT_2, [
        'methods'  => 'GET',
        'callback' => 'root_rest_cpt_2'
    ]);
}

// CPT 1 - Post (News)
function root_rest_cpt_1($request)
{
    $return = [];
    $slug = $request->get_param('slug');
    if ($slug) {
        $args = [
            'post_type'   => [CPT_1],
            'name'        => $slug,
            'posts_per_page' => 1
        ];
    } else {
        $args = [
            'post_type'   => [CPT_1],
            'posts_per_page' => 100
        ];
    }
    $the_query = new WP_Query($args);
    if ($the_query->have_posts()) {
        while ($the_query->have_posts()) {
            $the_query->the_post();

            $post_data = [
                'id'        => get_the_ID(),
                'slug'      => get_post_field('post_name'),
                'title'     => get_the_title(),
                'date'      => get_the_date('j F Y'),
                'datetime'  => get_the_date('Y-m-d'),
                'd'         => get_the_date('j'),
                'm'         => get_the_date('n'),
                'y'         => get_the_date('y'),
                'excerpt'   => get_the_excerpt(),
                'content'   => get_the_content(),
                'image'     => get_the_post_thumbnail_url(get_the_ID(), 'full'),
            ];

            $return[] = $post_data;
        }
        wp_reset_postdata();
    }
    return $return;
}

// CPT 2 - Dessert, such as
function root_rest_cpt_2($request)
{
    $return = [];
    $slug = $request->get_param('slug');
    if ($slug) {
        $args = [
            'post_type'   => [CPT_2],
            'name'        => $slug,
            'posts_per_page' => 1
        ];
    } else {
        $args = [
            'post_type'   => [CPT_2],
            'posts_per_page' => 100
        ];
    }
    $the_query = new WP_Query($args);
    if ($the_query->have_posts()) {
        while ($the_query->have_posts()) {
            $the_query->the_post();

            if(function_exists('get_field')) {
                $content = get_field('content');
                if (!$content) {
                    $content = get_the_content();
                }
            } else {
                $content = get_the_content();
            }

            $post_data = [
                'id'        => get_the_ID(),
                'slug'      => get_post_field('post_name'),
                'title'     => get_the_title(),
                'excerpt'   => get_the_excerpt(),
                'content'   => $content,
                'thumb'     => get_the_post_thumbnail_url(get_the_ID(), 'medium_large'),
                'image'     => get_the_post_thumbnail_url(get_the_ID(), 'full'),
            ];

            $return[] = $post_data;
        }
        wp_reset_postdata();
    }
    return $return;
}


/* REDIRECT GUEST */
add_action('template_redirect', 'redirect_guest');
function redirect_guest()
{
    if (!is_user_logged_in() && !is_page(LOGIN_SLUG)) {
        wp_redirect(LIVE_URL);
        exit;
    }
}


/* NUMERIC SLUG */
/*
add_action('wp_insert_post', 'change_slug');
function change_slug($post_id, $force = false)
{
    $post_types = ['post'];
    $post_type  = $_POST['post_type'];

    if (!in_array($post_type, $post_types) || get_field('no', $post_id)) {
        return;
    }

    $i_no = new WP_Query([
        'post_type'  => $post_type,
        'posts_per_page' => 1,
        'meta_key' => 'no',
        'orderby' => 'meta_value_num',
        'order' => 'DESC',
    ]);

    while ($i_no->have_posts()) {
        $i_no->the_post();
        $max = get_field('no');
    }
    if (!$max) {
        $max = 0;
    }
    $new_no = (int)$max + 1;

    update_field('no', $new_no, $post_id);

    wp_update_post(array(
        'ID' => $post_id,
        'post_name' => $new_no
    ));
}
*/
