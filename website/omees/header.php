<!doctype html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php wp_head(); ?>
</head>

<body class="d-flex flex-column min-vh-100" <?php body_class(); ?>>

    <?php wp_body_open(); ?>

    <?php if (!is_404()) : ?>
        <?php get_template_part('template-parts/components/navbar'); ?>
    <?php endif; ?>

    <main class="site-main flex-grow-1">