<?php
if (!defined('ABSPATH')) {
    exit;
}

/* Cusotm filtre */

add_action('pre_get_posts', function ($query) {
    if (is_admin() || !$query->is_main_query() || !$query->is_post_type_archive('ressource')) {
        return;
    }

    $query->set('posts_per_page', 12);

    $filtres = [
        'types' => 'type-de-contenu',
        'thematiques' => 'thematique',
    ];

    $tax_query = ['relation' => 'AND'];

    foreach ($filtres as $parametre => $taxonomie) {
        $valeur =
            isset($_GET[$parametre]) && is_string($_GET[$parametre])
                ? sanitize_text_field(wp_unslash($_GET[$parametre]))
                : '';

        if ($valeur === '') {
            continue;
        }

        $slugs = array_filter(array_map('sanitize_title', explode(',', $valeur)));

        if (!empty($slugs)) {
            $tax_query[] = [
                'taxonomy' => $taxonomie,
                'field' => 'slug',
                'terms' => $slugs,
                'operator' => 'IN',
            ];
        }
    }

    if (count($tax_query) > 1) {
        $query->set('tax_query', $tax_query);
    }
});

/**
 * Construit une requête de ressources.
 */
function omees_get_resources_query($args = []) {

    $defaults = [
        'fixed_type' => '',
        'types' => [],
        'thematiques' => [],
        'paged' => 1,
        'posts_per_page' => 12,
    ];

    $args = wp_parse_args($args, $defaults);

    $fixed_type = sanitize_title($args['fixed_type']);

    $types = array_map('sanitize_title', (array) $args['types']);
    $thematiques = array_map('sanitize_title', (array) $args['thematiques']);

    // Un type fixe est prioritaire sur les filtres sélectionnés.
    if ($fixed_type) {
        $types = [$fixed_type];
    }

    $tax_query = ['relation' => 'AND'];

    if (!empty($types)) {
        $tax_query[] = [
            'taxonomy' => 'type-de-contenu',
            'field' => 'slug',
            'terms' => $types,
            'operator' => 'IN',
        ];
    }

    if (!empty($thematiques)) {
        $tax_query[] = [
            'taxonomy' => 'thematique',
            'field' => 'slug',
            'terms' => $thematiques,
            'operator' => 'IN',
        ];
    }

    $query_args = [
        'post_type' => 'ressource',
        'post_status' => 'publish',
        'posts_per_page' => absint($args['posts_per_page']),
        'paged' => max(1, absint($args['paged'])),
    ];

    if (count($tax_query) > 1) {
        $query_args['tax_query'] = $tax_query;
    }

    return new WP_Query($query_args);
}

/**
 * Inclure les pages, articles et ressources
 * dans la recherche WordPress.
 */
add_action('pre_get_posts', function ($query) {

    if (
        is_admin() ||
        !$query->is_main_query() ||
        !$query->is_search()
    ) {
        return;
    }

    $query->set('post_type', [
        'page',
        'post',
        'ressource',
    ]);

    $query->set('posts_per_page', 12);
});
