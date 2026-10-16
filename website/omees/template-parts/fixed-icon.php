<?php
$icon_url = get_template_directory_uri() . '/assets/img/badge_site.svg';
?>

<div class="fixedIcon" aria-hidden="true">
    <img
        src="<?php echo esc_url($icon_url); ?>"
        alt=""
        width="100"
        height="100"
    >
</div>

<style>
.fixedIcon {
    position: fixed;
    bottom: clamp(20px, 3vw, 50px);
    right: clamp(20px, 3vw, 50px);

    width: clamp(60px, 8vw, 120px);
    aspect-ratio: 1;

    z-index: 100;
    pointer-events: none;
}

.fixedIcon img {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: contain;
}
</style>