<?php
if (!defined('ABSPATH')) {
    exit();
}
require_once get_template_directory() . '/inc/setup.php';
require_once get_template_directory() . '/inc/enqueue.php';
require_once get_template_directory() . '/inc/theme-options.php';
require_once get_template_directory() . '/inc/home-options.php';
require_once get_template_directory() . '/inc/catalogues-options.php';

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('sommaire', get_template_directory_uri() . '/assets/css/sommaire.css', [], '1.0');

    wp_enqueue_script('sommaire', get_template_directory_uri() . '/assets/js/sommaire.js', [], '1.0', true);
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

add_action('after_setup_theme', function () {
    // Activer les styles personnalisés dans Gutenberg
    add_theme_support('editor-styles');

    // Charger la feuille de style de l'éditeur
    add_editor_style('assets/css/editor-style.css');
});

/* Document block custom */

add_filter('render_block_core/file', function ($block_content, $block) {

    if (is_admin()) {
        return $block_content;
    }

    $url = $block['attrs']['href'] ?? '';
    $titre = '';
    $label = 'Télécharger le fichier';

    if (!empty($block['innerHTML'])) {
        $dom = new DOMDocument();

        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $block['innerHTML']);
        libxml_clear_errors();

        foreach ($dom->getElementsByTagName('a') as $lien) {

            $classes = $lien->getAttribute('class');

            if (str_contains($classes, 'wp-block-file__button')) {
                // Texte du bouton Gutenberg
                $texte = trim($lien->textContent);

                if ($texte !== '') {
                    $label = $texte;
                }
            } else {
                // Nom du fichier
                $titre = trim($lien->textContent);
            }
        }
    }

    if (!$url) {
        return $block_content;
    }

    ob_start();

    get_template_part('template-parts/components/document', null, [
        'url'   => $url,
        'titre' => $titre,
        'label' => $label,
    ]);

    return ob_get_clean();

}, 10, 2);

add_filter('single_template', function ($template) {
    if (is_singular('ressource') && has_term('decryptage', 'type-de-contenu', get_queried_object_id())) {
        $custom_template = locate_template('single-ressource-decryptage.php');

        if ($custom_template) {
            return $custom_template;
        }
    }

    return $template;
});

add_action('init', function () {
    add_post_type_support('page', 'excerpt');
});

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

add_action('wp_ajax_filter_resources', 'omees_filter_resources');
add_action('wp_ajax_nopriv_filter_resources', 'omees_filter_resources');

function omees_filter_resources() {

    check_ajax_referer('omees_resource_filters', 'nonce');

    $types = isset($_POST['types']) && is_array($_POST['types'])
        ? array_map('sanitize_title', wp_unslash($_POST['types']))
        : [];

    $thematiques = isset($_POST['thematiques']) && is_array($_POST['thematiques'])
        ? array_map('sanitize_title', wp_unslash($_POST['thematiques']))
        : [];

    $fixed_type = isset($_POST['fixed_type']) && is_string($_POST['fixed_type'])
        ? sanitize_title(wp_unslash($_POST['fixed_type']))
        : '';

    // Types autorisés pour les pages spécialisées.
    $allowed_fixed_types = ['carte', 'decryptage'];

    if (!in_array($fixed_type, $allowed_fixed_types, true)) {
        $fixed_type = '';
    }

    $page = isset($_POST['page'])
        ? max(1, absint($_POST['page']))
        : 1;

    $query = omees_get_resources_query([
        'fixed_type' => $fixed_type,
        'types' => $types,
        'thematiques' => $thematiques,
        'paged' => $page,
    ]);

    $total = (int) $query->found_posts;

    ob_start();

    get_template_part('template-parts/components/resource-results', null, [
        'query' => $query,
        'fixed_type' => $fixed_type,
    ]);

    $html = ob_get_clean();

    wp_send_json_success([
        'html' => $html,
        'total' => $total,
    ]);
}
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

/**
 * Styles des blocs Gutenberg — Front-end
 */
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'omees-gutenberg-blocks',
        get_template_directory_uri() . '/assets/css/gutenberg-blocks.css',
        [],
        filemtime(get_template_directory() . '/assets/css/gutenberg-blocks.css')
    );
});

/**
 * Styles des blocs Gutenberg — Éditeur
 */
add_action('after_setup_theme', function () {
    add_theme_support('editor-styles');

    add_editor_style('assets/css/gutenberg-blocks.css');
});

/* Lien externe — Bloc Bouton Gutenberg */

add_filter('render_block_core/button', function ($block_content, $block) {

    // Conserver le bouton normal dans l'éditeur
    if (is_admin()) {
        return $block_content;
    }

    // Ne transformer que les boutons avec cette classe
    $classes = $block['attrs']['className'] ?? '';

    if (!in_array('is-style-external-card', preg_split('/\s+/', $classes), true)) {
        return $block_content;
    }

    $url = '';
    $label = '';

    if (!empty($block_content)) {

        $dom = new DOMDocument();

        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $block_content);
        libxml_clear_errors();

        foreach ($dom->getElementsByTagName('a') as $lien) {
            $url = $lien->getAttribute('href');
            $label = trim($lien->textContent);
            break;
        }
    }

    if (!$url) {
        return $block_content;
    }

    ob_start();

    get_template_part('template-parts/components/external-card', null, [
        'url'   => $url,
        'label' => $label,
    ]);

    return ob_get_clean();

}, 10, 2);

add_action('init', function () {
    register_block_style('core/button', [
        'name'  => 'external-card',
        'label' => 'Lien externe',
    ]);
});
