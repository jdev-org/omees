<?php

get_header(); ?>

<?php get_template_part('template-parts/fixed-icon'); ?>

<main class="site-main" id="omees__home">

    <!-- HERO -->
    <section id="homeHero" style="height: 92vh;">
        <div class="container-xxl">
            <div class="home-hero-content text-center">
                <h1 class="hero-title pageTitle fade-in">
                    L'Observatoire Montpelliérain<br>
                    en <span class="highlight highlight-yellow">Écologie</span>
                    et Évolution de la
                    <span class="highlight highlight-green">Santé</span>
                </h1>
                <a href="#presentation" class="home-scroll">
                    <i class="ri-arrow-down-line"></i>
                </a>
            </div>

        </div>
        <img
      src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/img/OMEES_illustrationCity.svg"
      class=""
      data-parallax-speed="0.5"
      alt=""
    >
    </section>

    <!-- PRÉSENTATION -->
    <section class="home-presentation my-5 py-5" id="presentation">

        <div class="container-xxl">

            <div class="row align-items-center g-5 ">

                <div class="col-md-6">

                    <h2 class="mb-4">
                        L'écologie de la santé,<br>
                        une nouvelle lecture<br>
                        du territoire
                    </h2>

                    <p>
                        L'Observatoire réunit des acteurs scientifiques
                        et territoriaux pour mieux comprendre les liens
                        entre environnement, biodiversité et santé.
                    </p>

                    <p>
                        Nous partageons des données, des analyses et des outils
                        pour aider les territoires à prévenir les risques
                        et à guider les décisions.
                    </p>

                    <a href="#" class="btn btn-secondary mt-4">
                        NOUS CONNAÎTRE
                    </a>

                </div>


                <div class="col-md-6">

                    <a href="#" class="home-map-card">

                        <div class="home-map-image">
                            <!-- image / carte -->
                        </div>

                        <div class="home-map-title">
                            Découvrir les projets<br>
                            et les acteurs de l'OMEES
                        </div>

                    </a>

                </div>

            </div>

        </div>

    </section>


    <!-- THÉMATIQUES -->
    <section class="home-themes my-5 py-5 bg-info text-white">
        <div class="container-xxl ">
            <h2 class="mb-5 text-center"> Explorer nos thématiques</h2>
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
            
        <h2 class="mb-5"> Nos décryptages</h2>
</div>


    </section>


    <!-- OUTILS -->
    <section class="home-tools bg-secondary text-white py-5">
         <div class="container-xxl mb-4">
            <h2 class="mb-5"> Découvrir nos outils</h2>
            <?php
            $catalogue_url = get_post_type_archive_link('ressource');

            $tools = [
                [
                    'title' => 'Catalogue de données',
                    'description' => 'Les données en accès libre pour comprendre les territoires et leurs enjeux',
                    'url' => '#', // À remplacer par la bonne URL
                ],
                [
                    'title' => 'Décryptages',
                    'description' => 'Des résumés clairs pour comprendre chaque enjeu',
                    'url' => add_query_arg('types', 'decryptage', $catalogue_url),
                ],
                [
                    'title' => 'Cartothèque',
                    'description' => 'Des cartes pour voir autrement et pour mieux décider.',
                    'url' => add_query_arg('types', 'carte', $catalogue_url),
                ],
                [
                    'title' => 'Médiathèque',
                    'description' => 'Toutes nos ressources au même endroit',
                    'url' => $catalogue_url,
                ],
            ];
            ?>
<div class="row g-4">

            <?php foreach ($tools as $tool): ?>

                <div class="col-12 col-md-6">

                    <a
                        href="<?php echo esc_url($tool['url']); ?>"
                        class="discoverTools__card"
                    >

                        <div class="discoverTools__content">

                            <h3 class="discoverTools__cardTitle">
                                <?php echo esc_html($tool['title']); ?>
                            </h3>

                            <p class="discoverTools__description">
                                <?php echo esc_html($tool['description']); ?>
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
    <section class="home-resources py-5">
        <div class="container-xxl ">
        <h2 class="mb-5"> Nos dernières ressources</h2>
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

                        <?php get_template_part('template-parts/components/ressource-card'); ?>

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
