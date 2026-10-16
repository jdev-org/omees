<?php
$options = get_field('options_de_la_publication');
$options = is_array($options) ? $options : [];

// Champs ACF
$source = $options['document_de_la_publication'] ?? '';
$fichier = $options['selectionner_un_document_pdf'] ?? null;
$url_externe = $options['url_du_document'] ?? '';
$couverture = $options['image_de_couverture_de_la_publication'] ?? null;

// URL du PDF
$pdf_url = '';

// Adapter les valeurs selon les choix du bouton radio ACF
if ($source === 'local_fichier') {
    if (is_array($fichier)) {
        $pdf_url = $fichier['url'] ?? '';
    } elseif (is_numeric($fichier)) {
        $pdf_url = wp_get_attachment_url($fichier);
    } elseif (is_string($fichier)) {
        $pdf_url = $fichier;
    }
} elseif ($source === 'url_fichier') {
    $pdf_url = $url_externe;
}

// Image de couverture
$image_url = get_template_directory_uri() . '/assets/img/default_doc.png';
$image_alt = get_the_title();

if (is_array($couverture) && !empty($couverture['url'])) {

    // 1. Image de couverture ACF
    $image_url = $couverture['url'];
    $image_alt = !empty($couverture['alt']) ? $couverture['alt'] : $image_alt;

} elseif (is_numeric($couverture) && wp_get_attachment_image_url($couverture, 'large')) {

    // 1. Image de couverture ACF (ID)
    $image_url = wp_get_attachment_image_url($couverture, 'large');

} elseif (is_string($couverture) && !empty($couverture)) {

    // 1. Image de couverture ACF (URL)
    $image_url = $couverture;

} elseif (has_post_thumbnail()) {

    // 2. Sinon, image mise en avant WordPress
    $image_url = get_the_post_thumbnail_url(get_the_ID(), 'large') ?: $image_url;

    $thumbnail_alt = get_post_meta(
        get_post_thumbnail_id(),
        '_wp_attachment_image_alt',
        true
    );

    if ($thumbnail_alt) {
        $image_alt = $thumbnail_alt;
    }
}

// 3. Sinon, l'image par défaut reste utilisée
?>

<div class="row">
<div class="col-md-3">
    <div class="publicationCard sticky-top mb-4">

        <div class="publicationCard__cover">
            <img
                src="<?php echo esc_url($image_url); ?>"
                alt="<?php echo esc_attr($image_alt); ?>"
                class="img-fluid"
            >
        </div>

        <div class="publicationCard__content">

            <h6><?php the_title(); ?></h6>

            <?php if ($pdf_url) : ?>

                <div class="publicationCard__actions">

                    <a
                        href="<?php echo esc_url($pdf_url); ?>"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="btn btn-info"
                    >
                        <i class="ri-external-link-line"></i>
                        Consulter le PDF
                    </a>
                </div>

            <?php endif; ?>

        </div>

    </div>
</div>
<div class="col-md-8">                   
            
                    <?php the_content(); ?>

                    <?php get_template_part(
                        "template-parts/components/keywords"
                    ); ?>
                </div>                
</div>