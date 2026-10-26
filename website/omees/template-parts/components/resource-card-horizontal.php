<?php
$thematiques = get_the_terms(get_the_ID(), 'thematique');
$options = get_field('options_decryptage');

$resume = $options['resume'] ?? '';
?>

<article class="resourceCardHorizontal">

    <a href="<?php the_permalink(); ?>" class="resourceCardHorizontal__link">

        <div class="resourceCardHorizontal__image">
            <?php if (has_post_thumbnail()) : ?>
                <?php the_post_thumbnail('medium_large'); ?>
            <?php else : ?>
                <img
                    src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/default_doc.png'); ?>"
                    alt=""
                >
            <?php endif; ?>
        </div>

        <div class="resourceCardHorizontal__content">

            <div class="resourceCardHorizontal__meta">

                <span class="resourceCard__badge resourceCardHorizontal__badge">
                    Décryptage
                </span>

                <?php if ($thematiques && !is_wp_error($thematiques)) : ?>

                    <?php foreach ($thematiques as $thematique) : ?>

                        <span class="resourceCard__theme resourceCard__badge">
                            <?php echo esc_html($thematique->name); ?>
                        </span>

                    <?php endforeach; ?>

                <?php endif; ?>

            </div>

            <h3 class="resourceCardHorizontal__title">
                <?php the_title(); ?>
            </h3>

            <?php if (!empty($resume)) : ?>

                <p class="resourceCardHorizontal__excerpt">
                    <?php
                    echo esc_html(
                        wp_trim_words(
                            wp_strip_all_tags($resume),
                            35,
                            '…'
                        )
                    );
                    ?>
                </p>

            <?php endif; ?>

            <span class="resourceCardHorizontal__more">
                Lire le décryptage
                <i class="ri-arrow-right-up-line"></i>
            </span>

        </div>

    </a>

</article>