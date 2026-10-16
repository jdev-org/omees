<?php get_header(); ?>

<?php get_template_part('template-parts/fixed-icon'); ?>

<?php
// Terme de taxonomie actuellement affiché
$term = get_queried_object();

// Champs ACF associés au terme
$icone_id = get_field('icone_de_la_thematique', 'term_' . $term->term_id);
$icone_url = $icone_id ? wp_get_attachment_url($icone_id) : '';

$description = term_description($term->term_id, 'thematique');

$comprendre = get_field('presentation_de_la_thematique', 'term_' . $term->term_id);
$pour_aller_plus_loin = get_field('pour_aller_plus_loin', 'term_' . $term->term_id);
?>

<main class="thematique">


    <section class="thematique__header bg-info text-white py-5"
    
        <?php if ($icone_url): ?>
        style="--icon-url: url('<?php echo esc_url($icone_url); ?>');"
    <?php endif; ?>>

        <div class="thematique__intro container-xxl py-5"
        >
            
                <div class="mb-4"><span class="singleBadge_header">NOS THÉMATIQUES</span></div>  
            <h1 class="thematique__title mb-4 col-md-9">
                <?php echo esc_html($term->name); ?>
            </h1>

            <?php if ($description): ?>
                <div class="thematique__description col-md-9">
                    <?php echo wp_kses_post($description); ?>
                </div>
            <?php endif; ?>

        </div>

        </section>
        <section>
            <div class="thematique__contain container-xxl my-5">
                <div class="row">
                    <div class="col-lg-3">
                                        <aside class="pageLayout__sidebar">
                                            <?php get_template_part('template-parts/sommaire'); ?>
                                        </aside>
                                    </div>
                                    <div class="col-lg-9 pageLayout__content">
                                            <section>
                                                <h2>Comprendre</h2>
                                                <?php echo wp_kses_post($comprendre); ?>
                                            </section>

                                        <section>
                                            <h2>Pour aller plus loin</h2>
                                            <?php echo wp_kses_post($pour_aller_plus_loin); ?>
                                        </section>

                                        <section>
                        <h2>Ressources associées</h2>

                        <?php $ressources = new WP_Query([
                            'post_type' => 'ressource',
                            'post_status' => 'publish',
                            'posts_per_page' => 6,
                            'orderby' => 'date',
                            'order' => 'DESC',
                            'tax_query' => [
                                [
                                    'taxonomy' => 'thematique',
                                    'field' => 'term_id',
                                    'terms' => (int) $term->term_id,
                                ],
                            ],
                        ]); ?>

                        <?php if ($ressources->have_posts()): ?>

                            <div class="row g-4">

                                <?php while ($ressources->have_posts()):
                                    $ressources->the_post(); ?>

                                    <div class="col-12 col-md-6 col-xl-4">
                                        <?php get_template_part('template-parts/components/ressource-card'); ?>
                                    </div>

                                <?php
                                endwhile; ?>

                            </div>

                        <?php else: ?>

                            <p>Aucune ressource associée à cette thématique pour le moment.</p>

                        <?php endif; ?>

                        <?php wp_reset_postdata(); ?>

                    </section>
                </div>
            </div>
                
            </div>
        </section>


</main>

<?php get_footer(); ?>
