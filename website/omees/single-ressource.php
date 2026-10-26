<?php get_header(); ?>


    <?php while (have_posts()):
        the_post(); ?>
    

    <article class="single omees__contentMain" <?php post_class(); ?>>
        <div class="single__header">
            <div class="container p-5">            
                <h1 class="mb-4"><?php the_title(); ?></h1>

                <p class="my-4">
                    <?php echo esc_html(get_the_author()); ?>
                    —
                    Publié le <?php echo esc_html(get_the_date("d/m/Y")); ?>
                    | Mis à jour le <?php echo esc_html(
                        get_the_modified_date("d/m/Y")
                    ); ?>
                </p>

                <div>
                    <?php
                    // Type de contenu
                    $types = get_the_terms(get_the_ID(), "type-de-contenu");

                    if ($types && !is_wp_error($types)) {
                        foreach ($types as $type) {
                            echo '<span class="singleBadge singleBadge__type">' .
                                esc_html($type->name) .
                                "</span> ";
                        }
                    }

                    // Thématiques
                    $thematiques = get_the_terms(get_the_ID(), "thematique");

                    if ($thematiques && !is_wp_error($thematiques)) {
                        foreach ($thematiques as $thematique) {
                            echo '<a href="' .
                                esc_url(get_term_link($thematique)) .
                                '" class="singleBadge singleBadge__theme">' .
                                esc_html($thematique->name) .
                                "</a> ";
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="single__content">
            <div class="container p-5">
                <?php
                // Article par défaut
                $template = 'article';

                // Récupérer le type de contenu
                $types = get_the_terms(get_the_ID(), 'type-de-contenu');

                if ($types && !is_wp_error($types)) {
                    $type = reset($types);

                    // Vérifier que le template existe
                    if (locate_template('template-parts/ressources/' . $type->slug . '.php')) {
                        $template = $type->slug;
                    }
                }

                // Charger le template
                get_template_part('template-parts/ressources/' . $template);
                ?>
                
            </div>
        </div>
    </article>

    <?php
    endwhile; ?>


<?php get_footer(); ?>
