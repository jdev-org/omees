<?php get_header(); ?>
<div class="container py-5">
<?php if (have_posts()) : ?>
<div class="row g-4">
<?php while (have_posts()) : the_post(); ?>
<div class="col-md-6 col-lg-4"><?php get_template_part('template-parts/content'); ?></div>
<?php endwhile; ?>
</div>
<div class="mt-4"><?php the_posts_pagination(); ?></div>
<?php else : get_template_part('template-parts/content', 'none'); endif; ?>
</div>
<?php get_footer(); ?>
