<?php
get_header();

$catalogues_options = get_option('omees_catalogues_options', []);

get_template_part('template-parts/components/resource-listing', null, [
    'fixed_type' => 'carte',

    'title' => !empty($catalogues_options['cartotheque_title'])
        ? $catalogues_options['cartotheque_title']
        : 'Cartothèque',

    'description' => !empty($catalogues_options['cartotheque_description'])
        ? $catalogues_options['cartotheque_description']
        : 'Explorez nos cartes pour mieux comprendre les enjeux environnementaux et sanitaires.',
]);

get_footer();