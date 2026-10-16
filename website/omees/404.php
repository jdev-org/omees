<?php get_header(); ?>
<div class="container py-5 text-center"><h1>404</h1><p><?php esc_html_e('Cette page est introuvable.', 'mon-theme'); ?></p><a class="btn btn-primary" href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Retour a l accueil', 'mon-theme'); ?></a></div>
<?php get_footer(); ?>
