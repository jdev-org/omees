<?php
if (!defined('ABSPATH')) {
    exit;
}

add_filter('single_template', function ($template) {
    if (is_singular('ressource') && has_term('decryptage', 'type-de-contenu', get_queried_object_id())) {
        $custom_template = locate_template('single-ressource-decryptage.php');

        if ($custom_template) {
            return $custom_template;
        }
    }

    return $template;
});
