<?php
function mon_theme_assets() {
    $version = wp_get_theme()->get('Version');
    wp_enqueue_style('bootstrap', get_template_directory_uri() . '/assets/css/bootstrap.min.css', [], '5.3.8');
    wp_enqueue_style('mon-theme', get_template_directory_uri() . '/assets/css/main.css', ['bootstrap'], $version);
    wp_enqueue_script('bootstrap', get_template_directory_uri() . '/assets/js/bootstrap.bundle.min.js', '5.3.8', true);
    wp_enqueue_script('mon-theme', get_template_directory_uri() . '/assets/js/main.js', ['bootstrap'], $version, true);
    wp_enqueue_style(
        'remixicon',
        'https://cdn.jsdelivr.net/npm/remixicon@4.9.0/fonts/remixicon.css',
        [],
        '4.9.0'
    );
}
add_action('wp_enqueue_scripts', 'mon_theme_assets');
