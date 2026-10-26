<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Configuration des champs
 */
function omees_catalogues_fields()
{
    return [
        'Cartothèque' => [
            'cartotheque_title' => [
                'Titre',
                'text',
            ],
            'cartotheque_description' => [
                'Texte de présentation',
                'textarea',
            ],
        ],

        'Décryptages' => [
            'decryptages_title' => [
                'Titre',
                'text',
            ],
            'decryptages_description' => [
                'Texte de présentation',
                'textarea',
            ],
        ],

        'Médiathèque' => [
            'mediatheque_title' => [
                'Titre',
                'text',
            ],
            'mediatheque_description' => [
                'Texte de présentation',
                'textarea',
            ],
        ],

        'Filtres' => [
            'filter_title' => [
                'Nom du bloc de filtre',
                'text',
            ],
        ],
    ];
}

/**
 * Enregistrement des options
 */
add_action('admin_init', function () {

    register_setting(
        'omees_catalogues_group',
        'omees_catalogues_options',
        [
            'type'              => 'array',
            'sanitize_callback' => 'omees_sanitize_catalogues_options',
            'default'           => [],
        ]
    );
});

/**
 * Nettoyage des données
 */
function omees_sanitize_catalogues_options($input)
{
    $input = is_array($input) ? $input : [];
    $output = [];

    foreach (omees_catalogues_fields() as $fields) {

        foreach ($fields as $key => $field) {

            $type = $field[1];
            $value = $input[$key] ?? '';

            if ($type === 'textarea') {
                $output[$key] = sanitize_textarea_field($value);
            } else {
                $output[$key] = sanitize_text_field($value);
            }
        }
    }

    return $output;
}

/**
 * Page d'administration
 */
function omees_theme_catalogues_page()
{
    if (!current_user_can('manage_options')) {
        return;
    }

    $options = get_option('omees_catalogues_options', []);
    ?>

    <div class="wrap omees-catalogues-admin">

        <h1>Catalogues & filtres</h1>

        <p class="omees-catalogues-intro">
            Personnalisez les titres et les textes de présentation
            des catalogues ainsi que le libellé du bloc de filtre.
        </p>

        <form method="post" action="options.php">

            <?php settings_fields('omees_catalogues_group'); ?>

            <?php foreach (omees_catalogues_fields() as $section => $fields) : ?>

                <div class="omees-catalogues-section">

                    <h2>
                        <?php echo esc_html($section); ?>
                    </h2>

                    <table class="form-table">

                        <?php foreach ($fields as $key => $field) : ?>

                            <?php
                            $label = $field[0];
                            $type = $field[1];

                            $value = $options[$key] ?? '';
                            $name = "omees_catalogues_options[$key]";
                            ?>

                            <tr>

                                <th scope="row">
                                    <label for="<?php echo esc_attr($key); ?>">
                                        <?php echo esc_html($label); ?>
                                    </label>
                                </th>

                                <td>

                                    <?php if ($type === 'textarea') : ?>

                                        <textarea
                                            id="<?php echo esc_attr($key); ?>"
                                            name="<?php echo esc_attr($name); ?>"
                                            class="large-text"
                                            rows="5"
                                        ><?php echo esc_textarea($value); ?></textarea>

                                    <?php else : ?>

                                        <input
                                            type="text"
                                            id="<?php echo esc_attr($key); ?>"
                                            name="<?php echo esc_attr($name); ?>"
                                            value="<?php echo esc_attr($value); ?>"
                                            class="regular-text"
                                        >

                                    <?php endif; ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </table>

                </div>

            <?php endforeach; ?>

            <?php submit_button('Enregistrer les modifications'); ?>

        </form>

    </div>

    <style>
        /* ==========================================
           ADMIN OMEES — CATALOGUES & FILTRES
        ========================================== */

        .omees-catalogues-admin {
            max-width: 1100px;
        }

        .omees-catalogues-intro {
            margin-bottom: 25px;
            color: #646970;
        }

        /* Blocs blancs */

        .omees-catalogues-section {
            margin: 25px 0;
            padding: 20px 25px;
            background: #fff;
            border: 1px solid #dcdcde;
            border-radius: 8px;
        }

        /* Titres des sections */

        .omees-catalogues-section h2 {
            margin: 0 0 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
            font-size: 1.3rem;
            font-weight: 600;
        }

        /* Tables */

        .omees-catalogues-section .form-table {
            margin-top: 0;
        }

        .omees-catalogues-section .form-table th {
            width: 220px;
            font-weight: 600;
        }

        /* Champs */

        .omees-catalogues-section input[type="text"],
        .omees-catalogues-section textarea {
            max-width: 100%;
            border-radius: 5px;
        }

        .omees-catalogues-section textarea {
            max-width: 700px;
        }

        /* Responsive */

        @media (max-width: 782px) {

            .omees-catalogues-section {
                padding: 20px 15px;
            }

            .omees-catalogues-section .form-table th {
                width: auto;
            }
        }
    </style>

    <?php
}