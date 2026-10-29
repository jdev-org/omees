<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Filtres dans l'administration des ressources.
 */
add_action('restrict_manage_posts', function ($post_type) {

    if ($post_type !== 'ressource') {
        return;
    }

    $taxonomies = [
        'type-de-contenu' => 'Tous les types de contenu',
        'thematique'      => 'Toutes les thématiques',
    ];

    foreach ($taxonomies as $taxonomy => $label) {

        $selected = isset($_GET[$taxonomy]) && is_string($_GET[$taxonomy])
            ? sanitize_title(wp_unslash($_GET[$taxonomy]))
            : '';

        wp_dropdown_categories([
            'show_option_all' => $label,
            'taxonomy'        => $taxonomy,
            'name'            => $taxonomy,
            'orderby'         => 'name',
            'selected'        => $selected,
            'hierarchical'    => true,
            'hide_empty'      => false,
            'value_field'     => 'slug',
            'show_count'      => false,
        ]);
    }

}, 10, 1);


/**
 * Colonnes personnalisées dans la liste des ressources.
 */
add_filter('manage_ressource_posts_columns', function ($columns) {

    $columns['resource_type'] = 'Type de contenu';
    $columns['resource_theme'] = 'Thématiques';

    return $columns;
});

add_action('manage_ressource_posts_custom_column', function ($column, $post_id) {

    $taxonomy = match ($column) {
        'resource_type'  => 'type-de-contenu',
        'resource_theme' => 'thematique',
        default          => null,
    };

    if (!$taxonomy) {
        return;
    }

    $terms = get_the_terms($post_id, $taxonomy);

    if (empty($terms) || is_wp_error($terms)) {
        echo '—';
        return;
    }

    echo esc_html(implode(', ', wp_list_pluck($terms, 'name')));

}, 10, 2);
