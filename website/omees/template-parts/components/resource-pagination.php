<?php
$total_pages = (int) ($args['total_pages'] ?? 1);
$current_page = (int) ($args['current_page'] ?? 1);

if ($total_pages <= 1) {
    return;
}
?>

<nav class="resourcePagination mt-5" aria-label="Pagination des ressources">

    <!-- Précédent -->
    <button
        type="button"
        class="resourcePagination__button resourcePagination__prev"
        data-page="<?php echo esc_attr($current_page - 1); ?>"
        <?php disabled($current_page <= 1); ?>
        aria-label="Page précédente"
    >
        <i class="ri-arrow-left-s-line"></i>
        Précédent
    </button>

    <!-- Numéros de pages -->
    <?php for ($i = 1; $i <= $total_pages; $i++): ?>

        <button
            type="button"
            class="resourcePagination__button <?php echo $i === $current_page ? 'active' : ''; ?>"
            data-page="<?php echo esc_attr($i); ?>"
            <?php if ($i === $current_page): ?>aria-current="page"<?php endif; ?>
        >
            <?php echo esc_html($i); ?>
        </button>

    <?php endfor; ?>

    <!-- Suivant -->
    <button
        type="button"
        class="resourcePagination__button resourcePagination__next"
        data-page="<?php echo esc_attr($current_page + 1); ?>"
        <?php disabled($current_page >= $total_pages); ?>
        aria-label="Page suivante"
    >
        Suivant
        <i class="ri-arrow-right-s-line"></i>
    </button>

</nav>