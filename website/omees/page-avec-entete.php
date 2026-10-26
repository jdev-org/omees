<?php
/**
 * Template Name: Page avec entête
 * Template Post Type: page
 */

get_header(); ?>

<div class="page omees__contentMain">

    <?php while (have_posts()):
        the_post(); ?>

        <div class="page__header">
            <div class="container p-5">            
                <h1 class="mb-4"><?php the_title(); ?></h1>

                <?php if (has_excerpt()): ?>
                    <p class="page__summary">
                        <?php echo esc_html(get_the_excerpt()); ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <div class="container p-5 page__content">
            <div class="col-md-8 offset-md-2">
                <?php the_content(); ?>
            </div>   
        </div>

    <?php
    endwhile; ?>

</div>

<?php get_footer(); ?>
