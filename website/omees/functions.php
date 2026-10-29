<?php
if (!defined('ABSPATH')) {
    exit();
}
require_once get_template_directory() . '/inc/setup.php';
require_once get_template_directory() . '/inc/enqueue.php';
require_once get_template_directory() . '/inc/theme-options.php';
require_once get_template_directory() . '/inc/home-options.php';
require_once get_template_directory() . '/inc/catalogues-options.php';

// Requetes et traitements des ressources.
require_once get_template_directory() . '/inc/resource-queries.php';
require_once get_template_directory() . '/inc/resource-ajax.php';
require_once get_template_directory() . '/inc/resource-admin.php';

// Transformations de blocs et selection des gabarits.
require_once get_template_directory() . '/inc/gutenberg.php';
require_once get_template_directory() . '/inc/templates.php';
