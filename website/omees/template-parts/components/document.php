<?php
$url = $args['url'] ?? '';
$titre = $args['titre'] ?? '';

if (!$url) {
    return;
}
?>

<a
        href="<?php echo esc_url($url); ?>"
        class="externalCard mb-5"
        target="_blank"
        rel="noopener noreferrer"
    >
        <div class="externalCard__content">

            <div class="externalCard__text">
                <h3>
                     Télécharger le fichier
                </h3>

                <p><?php if ($titre) : ?>
            <span><?php echo esc_html($titre); ?></span>
        <?php endif; ?></p>
            </div>

            <span class="externalCard__icon" aria-hidden="true">
                <i class="ri-arrow-right-up-line"></i>
            </span>

        </div>

    </a>