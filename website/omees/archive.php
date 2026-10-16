<?php get_header(); ?>
<div class="container py-5"><h1 class="mb-4"><?php the_archive_title(); ?></h1><div class="row g-4"><?php while (have_posts()) : the_post(); ?><div class="col-md-6 col-lg-4"><?php get_template_part('template-parts/content'); ?></div><?php endwhile; ?></div><?php the_posts_pagination(); ?></div>
<?php get_footer(); ?>
