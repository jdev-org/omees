<?php
get_header();

$catalogues_options = get_option('omees_catalogues_options', []);

get_template_part('template-parts/components/resource-listing', null, [
    'fixed_type' => 'decryptage',

    'title' => !empty($catalogues_options['decryptages_title'])
        ? $catalogues_options['decryptages_title']
        : 'Décryptages',

    'description' => !empty($catalogues_options['decryptages_description'])
        ? $catalogues_options['decryptages_description']
        : 'Découvrez nos analyses pour mieux comprendre les enjeux environnementaux et sanitaires.',
]);

get_footer();