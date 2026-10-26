<?php
$post_id = get_the_ID();

// Type de contenu
$types = get_the_terms($post_id, 'type-de-contenu');
$type = (!empty($types) && !is_wp_error($types))
    ? reset($types)
    : null;

// Thématiques
$thematiques = get_the_terms($post_id, 'thematique');

// Image mise en avant
$image_url = get_the_post_thumbnail_url($post_id, 'medium_large');

if (!$image_url) {
    $image_url = get_template_directory_uri() . '/assets/img/default_img.png';
}
?>

<article class="resourceCard">

    <a href="<?php echo esc_url(get_permalink()); ?>" class="resourceCard__link">

        <div class="resourceCard__image">

            <img
                src="<?php echo esc_url($image_url); ?>"
                alt=""
                loading="lazy"
            >

            <div class="resourceCard__badgeGrid">

                <?php if ($type) : ?>
                    <span class="resourceCard__badge resourceCard__type">
                        <?php echo esc_html($type->name); ?>
                    </span>
                <?php endif; ?>

                <?php if (!empty($thematiques) && !is_wp_error($thematiques)) : ?>

                    <?php foreach (array_slice($thematiques, 0, 2) as $thematique) : ?>

                        <span
                            class="resourceCard__badge resourceCard__theme"
                            title="<?php echo esc_attr($thematique->name); ?>"
                        >
                            <?php echo esc_html($thematique->name); ?>
                        </span>

                    <?php endforeach; ?>

                <?php endif; ?>

            </div>

        </div>

        <h3 class="resourceCard__title">
            <?php echo esc_html(get_the_title()); ?>
        </h3>

    </a>

</article>