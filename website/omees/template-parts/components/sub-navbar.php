
<?php
$breadcrumb = [];

$show_actions = false;
$show_share = true;

// Pages de référence
$cartotheque_page = get_page_by_path('cartotheque');
$decryptages_page = get_page_by_path('decryptages');
$thematiques_page = get_page_by_path('thematiques');

// Ajouter un élément au fil d'Ariane
$add_breadcrumb = function ($label, $url = '') use (&$breadcrumb) {
    $breadcrumb[] = [
        'label' => $label,
        'url'   => $url,
    ];
};

/*
 * RESSOURCE INDIVIDUELLE
 */
if (is_singular('ressource')) {

    $show_actions = true;

    if (has_term('decryptage', 'type-de-contenu')) {

        $add_breadcrumb(
            'Décryptages',
            $decryptages_page ? get_permalink($decryptages_page) : ''
        );

    } elseif (has_term('carte', 'type-de-contenu')) {

        $add_breadcrumb(
            'Cartothèque',
            $cartotheque_page ? get_permalink($cartotheque_page) : ''
        );

    } else {

        $add_breadcrumb(
            'Médiathèque',
            get_post_type_archive_link('ressource')
        );
    }

    $add_breadcrumb(get_the_title(get_queried_object_id()));

/*
 * ARCHIVE MÉDIATHÈQUE
 */
} elseif (is_post_type_archive('ressource')) {

    $add_breadcrumb('Médiathèque');

/*
 * TAXONOMIE THÉMATIQUE
 */
} elseif (is_tax('thematique')) {

    $add_breadcrumb(
        'Thématiques',
        $thematiques_page ? get_permalink($thematiques_page) : ''
    );

    $add_breadcrumb(single_term_title('', false));

    // Selon ton tableau : pas de boutons sur ce gabarit
    $show_share = false;

/*
 * RECHERCHE
 */
} elseif (is_search()) {

    $add_breadcrumb('Résultats de recherche');

/*
 * PAGES WORDPRESS
 */
} elseif (is_page()) {

    $page_id = get_queried_object_id();

    if (is_page('cartotheque')) {

        $add_breadcrumb('Cartothèque');

    } elseif (is_page('decryptages')) {

        $add_breadcrumb('Décryptages');

    } elseif (is_page('mediatheque')) {

        $add_breadcrumb('Médiathèque');

    } else {

        $add_breadcrumb(get_the_title($page_id));

        // Boutons d'action pour les pages classiques
        $show_actions = true;
    }

/*
 * ARTICLES WORDPRESS CLASSIQUES
 */
} elseif (is_single()) {

    $show_actions = true;

    $add_breadcrumb(
        'Médiathèque',
        get_post_type_archive_link('ressource')
    );

    $add_breadcrumb(get_the_title(get_queried_object_id()));

} else {

    $show_share = false;
}

// Ne pas afficher la sub-navbar sur l'accueil
$show_subnavbar = !is_front_page();
?>

<?php if ($show_subnavbar) : ?>

    <nav
        class="subNavbar bg-white px-4 py-2"
        aria-label="Fil d'Ariane"
    >
        <div class="container-fluid">

            <div class="d-flex align-items-center justify-content-between gap-3">

                <ol class="breadcrumb mb-0 align-items-center">

                    <li class="breadcrumb-item">
                        <a href="<?php echo esc_url(home_url('/')); ?>">
                            Accueil
                        </a>
                    </li>

                    <?php foreach ($breadcrumb as $item) : ?>

                        <li
                            class="breadcrumb-item <?php echo $item['url'] ? '' : 'active'; ?>"
                            <?php if (!$item['url']) : ?>
                                aria-current="page"
                            <?php endif; ?>
                        >

                            <?php if ($item['url']) : ?>

                                <a href="<?php echo esc_url($item['url']); ?>">
                                    <?php echo esc_html($item['label']); ?>
                                </a>

                            <?php else : ?>

                                <?php echo esc_html($item['label']); ?>

                            <?php endif; ?>

                        </li>

                    <?php endforeach; ?>

                </ol>

                <?php if ($show_actions || $show_share) : ?>

                    <?php
                    get_template_part(
                        'template-parts/components/action-bar',
                        null,
                        [
                            'show_actions' => $show_actions,
                            'show_share'   => $show_share,
                        ]
                    );
                    ?>

                <?php endif; ?>

            </div>

        </div>
    </nav>

<?php endif; ?>
