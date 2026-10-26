
<?php
$post_type = get_post_type();
$post_id = get_the_ID();

$badge = 'Contenu';
$thematiques = [];

// Déterminer le type de contenu
if ($post_type === 'ressource') {

    $types = get_the_terms($post_id, 'type-de-contenu');

    if ($types && !is_wp_error($types)) {
        $badge = $types[0]->name;
    } else {
        $badge = 'Ressource';
    }

    $terms = get_the_terms($post_id, 'thematique');

    if ($terms && !is_wp_error($terms)) {
        $thematiques = $terms;
    }

} elseif ($post_type === 'page') {
    $badge = 'Page';
} elseif ($post_type === 'post') {
    $badge = 'Article';
}

// Résumé ACF ou extrait WordPress
$resume = '';

if ($post_type === 'ressource' && function_exists('get_field')) {
    $resume = get_field('resume', $post_id);
}

if (!is_string($resume) || trim($resume) === '') {
    $resume = get_the_excerpt();
}

$resume = wp_trim_words(
    wp_strip_all_tags($resume),
    15,
    '…'
);
?>

<article class="resourceCardHorizontal searchResultCard">

    <a
        href="<?php the_permalink(); ?>"
        class="searchResultCard__link"
        aria-label="<?php echo esc_attr(get_the_title()); ?>"
    >

        <div class="resourceCardHorizontal__inner">

            <div class="resourceCardHorizontal__image">

                <?php if (has_post_thumbnail()) : ?>

                    <?php the_post_thumbnail('medium_large', [
                        'loading' => 'lazy',
                    ]); ?>

                <?php else : ?>

                    <img
                        src="<?php echo esc_url(
                            get_template_directory_uri() .
                            '/assets/img/default_doc.png'
                        ); ?>"
                        alt=""
                        loading="lazy"
                    >

                <?php endif; ?>

            </div>

            <div class="resourceCardHorizontal__content">

                <div class="resourceCardHorizontal__meta">

                    <span class="resourceCard__badge resourceCardHorizontal__badge">
                        <?php echo esc_html($badge); ?>
                    </span>

                    <!--<?php foreach ($thematiques as $thematique) : ?>

                        <span class="resourceCard__theme resourceCard__badge">
                            <?php echo esc_html($thematique->name); ?>
                        </span>

                    <?php endforeach; ?>-->

                </div>

                <h2 class="resourceCardHorizontal__title">
                    <?php echo esc_html(get_the_title()); ?>
                </h2>

                <?php if ($resume) : ?>

                    <p class="resourceCardHorizontal__excerpt">
                        <?php echo esc_html($resume); ?>
                    </p>

                <?php endif; ?>

                <span class="resourceCardHorizontal__more">
                    Consulter
                    <i class="ri-arrow-right-up-line" aria-hidden="true"></i>
                </span>

            </div>

        </div>

    </a>

</article>
