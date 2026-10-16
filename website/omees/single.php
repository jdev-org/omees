<?php get_header(); ?>


    <?php while (have_posts()):
        the_post(); ?>
    

    <article class="single" <?php post_class(); ?>>
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
                <div class="col-md-8 offset-md-2">
                    <?php if (has_post_thumbnail()): ?>

                        <?php if (has_post_thumbnail()): ?>

                        <?php
                        // Récupérer l'ID de l'image mise en avant
                        $image_id = get_post_thumbnail_id();

                        // Récupérer sa légende
                        $legende = wp_get_attachment_caption($image_id);
                        ?>

                        <figure class="single__thumbnail mb-4">

                            <?php the_post_thumbnail("large", [
                                "class" => "img-fluid media__rounded",
                                "loading" => "eager",
                            ]); ?>

                            <?php if ($legende): ?>
                                <figcaption class="my-2">
                                    <?php echo esc_html($legende); ?>
                                </figcaption>
                            <?php endif; ?>

                        </figure>

                    <?php endif; ?>

                    <?php endif; ?>
                    
            
            <?php the_content(); ?>

            <hr class="mt-5">
            <h6>Les mots clés associés à cette ressource</h6>
            <?php
            $etiquettes = get_the_terms(get_the_ID(), "post_tag");

            if ($etiquettes && !is_wp_error($etiquettes)): ?>

                <div class="mt-3">

                    <?php foreach ($etiquettes as $etiquette): ?>
                        <a class="singleBadge singleBadge__keyWord" href="<?php echo esc_url(
                            get_term_link($etiquette)
                        ); ?>">
                            <?php echo esc_html($etiquette->name); ?>
                        </a>
                    <?php endforeach; ?>

                </div>

            <?php endif;
                        ?>
            </div>
                
            </div>
        </div>
    </article>

    <?php
    endwhile; ?>


<?php get_footer(); ?>
