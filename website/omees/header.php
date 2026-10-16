<!doctype html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php wp_head(); ?>
</head>

<body class="d-flex flex-column min-vh-100" <?php body_class(); ?>>

    <?php wp_body_open(); ?>

    <header class="site-header fixed-top " id="omees__header">

        <nav class="navbar navbar-expand-lg p-0 bg-white scrollNavbar" id="omees__navHeader">
    <div class="container-fluid justify-content-center">

        <div class="row w-100 align-items-center">

            <!-- LOGO -->
            <div class="col col-lg-3">
                <a class="navbar-brand" href="<?php echo esc_url(home_url('/')); ?>">
                    <?php $logo = wp_get_attachment_url(get_option('omees_theme_options', [])['nav_logo'] ?? 0); ?>
                    <img
                        src="<?= esc_url($logo) ?>"
                        alt="Logo OMEES"
                        height="35"
                    />
                </a>
            </div>


            <!-- MENU -->
            <div class="col-12 col-lg-6 order-3 order-lg-2">

                <div
                    class="collapse navbar-collapse justify-content-center"
                    id="mainNavbar"
                >
                    <?php wp_nav_menu([
                        'theme_location' => 'primary',
                        'container' => false,
                        'menu_class' => 'navbar-nav',
                        'fallback_cb' => false,
                    ]); ?>
                </div>

            </div>


            <!-- ACTIONS -->
            <div class="col col-lg-3 order-2 order-lg-3">

                <div class="d-flex align-items-center justify-content-end gap-2">

                    <!-- Recherche -->
                    <button
                        class="btn search-toggle hover-zoom"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#searchContainer"
                        aria-expanded="false"
                        aria-controls="searchContainer"
                        aria-label="<?php esc_attr_e('Ouvrir la recherche', 'mon-theme'); ?>"
                    >
                        <i
                            class="ri-search-line fs-3"
                            aria-hidden="true"
                        ></i>
                    </button>


                    <!-- Menu mobile -->
                    <button
                        class="navbar-toggler d-lg-none border-0 collapsed"                        
                        id="omees__btnNavMobileColl"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#mainNavbar"
                        aria-controls="mainNavbar"
                        aria-expanded="false"
                        aria-label="<?php esc_attr_e('Afficher le menu', 'mon-theme'); ?>"
                    >
                        <span class="navbar-toggler-icon"></span>
                    </button>

                </div>

            </div>

        </div>
    </div>    
</nav>
<?php if (is_singular('ressource') || is_single() || is_tax('thematique') || is_page()) {
    get_template_part('template-parts/components/sub-navbar');
} ?>
<!-- RECHERCHE -->
        <div class="collapse w-100" id="searchContainer">

            <div class="search-container">

                <?php get_search_form(); ?>

                <button
                    type="button"
                    class="search-close hover-zoom"
                    data-bs-toggle="collapse"
                    data-bs-target="#searchContainer"
                    aria-controls="searchContainer"
                    aria-label="<?php esc_attr_e('Fermer la recherche', 'mon-theme'); ?>"
                >
                    <i
                        class="ri-close-line fs-2"
                        aria-hidden="true"
                    ></i>
                </button>

            </div>

        </div>

    </header>

    <main class="site-main flex-grow-1">