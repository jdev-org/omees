<?php
$url = $args['url'] ?? '';
$label = $args['label'] ?? 'Consulter le lien';

if (!$url) {
    return;
}
?>

<a
    href="<?php echo esc_url($url); ?>"
    class="externalCard my-2 w-100"
    target="_blank"
    rel="noopener noreferrer"
>
    <div class="externalCard__content">

        <div class="externalCard__text">
            <h3><?php echo esc_html($label); ?></h3>

            <p class="externalCard__url">
                <?php echo esc_html($url); ?>
            </p>
            <span class="externalCard__printUrl">
                <?php echo esc_html(
                    preg_replace('#^https?://#', '', $url)
                ); ?>
            </span>
        </div>

        <span class="externalCard__icon" aria-hidden="true">
            <i class="ri-arrow-right-up-line"></i>
        </span>

    </div>
</a>