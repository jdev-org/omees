<?php
function mon_theme_assets() {
    $version = wp_get_theme()->get('Version');
    wp_enqueue_style('bootstrap', get_template_directory_uri() . '/assets/css/bootstrap.min.css', [], '5.3.8');
    wp_enqueue_style('mon-theme', get_template_directory_uri() . '/assets/css/main.css', ['bootstrap'], $version);
    wp_enqueue_script('bootstrap', get_template_directory_uri() . '/assets/js/bootstrap.bundle.min.js', [], '5.3.8', true);
    wp_enqueue_script('omees-heading-ids', get_template_directory_uri() . '/assets/js/heading-ids.js', [], $version, true);
    wp_enqueue_script('mon-theme', get_template_directory_uri() . '/assets/js/main.js', ['bootstrap', 'omees-heading-ids'], $version, true);
    
    wp_enqueue_style(
        'remixicon',
        'https://cdn.jsdelivr.net/npm/remixicon@4.9.0/fonts/remixicon.css',
        [],
        '4.9.0'
    );
}
add_action('wp_enqueue_scripts', 'mon_theme_assets');

add_action('wp_enqueue_scripts', function () {

    /*if (!is_page_template('page-catalogue-donnees.php')) {
        return;
    }*/

    wp_enqueue_script(
        'omees-geonetwork-ui',
        /*'https://cdn.jsdelivr.net/gh/geonetwork/geonetwork-ui@wc-dist-v2.9.0/gn-wc.js',*/
        get_template_directory_uri() . '/assets/js/gn-wc.js',
        [],
        '2.9.0',
        true
    );
});



add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'omees-print',
        get_template_directory_uri() . '/assets/css/print.css',
        [],
        '1.0',
        'print'
    );
});

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('sommaire', get_template_directory_uri() . '/assets/css/sommaire.css', [], '1.0');

    wp_enqueue_script('sommaire', get_template_directory_uri() . '/assets/js/sommaire.js', ['mon-theme', 'omees-heading-ids'], '1.0', true);
});

/**
 * Masquer les panneaux Thématiques et Types de contenu
 * uniquement dans l'éditeur Gutenberg des ressources.
 */
add_action('enqueue_block_editor_assets', function () {
    $screen = get_current_screen();

    if (!$screen || $screen->post_type !== 'ressource') {
        return;
    }

    wp_add_inline_script(
        'wp-edit-post',
        "
        wp.domReady(function () {

            var editor = wp.data.dispatch('core/edit-post');

            editor.removeEditorPanel('taxonomy-panel-thematique');
            editor.removeEditorPanel('taxonomy-panel-type-de-contenu');

        });
        ",
    );
});
