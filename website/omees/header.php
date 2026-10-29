<!doctype html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php wp_head(); ?>
</head>

<body <?php body_class('d-flex flex-column min-vh-100'); ?>>

    <?php wp_body_open(); ?>

    <a class="skip-link visually-hidden-focusable no-print" href="#omees-main-content">
        <?php esc_html_e('Aller au contenu principal', 'omees'); ?>
    </a>

    <?php if (!is_404()) : ?>
        <?php get_template_part('template-parts/components/navbar'); ?>
    <?php endif; ?>

    <main class="site-main flex-grow-1" id="omees-main-content" tabindex="-1">
