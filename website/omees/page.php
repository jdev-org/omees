<?php get_header(); ?>

<div class="page">

    <?php while (have_posts()):
        the_post(); ?>

        <div class="container py-5">

            <h1 class="mb-5">
                <?php the_title(); ?>
            </h1>

            <div class="page__content">
                <?php the_content(); ?>
            </div>

        </div>

    <?php
    endwhile; ?>

</div>

<?php get_footer(); ?>
