<?php
/**
 * Template Name: Page avec sommaire
 * Template Post Type: page
 */

get_header(); ?>

<div class="page omees__contentMain">

    <?php while (have_posts()):
        the_post(); ?>

        <div class="page__header">
            <div class="container py-5">            
                <h1 class="mb-4"><?php the_title(); ?></h1>

                <?php if (has_excerpt()): ?>
                    <p class="page__summary">
                        <?php echo esc_html(get_the_excerpt()); ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <div class="container-xxl my-5 page__content">
            <div class="row">
                <div class="col-md-3">
                    <aside class="pageLayout__sidebar">
                        <?php get_template_part('template-parts/sommaire'); ?>
                    </aside>
                </div>  
                <div class="offset-md-1 col-md-8 pageLayout__content">
                    <?php the_content(); ?>
                </div>  
            </div>
        </div>

    <?php
    endwhile; ?>

</div>

<?php get_footer(); ?>
