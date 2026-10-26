<?php
$url = $args['url'] ?? '';
$titre = $args['titre'] ?? '';

if (!$url) {
    return;
}
?>

<figure class="wp-block-audio">

    <?php if ($titre) : ?>
        <div class="audioCard__header">
            <i class="ri-headphone-line" aria-hidden="true"></i>
            <h3><?php echo esc_html($titre); ?></h3>
        </div>
    <?php endif; ?>

    <audio controls preload="metadata" src="<?php echo esc_url($url); ?>">
        Votre navigateur ne prend pas en charge la lecture audio.
    </audio>

</figure>