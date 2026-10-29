<?php
if (!defined('ABSPATH')) {
    exit;
}

/* Document block custom */

add_filter('render_block_core/file', function ($block_content, $block) {

    if (is_admin()) {
        return $block_content;
    }

    $url = $block['attrs']['href'] ?? '';
    $titre = '';
    $label = 'Télécharger le fichier';

    if (!empty($block['innerHTML'])) {
        $dom = new DOMDocument();

        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $block['innerHTML']);
        libxml_clear_errors();

        foreach ($dom->getElementsByTagName('a') as $lien) {

            $classes = $lien->getAttribute('class');

            if (str_contains($classes, 'wp-block-file__button')) {
                // Texte du bouton Gutenberg
                $texte = trim($lien->textContent);

                if ($texte !== '') {
                    $label = $texte;
                }
            } else {
                // Nom du fichier
                $titre = trim($lien->textContent);
            }
        }
    }

    if (!$url) {
        return $block_content;
    }

    ob_start();

    get_template_part('template-parts/components/document', null, [
        'url'   => $url,
        'titre' => $titre,
        'label' => $label,
    ]);

    return ob_get_clean();

}, 10, 2);

/* Lien externe — Bloc Bouton Gutenberg */

add_filter('render_block_core/button', function ($block_content, $block) {

    // Conserver le bouton normal dans l'éditeur
    if (is_admin()) {
        return $block_content;
    }

    // Ne transformer que les boutons avec cette classe
    $classes = $block['attrs']['className'] ?? '';

    if (!in_array('is-style-external-card', preg_split('/\s+/', $classes), true)) {
        return $block_content;
    }

    $url = '';
    $label = '';

    if (!empty($block_content)) {

        $dom = new DOMDocument();

        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $block_content);
        libxml_clear_errors();

        foreach ($dom->getElementsByTagName('a') as $lien) {
            $url = $lien->getAttribute('href');
            $label = trim($lien->textContent);
            break;
        }
    }

    if (!$url) {
        return $block_content;
    }

    ob_start();

    get_template_part('template-parts/components/external-card', null, [
        'url'   => $url,
        'label' => $label,
    ]);

    return ob_get_clean();

}, 10, 2);

add_action('init', function () {
    register_block_style('core/button', [
        'name'  => 'external-card',
        'label' => 'Lien externe',
    ]);
});
