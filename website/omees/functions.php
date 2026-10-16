<?php
if (!defined('ABSPATH')) {
    exit();
}
require_once get_template_directory() . '/inc/setup.php';
require_once get_template_directory() . '/inc/enqueue.php';
require_once get_template_directory() . '/inc/theme-options.php';

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

add_filter(
    'render_block_core/file',
    function ($block_content, $block) {
        // Ne pas modifier le rendu dans l'éditeur
        if (is_admin()) {
            return $block_content;
        }

        $url = $block['attrs']['href'] ?? '';

        // Récupérer le nom du fichier depuis le bloc
        $titre = '';

        if (!empty($block['innerHTML'])) {
            $dom = new DOMDocument();

            libxml_use_internal_errors(true);
            $dom->loadHTML('<?xml encoding="utf-8" ?>' . $block['innerHTML']);
            libxml_clear_errors();

            foreach ($dom->getElementsByTagName('a') as $lien) {
                if (
                    !$lien->hasAttribute('class') ||
                    !str_contains($lien->getAttribute('class'), 'wp-block-file__button')
                ) {
                    $titre = trim($lien->textContent);
                    break;
                }
            }
        }

        if (!$url) {
            return $block_content;
        }

        ob_start();

        get_template_part('template-parts/components/document', null, [
            'url' => $url,
            'titre' => $titre,
        ]);

        return ob_get_clean();
    },
    10,
    2,
);

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

    $query->set('posts_per_page', 16);

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

function omees_filter_resources()
{
    check_ajax_referer('omees_resource_filters', 'nonce');

    $types =
        isset($_POST['types']) && is_array($_POST['types'])
            ? array_map('sanitize_title', wp_unslash($_POST['types']))
            : [];

    $thematiques =
        isset($_POST['thematiques']) && is_array($_POST['thematiques'])
            ? array_map('sanitize_title', wp_unslash($_POST['thematiques']))
            : [];

    $page = isset($_POST['page']) ? max(1, absint($_POST['page'])) : 1;

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

    $args = [
        'post_type' => 'ressource',
        'post_status' => 'publish',
        'posts_per_page' => 16,
        'paged' => $page,
    ];

    if (count($tax_query) > 1) {
        $args['tax_query'] = $tax_query;
    }

    $ressources = new WP_Query($args);

    ob_start();

    echo '<div class="row g-4">';

    if ($ressources->have_posts()) {
        while ($ressources->have_posts()) {
            $ressources->the_post();

            echo '<div class="col-12 col-md-6 col-lg-4">';
            get_template_part('template-parts/components/ressource-card');
            echo '</div>';
        }
    } else {
        echo '<div class="col-12"><p>Aucune ressource trouvée.</p></div>';
    }

    echo '</div>';

    get_template_part('template-parts/components/resource-pagination', null, [
        'total_pages' => (int) $ressources->max_num_pages,
        'current_page' => $page,
    ]);

    wp_reset_postdata();

    wp_send_json_success([
        'html' => ob_get_clean(),
        'total' => $ressources->found_posts,
    ]);
}
