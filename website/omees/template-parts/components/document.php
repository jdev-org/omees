<?php
$url = $args['url'] ?? '';
$titre = $args['titre'] ?? '';
$label = $args['label'] ?? 'Télécharger le fichier';


if (!$url) {
    return;
}
?>

<a
        href="<?php echo esc_url($url); ?>"
        class="externalCard my-2"
        target="_blank"
        rel="noopener noreferrer"
    >
        <div class="externalCard__content">

            <div class="externalCard__text">
                <h3>
                     <?php echo esc_html($label); ?>
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