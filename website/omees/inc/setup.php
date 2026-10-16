<?php
function mon_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form','comment-form','comment-list','gallery','caption','style','script']);
    register_nav_menus([
        'primary' => __('Menu principal', 'mon-theme'),
        'footer'  => __('Menu footer', 'mon-theme'),
    ]);
}
add_action('after_setup_theme', 'mon_theme_setup');
