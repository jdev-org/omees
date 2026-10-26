<?php
$fixed_type = sanitize_title($args['fixed_type'] ?? '');
$title = $args['title'] ?? 'Médiathèque';
$description = $args['description'] ?? '';

$types = isset($_GET['types'])
    ? array_filter(array_map('sanitize_title', explode(',', sanitize_text_field(wp_unslash($_GET['types'])))))
    : [];

$thematiques = isset($_GET['thematiques'])
    ? array_filter(array_map('sanitize_title', explode(',', sanitize_text_field(wp_unslash($_GET['thematiques'])))))
    : [];

$paged = isset($_GET['paged'])
    ? max(1, absint($_GET['paged']))
    : max(1, get_query_var('paged'));

$query = omees_get_resources_query([
    'fixed_type' => $fixed_type,
    'types' => $types,
    'thematiques' => $thematiques,
    'paged' => $paged,
]);
?>

<div class="container py-3">

    <h1 >
        <?php echo esc_html($title); ?>
    </h1>

    <?php if ($description) : ?>
        <p class="lead lead__bold mb-5">
            <?php echo esc_html($description); ?>
        </p>
    <?php endif; ?>

    <?php
    get_template_part('template-parts/components/resource-filters', null, [
        'fixed_type' => $fixed_type,
        'types' => $types,
        'thematiques' => $thematiques,
    ]);
    ?>

    <p id="resourceCount" class="resourceCount my-4 d-none" aria-live="polite">
        <?php
        printf(
            esc_html(_n('%s ressource trouvée', '%s ressources trouvées', $query->found_posts, 'omees')),
            number_format_i18n($query->found_posts)
        );
        ?>
    </p>

    <div id="resourceResults" class="my-5" aria-live="polite">

        <?php
        get_template_part('template-parts/components/resource-results', null, [
            'query' => $query,
            'fixed_type' => $fixed_type,
        ]);
        ?>

    </div>

</div>