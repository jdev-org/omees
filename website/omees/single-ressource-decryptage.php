<?php
$options = get_field('options_decryptage');

$resume = $options['resume'] ?? '';
$recommandations = $options['recommandations'] ?? '';

$pour_aller_plus_loin = $options['pour_aller_plus_loin'] ?? '';

$points = $options['les_points_a_retenir'] ?? [];

$points_affiches = [];

for ($i = 1; $i <= 5; $i++) {
    $point = trim($points['point_' . $i] ?? '');

    if ($point !== '') {
        $points_affiches[] = $point;
    }
}
?>

<?php get_header(); ?>

    <?php while (have_posts()):
        the_post(); ?>
    

    <article class="single" <?php post_class(); ?>>
        <div class="single__header bg-info text-white">
            <div class="container py-5">   
                <div class="mb-4"><span class="singleBadge_header">NOS DECRYPTAGES</span></div>  
                <h1 class="mb-4"><?php the_title(); ?></h1>
                <p><?php echo wp_kses_post($resume); ?></p>

                <p class="my-4">
                    Publié le <?php echo esc_html(get_the_date('d/m/Y')); ?>
                    | Mis à jour le <?php echo esc_html(get_the_modified_date('d/m/Y')); ?>
                </p>
                
            </div>
        </div>
        <div class="single__content">
            <div class="container py-5">
                <div class="row">
                    <div class="col-md-3">
                        <?php
                        // Si "experts" est un champ ACF indépendant :
                        $experts = $options['experts'] ?? [];

                        // Si "experts" est dans ton groupe $options,
                        // utilise plutôt :
                        // $experts = $options['experts'] ?? [];

                        $experts_affiches = [];

                        for ($i = 1; $i <= 5; $i++) {
                            $expert = $experts['expert_' . $i] ?? [];

                            $nom = trim($expert['nom'] ?? '');
                            $fonction = trim($expert['fonction__role'] ?? '');

                            // On ignore les experts entièrement vides
                            if ($nom === '' && $fonction === '') {
                                continue;
                            }

                            $experts_affiches[] = [
                                'nom' => $nom,
                                'fonction' => $fonction,
                            ];
                        }

                        $thematiques = get_the_terms(get_the_ID(), 'thematique');
                        ?>

                    <aside class="decryptageInfos">

                        <?php if (!empty($experts_affiches)): ?>

                            <div class="decryptageInfos__section">

                                <p class="decryptageInfos__title">
                                    Expert(s)
                        </p>

                                <div class="decryptageInfos__experts">

                                    <?php foreach ($experts_affiches as $expert): ?>

                                        <div class="decryptageInfos__expert mb-3">
                                            <i class="ri-account-circle-line"></i>
                                            <div>

                                            <?php if ($expert['nom']): ?>
                                                <div class="decryptageInfos__expertName">
                                                    <?php echo esc_html($expert['nom']); ?>
                                                </div>
                                            <?php endif; ?>

                                            <?php if ($expert['fonction']): ?>
                                                <div class="decryptageInfos__affiliation">
                                                    <?php echo esc_html($expert['fonction']); ?>
                                                </div>
                                            <?php endif; ?>
                                            </div>

                                        </div>

                                    <?php endforeach; ?>

                                </div>

                            </div>

                        <?php endif; ?>

                        <?php if (!empty($thematiques) && !is_wp_error($thematiques)): ?>

                            <div class="decryptageInfos__section mt-4">

                                <p class="decryptageInfos__title">
                                    Thématique(s)
                        </p>

                                <div class="decryptageInfos__tags">

                                    <?php foreach ($thematiques as $thematique): ?>

                                        <a
                                            href="<?php echo esc_url(get_term_link($thematique)); ?>"
                                            class="decryptageInfos__tag"
                                        >
                                            <?php echo esc_html($thematique->name); ?>
                                        </a>

                                    <?php endforeach; ?>

                                </div>

                            </div>

                        <?php endif; ?>

                    </aside>
                    <aside class="pageLayout__sidebar mt-5">
                        <?php get_template_part('template-parts/sommaire'); ?>
                    </aside>
                    </div>
                <div class="col-md-9 pageLayout__content">
                        <!-- Img mise en avant -->
                        <?php if (has_post_thumbnail()): ?>

                            <?php if (has_post_thumbnail()): ?>

                            <?php
                            // Récupérer l'ID de l'image mise en avant
                            $image_id = get_post_thumbnail_id();

                            // Récupérer sa légende
                            $legende = wp_get_attachment_caption($image_id);
                            ?>

                            <figure class="single__thumbnail mb-4">

                                <?php the_post_thumbnail('large', [
                                    'class' => 'img-fluid media__rounded',
                                    'loading' => 'eager',
                                ]); ?>

                                <?php if ($legende): ?>
                                    <figcaption class="my-2">
                                        <?php echo esc_html($legende); ?>
                                    </figcaption>
                                <?php endif; ?>

                            </figure>

                            <?php endif; ?>

                        <?php endif; ?>
                        <!-- /. Img mise en avant -->

                        <!-- Les points à retenir -->
                        <?php if (!empty($points_affiches)): ?>

                        <div class="decryptage__highlight text-info my-4" style="border: 2px solid #2d6079;">
                            <div class="decryptageHighlight__title mb-2">
                                <h2>Les points à retenir</h2>
                                <i class="ri-bookmark-3-line"></i>
                                </div>

                                <div class="decryptage__keyPoints">

                                    <?php foreach ($points_affiches as $point): ?>

                                        <div class="decryptage__keyPoint">
                                            <i class="ri-arrow-right-line"></i>
                                            <?php echo esc_html($point); ?>
                                    </div>

                                    <?php endforeach; ?>

                                </div>
                        </div>
                        <?php endif; ?>
                        <!-- /. Les points à retenir -->

                        <?php the_content(); ?>

                        <!-- Recommandations -->
                        <div class="decryptage__highlight bg-info text-white my-4">
                            <div class="decryptageHighlight__title">
                                <h2>Recommandations</h2>
                                <i class="ri-verified-badge-line"></i>
                                </div>
                            <p><?php echo wp_kses_post($recommandations); ?></p>
                        </div>
                        <!-- /. Recommandations -->

                        <!-- Pour eller plus loin -->
                        <div>
                            <hr class="my-5">
                            <h2 >Pour aller plus loin</h2>
                            <div><?php echo wp_kses_post($pour_aller_plus_loin); ?></div>
                        </div>
                        <!-- /. Pour eller plus loin -->

                    </div>                
                </div>                
            </div>
        </div>
    </article>

    <?php
    endwhile; ?>


<?php get_footer(); ?>
