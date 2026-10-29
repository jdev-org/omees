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

add_action('after_setup_theme', function () {
    // Activer les styles personnalisés dans Gutenberg
    add_theme_support('editor-styles');

    // Charger la feuille de style de l'éditeur
    add_editor_style('assets/css/editor-style.css');
});

add_action('init', function () {
    add_post_type_support('page', 'excerpt');
});

/**
 * Styles des blocs Gutenberg — Éditeur
 */
add_action('after_setup_theme', function () {
    add_theme_support('editor-styles');

    add_editor_style('assets/css/gutenberg-blocks.css');
});
