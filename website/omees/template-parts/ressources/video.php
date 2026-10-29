<?php
$options = get_field("options_de_la_video");

$options = is_array($options) ? $options : [];

$source = $options["source_de_la_video"] ?? "";
$video_externe = $options["url_de_la_video"] ?? "";
$fichier = $options["fichier_video"] ?? null;

// Initialiser l'URL de la vidéo locale
$video_url = "";

if (is_array($fichier)) {
    $video_url = $fichier["url"] ?? "";
} elseif (is_numeric($fichier)) {
    $video_url = wp_get_attachment_url($fichier);
} elseif (is_string($fichier)) {
    $video_url = $fichier;
}
?>

<div class="col-md-8 offset-md-2">
    <?php if ($source === "video_url" && $video_externe): ?>

        <!-- Vidéo externe : YouTube, Vimeo... -->
        <div class="ratio ratio-16x9 rounded-4 overflow-hidden">
            <?php echo wp_kses($video_externe, [
                "iframe" => [
                    "src" => true,
                    "width" => true,
                    "height" => true,
                    "title" => true,
                    "frameborder" => true,
                    "allow" => true,
                    "allowfullscreen" => true,
                    "loading" => true,
                    "referrerpolicy" => true,
                ],
            ]); ?>
        </div>

     <?php elseif ($source === "video_local" && $video_url): ?>
        <!-- Vidéo téléversée dans la médiathèque -->
        <div class="ratio ratio-16x9 rounded-4 overflow-hidden">
            <video controls preload="metadata" class="w-100 h-100">
                <source
                    src="<?php echo esc_url($video_url); ?>"
                    type="video/mp4"
                >
                Votre navigateur ne prend pas en charge cette vidéo.
            </video>
        </div>


    
    <?php endif; ?>
    <?php if (trim(get_the_content()) !== '') : ?>

        <h3 class="mt-5 mb-3">À propos de cette ressource</h3>

        <?php the_content(); ?>

    <?php endif; ?>

    <?php
        get_template_part('template-parts/components/keywords');
    ?>
    </div>                
