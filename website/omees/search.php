<?php get_header(); ?>
<div class="container py-5"><h1 class="mb-4"><?php printf(esc_html__('Resultats pour : %s', 'mon-theme'), esc_html(get_search_query())); ?></h1><?php if (have_posts()) : ?><div class="row g-4"><?php while (have_posts()) : the_post(); ?><div class="col-md-6 col-lg-4"><?php get_template_part('template-parts/content'); ?></div><?php endwhile; ?></div><?php the_posts_pagination(); else : get_template_part('template-parts/content', 'none'); endif; ?></div>
<?php get_footer(); ?>
