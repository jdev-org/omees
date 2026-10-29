<?php
if (!defined('ABSPATH')) {
    exit;
}

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
