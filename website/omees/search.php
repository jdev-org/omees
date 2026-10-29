
<?php
get_header();

$search_query = get_search_query();
global $wp_query;
?>

<div class="searchPage">
    <div class="container py-5">

        <h1 class="mb-3">Résultats de recherche</h1>

        <div class="d-flex justify-content-between align-items-center">

            <p class="lead mb-4">
                Résultats pour :
                <strong>« <?php echo esc_html($search_query); ?> »</strong>
            </p>
            <p class="searchPage__count mb-4">
                <?php
                printf(
                    esc_html(
                        _n(
                            '%s résultat trouvé',
                            '%s résultats trouvés',
                            $wp_query->found_posts,
                            'omees'
                        )
                    ),
                    number_format_i18n($wp_query->found_posts)
                );
                ?>
            </p>
        </div>

        <div class="mb-4">
            <form
                    role="search"
                    method="get"
                    action="<?php echo esc_url(home_url('/')); ?>"
                    class="error404Page__searchForm"
                >
                    <label class="visually-hidden" for="error404-search">
                        Rechercher sur le site
                    </label>

                    <input
                        id="error404-search"
                        type="search"
                        name="s"
                        placeholder="Rechercher un contenu..."
                    >

                    <button type="submit" aria-label="Lancer la recherche">
                        <i class="ri-search-line" aria-hidden="true"></i>
                    </button>
                </form>
    </div>
        
<?php
$matching_thematiques = [];

if ($search_query !== '') {

    $matching_thematiques = get_terms([
        'taxonomy'   => 'thematique',
        'hide_empty' => false,
        'search'     => $search_query,
    ]);

    if (is_wp_error($matching_thematiques)) {
        $matching_thematiques = [];
    }
}
?>

<?php if (!empty($matching_thematiques)) : ?>

    <section class="searchPage__themes my-4">

        <h2 class="h5 mb-3">Thématiques associées</h2>

        <div class="d-flex flex-wrap gap-2">

            <?php foreach ($matching_thematiques as $thematique) : ?>

                <a
                    href="<?php echo esc_url(
                        get_term_link($thematique)
                    ); ?>"
                    class="searchPage__theme"
                >
                    <i class="ri-price-tag-3-line" aria-hidden="true"></i>

                    <?php echo esc_html($thematique->name); ?>

                    <i class="ri-arrow-right-up-line" aria-hidden="true"></i>
                </a>

            <?php endforeach; ?>

        </div>

    </section>

<?php endif; ?>


        

        <?php if (have_posts()) : ?>

            <div class="row g-4">

                <?php while (have_posts()) : the_post(); ?>

                    <div class="col-12 col-xl-6">
                        <?php
                        get_template_part(
                            'template-parts/components/search-result-card'
                        );
                        ?>
                    </div>

                <?php endwhile; ?>

            </div>

            <div class="searchPage__pagination mt-5">
                <?php
                the_posts_pagination([
                    'mid_size'  => 2,
                    'prev_text' => '← Précédent',
                    'next_text' => 'Suivant →',
                ]);
                ?>
            </div>

        <?php else : ?>

            <?php
            get_template_part('template-parts/components/resource-empty', null, [
                'title' => 'Aucun résultat pour cette recherche',
                'message' => "Essayez d'utiliser d'autres mots-clés ou de simplifier votre recherche.",
                'heading_tag' => 'h2',
                'heading_class' => 'h5',
                'classes' => 'text-center my-5',
            ]);
            ?>

        <?php endif; ?>

    </div>
</div>

<?php get_footer(); ?>
