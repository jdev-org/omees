<?php
get_header();

$catalogues_options = get_option('omees_catalogues_options', []);

get_template_part('template-parts/components/resource-listing', null, [
    'title' => !empty($catalogues_options['mediatheque_title'])
        ? $catalogues_options['mediatheque_title']
        : 'Médiathèque',

    'description' => !empty($catalogues_options['mediatheque_description'])
        ? $catalogues_options['mediatheque_description']
        : 'Retrouvez toutes les ressources pour comprendre, explorer et approfondir les travaux de l’OMEES.',
]);

get_footer();