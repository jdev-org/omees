<?php

$home_options = get_option('omees_home_options', []);

/**
 * HERO
 */
$hero_title = $home_options['hero_title'] ?? '';

$hero_image_id = $home_options['hero_image'] ?? 0;
$hero_image = $hero_image_id
    ? wp_get_attachment_image_url($hero_image_id, 'full')
    : '';

/**
 * PRÉSENTATION DE L'OMEES
 */
$presentation_title = $home_options['presentation_title'] ?? '';
$presentation_text = $home_options['presentation_text'] ?? '';

$presentation_button_label = $home_options['presentation_button_label'] ?? '';

$presentation_button_url = omees_home_get_link(
    $home_options['presentation_button_url'] ?? []
);

/**
 * CARTE
 */
$presentation_map_title = $home_options['presentation_map_title'] ?? '';

$presentation_map_url = omees_home_get_link(
    $home_options['presentation_map_url'] ?? []
);

$presentation_map_image_id = $home_options['presentation_map_image'] ?? 0;

$presentation_map_image = $presentation_map_image_id
    ? wp_get_attachment_image_url($presentation_map_image_id, 'full')
    : '';

/**
 * THÉMATIQUES
 */
$themes_title = $home_options['themes_title'] ?? '';

/**
 * DÉCRYPTAGES
 */
$decryptages_title = $home_options['decryptages_title'] ?? '';


/**
 * OUTILS
 */
$tools_title = $home_options['tools_title'] ?? '';

$tools = $home_options['tools'] ?? [];

/**
 * RESSOURCES
 */
$resources_title = $home_options['resources_title'] ?? '';

?>

<?php

get_header(); ?>

<?php get_template_part('template-parts/fixed-icon'); ?>

<main class="site-main" id="omees__home">

    <!-- HERO -->
    
    <section id="homeHero" <?php if ($hero_image) : ?>
        style="background-image: url('<?php echo esc_url($hero_image); ?>');"
    <?php endif; ?>
>
        <div class="container-xxl">
            <div class="home-hero-content text-center">
                <h1 class="hero-title pageTitle fade-in">
                    <?php echo wp_kses_post($hero_title); ?>
                </h1>
                <a href="#homePresentation" class="home-scroll">
                    <i class="ri-arrow-down-line"></i>
                </a>
            </div>

        </div>
        
    </section>

    <!-- PRÉSENTATION -->
    <section class="home-presentation my-4 py-5" id="homePresentation">

        <div class="container-xxl">

            <div class="row align-items-center">

                <div class="offset-md-1 col-md-5">

                    <h2 class="mb-4">
                        <?php echo wp_kses_post($presentation_title ); ?>
                    </h2>
                    <div>
                        <?php echo wp_kses_post($presentation_text ); ?>
                    </div>
                    <a href="<?php echo esc_url($presentation_button_url); ?>" class="btn btn-secondary mt-4">
                        <?php echo wp_kses_post($presentation_button_label); ?>
                    </a>

                </div>


                <div class="col-md-5 py-4">

                    <a
                        href="<?php echo esc_url($presentation_map_url); ?>"
                        class="homeMapCard"
                        style="background-image: url('<?php echo esc_url($presentation_map_image); ?>');"
                    >
                    <div class="d-flex justify-content-end">    
                        <span class="homeMapCard__arrow" aria-hidden="true">
                            <i class="ri-arrow-right-up-line"></i>
                        </span>
                    </div>    
                        <h3 class="homeMapCard__title">
                            <?php echo wp_kses_post($presentation_map_title); ?>
                        </h3>

                    </a>

                </div>

            </div>

        </div>

    </section>


    <!-- THÉMATIQUES -->
    <section class="home-themes my-4 py-5 bg-info text-white" id="homeThemes">
        <div class="container-xxl ">
            <h2 class="mb-5 text-center"> <?php echo wp_kses_post($themes_title); ?></h2>
            <div class="homeTheme__grid my-5">
                <?php
                $terms = get_terms([
                    'taxonomy' => 'thematique',
                    'hide_empty' => false,
                ]);

                if (!is_wp_error($terms) && !empty($terms)):
                    foreach ($terms as $term):

                        // Récupération de l'ID du SVG depuis ACF
                        $icone_id = get_field('icone_de_la_thematique', 'term_' . $term->term_id);

                        // Récupération de l'URL du fichier
                        $icone_url = $icone_id ? wp_get_attachment_url($icone_id) : '';

                        $term_link = get_term_link($term);

                        if (is_wp_error($term_link)) {
                            continue;
                        }
                        ?>

                        <a class="homeTheme__card hover-zoom"
                        href="<?php echo esc_url($term_link); ?>">

                            <span class="homeTheme__title">
                                <?php echo esc_html($term->name); ?>
                            </span>

                            <?php if ($icone_url): ?>
                            <span
                                class="homeTheme__icon"
                                style="--icon-url: url('<?php echo esc_url($icone_url); ?>');"
                                aria-hidden="true"
                            ></span>
                        <?php endif; ?>
                        </a>
                    <?php
                    endforeach;
                endif;
                ?>

            </div>
        </div>    
    </section>


    <!-- NOS DÉCRYPTAGES -->
    <section class="home-decryptages py-5">
        <div class="container-xxl ">
                <h2 class="mb-5"><?php echo wp_kses_post($decryptages_title); ?></h2>
                <?php get_template_part('template-parts/home/decryptages'); ?>
        </div>
    </section>


    <!-- OUTILS -->
    <section class="home-tools bg-secondary text-white my-4 py-5">
         <div class="container-xxl mb-4">
            <h2 class="mb-5"><?php echo wp_kses_post($tools_title); ?></h2>
        <div class="row g-4">

            <?php foreach ($tools as $tool): ?>
                <?php
                $tool_title = $tool['title'] ?? '';
                $tool_text = $tool['text'] ?? '';
                $tool_url = omees_home_get_link($tool);
                ?>

                <div class="col-12 col-md-6">

                    <a
                        href="<?php echo esc_url($tool_url); ?>"
                        class="discoverTools__card"
                    >

                        <div class="discoverTools__content">

                            <h3 class="discoverTools__cardTitle">
                                <?php echo esc_html($tool_title); ?>
                            </h3>

                            <p class="discoverTools__description">
                                <?php echo esc_html($tool_text); ?>
                            </p>

                        </div>

                        <span class="discoverTools__arrow" aria-hidden="true">
                            <i class="ri-arrow-right-up-line"></i>
                        </span>

                    </a>

                </div>

            <?php endforeach; ?>

        </div>
        </div>

    </section>


    <!-- DERNIÈRES RESSOURCES -->
    <section class="home-resources my-4 py-5">
        <div class="container-xxl ">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                <h2 class=""><?php echo wp_kses_post($resources_title); ?></h2>
                <a
                            class="homeDecryptages__all m-0"
                            href="<?php echo esc_url(home_url('/mediatheque/')); ?>"
                        >
                            Voir toutes les ressources
                            <i class="ri-arrow-right-up-line" aria-hidden="true"></i>
                        </a>
            </div>
        <?php $dernieres_ressources = new WP_Query([
            'post_type' => 'ressource',
            'post_status' => 'publish',
            'posts_per_page' => 4,
            'orderby' => 'date',
            'order' => 'DESC',
            'ignore_sticky_posts' => true,
            'no_found_rows' => true,
        ]); ?>

        <?php if ($dernieres_ressources->have_posts()): ?>

            <div class="row g-4">

                <?php while ($dernieres_ressources->have_posts()):
                    $dernieres_ressources->the_post(); ?>

                    <div class="col-12 col-md-6 col-lg-3">

                        <?php get_template_part('template-parts/components/resource-card'); ?>

                    </div>

                <?php
                endwhile; ?>

            </div>

        <?php else: ?>

            <p>Aucune ressource disponible pour le moment.</p>

        <?php endif; ?>

        <?php wp_reset_postdata(); ?>
        </div>

    </section>

</main>

<?php get_footer();
?>
