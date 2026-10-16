<article <?php post_class('card h-100'); ?>>
<?php if (has_post_thumbnail()) : ?><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('large', ['class'=>'card-img-top']); ?></a><?php endif; ?>
<div class="card-body">
<h2 class="card-title h4"><a href="<?php the_permalink(); ?>" class="text-decoration-none"><?php the_title(); ?></a></h2>
<div class="card-text"><?php the_excerpt(); ?></div>
</div>
</article>
