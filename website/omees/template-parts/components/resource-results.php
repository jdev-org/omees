
<?php
$query = $args['query'] ?? null;
$fixed_type = $args['fixed_type'] ?? '';

if (!$query instanceof WP_Query) {
    return;
}

// Affichage horizontal uniquement pour les Décryptages
$is_horizontal = $fixed_type === 'decryptage';

$column_class = $is_horizontal
    ? 'col-12'
    : 'col-12 col-md-6 col-lg-3';

$card_template = $is_horizontal
    ? 'template-parts/components/resource-card-horizontal'
    : 'template-parts/components/resource-card';
?>

<div class="row g-4">

    <?php if ($query->have_posts()) : ?>

        <?php while ($query->have_posts()) : $query->the_post(); ?>

            <div class="<?php echo esc_attr($column_class); ?>">

                <?php get_template_part($card_template); ?>

            </div>

        <?php endwhile; ?>

    <?php else : ?>

        <div class="col-12 col-md-6 offset-md-3 text-center my-5">

            <?php
            get_template_part('template-parts/components/resource-empty', null, [
                'title' => 'Aucun résultat',
                'message' => 'Essayez de modifier ou de retirer certains filtres pour découvrir davantage de ressources.',
                'heading_tag' => 'h6',
            ]);
            ?>

        </div>

    <?php endif; ?>

</div>

<?php
get_template_part('template-parts/components/resource-pagination', null, [
    'total_pages'  => (int) $query->max_num_pages,
    'current_page' => max(1, (int) $query->get('paged')),
]);

wp_reset_postdata();
?>
