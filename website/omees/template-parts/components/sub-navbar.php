<?php
$breadcrumb = [];

if (is_singular('ressource')) {
    $breadcrumb[] = [
        'label' => 'Médiathèque',
        'url' => get_post_type_archive_link('ressource'),
    ];
} elseif (is_tax('thematique')) {
    $breadcrumb[] = [
        'label' => 'Thématiques',
        'url' => get_permalink(get_page_by_path('thematiques')),
    ];
}

if (is_post_type_archive('ressource')) {
    $title = 'Médiathèque';
} elseif (is_tax('thematique')) {
    $title = single_term_title('', false);
} else {
    $title = get_the_title(get_queried_object_id());
}

$breadcrumb[] = [
    'label' => $title,
    'url' => '',
];
?>

<nav class="subNavbar bg-white px-4 py-2" aria-label="Fil d'Ariane">
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between gap-3">

            <ol class="breadcrumb mb-0 align-items-center">

                <li class="breadcrumb-item">
                    <a href="<?php echo esc_url(home_url('/')); ?>">Accueil</a>
                </li>

                <?php foreach ($breadcrumb as $item): ?>
                    <li class="breadcrumb-item <?php echo $item['url'] ? '' : 'active'; ?>"
                        <?php echo $item['url'] ? '' : 'aria-current="page"'; ?>>

                        <?php if ($item['url']): ?>
                            <a href="<?php echo esc_url($item['url']); ?>">
                                <?php echo esc_html($item['label']); ?>
                            </a>
                        <?php else: ?>
                            <?php echo esc_html($item['label']); ?>
                        <?php endif; ?>

                    </li>
                <?php endforeach; ?>

            </ol>

            <?php get_template_part('template-parts/components/action-bar'); ?>

        </div>
    </div>
</nav>