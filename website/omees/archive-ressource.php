<?php get_header(); ?>
<div class="container py-3">
    <h1 class="mb-3">Médiathèque</h1>
    <p class="lead lead__bold">Retrouvez dans la médiathèque toutes les ressources pour comprendre, explorer et approfondir les travaux de l'OMEES.</p>
    
    <div class="row">
        <div class="col-lg-3 sticky-top">
            <?php get_template_part('template-parts/components/ressource-filters'); ?>
        </div>
        <div class="col-lg-9">

    

    <div id="resourceResults" aria-live="polite">

        <div class="row g-4">

            <?php if (have_posts()): ?>

                <?php while (have_posts()):
                    the_post(); ?>

                    <div class="col-12 col-md-6 col-lg-4">
                        <?php get_template_part('template-parts/components/ressource-card'); ?>
                    </div>

                <?php
                endwhile; ?>

            <?php else: ?>

                <div class="col-12">
                    <p>Aucune ressource trouvée.</p>
                </div>

            <?php endif; ?>

        </div>

        <div class="mt-5">
            <?php
            global $wp_query;

            get_template_part('template-parts/components/resource-pagination', null, [
                'total_pages' => (int) $wp_query->max_num_pages,
                'current_page' => max(1, get_query_var('paged')),
            ]);
            ?>
        </div>

    </div>
    </div>
    </div>
</div>
<?php get_footer(); ?>
