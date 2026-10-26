<?php

$home_options = get_option('omees_home_options', []);

$decryptages_subtitle = $home_options['decryptages_subtitle'] ?? '';
/**
 * Accueil — Dernier décryptage et thématiques
 */

$decryptages = new WP_Query([
    'post_type'           => 'ressource',
    'post_status'         => 'publish',
    'posts_per_page'      => 1,
    'orderby'             => 'date',
    'order'               => 'DESC',
    'ignore_sticky_posts' => true,
    'tax_query'           => [
        [
            'taxonomy' => 'type-de-contenu',
            'field'    => 'slug',
            'terms'    => 'decryptage',
        ],
    ],
]);

if (!$decryptages->have_posts()) {
    return;
}

$decryptage = $decryptages->posts[0];

$titre   = get_the_title($decryptage);
$url     = get_permalink($decryptage);
$options = get_field('options_decryptage', $decryptage->ID);
$resume = wp_strip_all_tags($options['resume'] ?? '');
$image   = get_the_post_thumbnail_url($decryptage, 'large');
$extrait = get_the_excerpt($decryptage);

if (!$extrait) {
    $extrait = wp_trim_words(
        wp_strip_all_tags($decryptage->post_content),
        35,
        '…'
    );
}

// Récupérer les thématiques utilisées par les décryptages.
$ids_decryptages = get_posts([
    'post_type'      => 'ressource',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'fields'         => 'ids',
    'tax_query'      => [
        [
            'taxonomy' => 'type-de-contenu',
            'field'    => 'slug',
            'terms'    => 'decryptage',
        ],
    ],
]);

$thematiques = [];

if ($ids_decryptages) {
    $thematiques = wp_get_object_terms(
        $ids_decryptages,
        'thematique',
        [
            'orderby' => 'name',
            'order'   => 'ASC',
        ]
    );
}

if (is_wp_error($thematiques)) {
    $thematiques = [];
}
?>

        <div class="homeDecryptages__featured">

            <div class="homeDecryptages__image">
                <?php if ($image) : ?>
                    <img
                        src="<?php echo esc_url($image); ?>"
                        alt="<?php echo esc_attr(get_post_meta(get_post_thumbnail_id($decryptage), '_wp_attachment_image_alt', true)); ?>"
                        loading="lazy"
                    >
                <?php endif; ?>
            </div>

            <a
                class="homeDecryptages__card"
                href="<?php echo esc_url($url); ?>"
                aria-label="<?php echo esc_attr('Lire le décryptage : ' . $titre); ?>"
            >
                <div class="homeDecryptages__cardBody">

                    <span class="homeDecryptages__eyebrow">
                        à la une
                    </span>

                    <h3 class="homeDecryptages__title">
                        <?php echo esc_html($titre); ?>
                    </h3>

                    <?php if ($extrait) : ?>
                        <p class="homeDecryptages__excerpt">
                            <?php echo esc_html(wp_trim_words($resume, 35, '…')); ?>
                        </p>
                    <?php endif; ?>

                </div>

                <div class="homeDecryptages__cardFooter">

                    <span class="homeDecryptages__read">
                        Lire
                    </span>

                    <span class="homeDecryptages__arrow" aria-hidden="true">
                        <i class="ri-arrow-right-up-line"></i>
                    </span>

                </div>
            </a>

        </div>

        <?php if ($thematiques) : ?>

            <div class="homeDecryptages__topics">

                <h3 class="homeDecryptages__topicsTitle">
                    <?php echo wp_kses_post($decryptages_subtitle); ?>
                </h3>

                <div class="homeDecryptages__tags">

                    <?php foreach ($thematiques as $thematique) : ?>

                         <a
                            class="homeDecryptages__tag"
                            href="<?php echo esc_url(
                                add_query_arg(
                                    'thematiques',
                                    $thematique->slug,
                                    home_url('/decryptages/')
                                )
                            ); ?>"
                        >
                            <?php echo esc_html($thematique->name); ?>
                        </a>

                    <?php endforeach; ?>

                </div>

                <a
                    class="homeDecryptages__all"
                    href="<?php echo esc_url(home_url('/decryptages/')); ?>"
                >
                    Voir tous les décryptages
                    <i class="ri-arrow-right-up-line" aria-hidden="true"></i>
                </a>

            </div>

        <?php endif; ?>

