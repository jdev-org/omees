<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Menu d'administration
 */
add_action('admin_menu', function () {

    add_menu_page(
        'Thème OMEES',
        'Thème OMEES',
        'manage_options',
        'omees-theme',
        'omees_theme_general_page',
        'dashicons-admin-customizer',
        60
    );

    add_submenu_page(
        'omees-theme',
        'Général',
        'Général',
        'manage_options',
        'omees-theme',
        'omees_theme_general_page'
    );

    add_submenu_page(
        'omees-theme',
        'Accueil',
        'Accueil',
        'manage_options',
        'omees-home',
        'omees_theme_home_page'
    );

    add_submenu_page(
        'omees-theme',
        'Catalogues & filtres',
        'Catalogues & filtres',
        'manage_options',
        'omees-catalogues',
        'omees_theme_catalogues_page'
    );
});

/**
 * Enregistrement des options
 */
add_action('admin_init', function () {

    register_setting(
        'omees_theme_options',
        'omees_theme_options',
        [
            'type'              => 'array',
            'sanitize_callback' => 'omees_sanitize_theme_options',
            'default'           => [],
        ]
    );
});

/**
 * Nettoyage / validation des données
 */
function omees_sanitize_theme_options($input)
{
    $input = is_array($input) ? $input : [];
    $output = [];

    $output['nav_logo'] = isset($input['nav_logo'])
        ? absint($input['nav_logo'])
        : 0;

    $output['footer_logo'] = isset($input['footer_logo'])
        ? absint($input['footer_logo'])
        : 0;

    $output['footer_slogan'] = isset($input['footer_slogan'])
        ? sanitize_textarea_field($input['footer_slogan'])
        : '';

    $output['contact_email'] = isset($input['contact_email'])
        ? sanitize_email($input['contact_email'])
        : '';

    $output['linkedin_url'] = isset($input['linkedin_url'])
        ? esc_url_raw($input['linkedin_url'])
        : '';

    $output['partners'] = [];

    if (!empty($input['partners']) && is_array($input['partners'])) {

        foreach ($input['partners'] as $partner) {

            $name = isset($partner['name'])
                ? sanitize_text_field($partner['name'])
                : '';

            $logo = isset($partner['logo'])
                ? absint($partner['logo'])
                : 0;

            $url = isset($partner['url'])
                ? esc_url_raw($partner['url'])
                : '';

            if (!$name && !$logo && !$url) {
                continue;
            }

            $output['partners'][] = [
                'name' => $name,
                'logo' => $logo,
                'url'  => $url,
            ];
        }
    }

    return $output;
}

/**
 * Chargement de la médiathèque WordPress
 */
add_action('admin_enqueue_scripts', function ($hook) {

    if ($hook !== 'toplevel_page_omees-theme') {
        return;
    }

    wp_enqueue_media();
});

/**
 * Page d'options générales
 */
function omees_theme_general_page()
{
    if (!current_user_can('manage_options')) {
        return;
    }

    $options = get_option('omees_theme_options', []);

    $nav_logo      = $options['nav_logo'] ?? 0;
    $footer_logo   = $options['footer_logo'] ?? 0;
    $footer_slogan = $options['footer_slogan'] ?? '';
    $contact_email = $options['contact_email'] ?? '';
    $linkedin_url  = $options['linkedin_url'] ?? '';
    $partners      = $options['partners'] ?? [];

    ?>

    <div class="wrap omees-general-admin">

        <h1>Thème OMEES</h1>

        <p class="omees-general-intro">
            Configurez les éléments globaux de votre thème.
        </p>

        <form method="post" action="options.php">

            <?php settings_fields('omees_theme_options'); ?>

            <!-- BARRE DE NAVIGATION -->

            <div class="omees-general-section">

                <h2>Barre de navigation</h2>

                <table class="form-table">

                    <tr>
                        <th scope="row">
                            Logo de la navbar
                        </th>

                        <td>
                            <?php
                            omees_media_field(
                                'omees_theme_options[nav_logo]',
                                $nav_logo
                            );
                            ?>
                        </td>
                    </tr>

                </table>

            </div>

            <!-- PIED DE PAGE -->

            <div class="omees-general-section">

                <h2>Pied de page</h2>

                <table class="form-table">

                    <tr>
                        <th scope="row">
                            Logo du footer
                        </th>

                        <td>
                            <?php
                            omees_media_field(
                                'omees_theme_options[footer_logo]',
                                $footer_logo
                            );
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            Slogan footer
                        </th>

                        <td>
                            <textarea
                                name="omees_theme_options[footer_slogan]"
                                rows="4"
                                class="large-text"
                            ><?php echo esc_textarea($footer_slogan); ?></textarea>
                        </td>
                    </tr>

                </table>

            </div>

            <!-- CONTACT ET RÉSEAUX SOCIAUX -->

            <div class="omees-general-section">

                <h2>Contact et réseaux sociaux</h2>

                <table class="form-table">

                    <tr>
                        <th scope="row">
                            Adresse e-mail
                        </th>

                        <td>
                            <input
                                type="email"
                                name="omees_theme_options[contact_email]"
                                value="<?php echo esc_attr($contact_email); ?>"
                                class="regular-text"
                            >
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            URL LinkedIn
                        </th>

                        <td>
                            <input
                                type="url"
                                name="omees_theme_options[linkedin_url]"
                                value="<?php echo esc_attr($linkedin_url); ?>"
                                class="regular-text"
                                placeholder="https://www.linkedin.com/..."
                            >
                        </td>
                    </tr>

                </table>

            </div>

            <!-- PARTENAIRES -->

            <div class="omees-general-section">

                <h2>Partenaires</h2>

                <p class="description">
                    Ajoutez autant de partenaires que nécessaire.
                    Vous pouvez également modifier leur ordre d'affichage.
                </p>

                <div id="omees-partners">

                    <?php foreach ($partners as $index => $partner) : ?>

                        <?php
                        $name = $partner['name'] ?? '';
                        $logo = $partner['logo'] ?? 0;
                        $url  = $partner['url'] ?? '';
                        ?>

                        <div class="omees-partner-row">

                            <div class="omees-partner-order">

                                <button
                                    type="button"
                                    class="button omees-move-up"
                                    title="Monter"
                                    aria-label="Monter le partenaire"
                                >
                                    ↑
                                </button>

                                <button
                                    type="button"
                                    class="button omees-move-down"
                                    title="Descendre"
                                    aria-label="Descendre le partenaire"
                                >
                                    ↓
                                </button>

                            </div>

                            <div class="omees-partner-fields">

                                <input
                                    type="text"
                                    name="omees_theme_options[partners][<?php echo esc_attr($index); ?>][name]"
                                    value="<?php echo esc_attr($name); ?>"
                                    placeholder="Nom du partenaire"
                                    class="regular-text"
                                >

                                <?php
                                omees_media_field(
                                    "omees_theme_options[partners][$index][logo]",
                                    $logo
                                );
                                ?>

                                <input
                                    type="url"
                                    name="omees_theme_options[partners][<?php echo esc_attr($index); ?>][url]"
                                    value="<?php echo esc_attr($url); ?>"
                                    placeholder="https://..."
                                    class="regular-text"
                                >

                            </div>

                            <button
                                type="button"
                                class="button omees-remove-partner"
                            >
                                Supprimer
                            </button>

                        </div>

                    <?php endforeach; ?>

                </div>

                <p class="omees-add-partner-wrap">

                    <button
                        type="button"
                        id="omees-add-partner"
                        class="button button-secondary"
                    >
                        + Ajouter un partenaire
                    </button>

                </p>

            </div>

            <div class="omees-general-submit">
                <?php submit_button('Enregistrer les modifications'); ?>
            </div>

        </form>

    </div>

    <style>
        /* ==========================================
           ADMIN OMEES — OPTIONS GÉNÉRALES
        ========================================== */

        .omees-general-admin {
            max-width: 1100px;
        }

        .omees-general-intro {
            margin-bottom: 25px;
            color: #646970;
        }

        /* Sections blanches */

        .omees-general-section {
            margin: 25px 0;
            padding: 20px 25px;
            background: #fff;
            border: 1px solid #dcdcde;
            border-radius: 8px;
        }

        .omees-general-section h2 {
            margin: 0 0 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
            font-size: 1.3rem;
            font-weight: 600;
        }

        /* Tables */

        .omees-general-section .form-table {
            margin-top: 0;
        }

        .omees-general-section .form-table th {
            width: 220px;
            font-weight: 600;
        }

        /* Champs */

        .omees-general-section input[type="text"],
        .omees-general-section input[type="email"],
        .omees-general-section input[type="url"],
        .omees-general-section textarea {
            max-width: 100%;
            border-radius: 5px;
        }

        /* Médiathèque */

        .omees-media-field {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .omees-media-preview {
            width: 140px;
            height: 90px;
            padding: 10px;
            box-sizing: border-box;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f6f7f7;
            border: 1px solid #dcdcde;
            border-radius: 6px;
        }

        .omees-media-preview img {
            display: block;
            max-width: 120px;
            max-height: 70px;
            object-fit: contain;
        }

        /* Partenaires */

        .omees-general-section .description {
            margin-bottom: 20px;
        }

        .omees-partner-row {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 20px;
            margin-bottom: 15px;
            background: #f6f7f7;
            border: 1px solid #dcdcde;
            border-radius: 6px;
        }

        .omees-partner-order {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .omees-partner-fields {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
            flex: 1;
            min-width: 0;
        }

        .omees-partner-fields > input {
            flex: 1;
            min-width: 180px;
        }

        .omees-partner-row .omees-remove-partner {
            color: #b32d2e;
            border-color: #dba7a7;
        }

        .omees-partner-row .omees-remove-partner:hover {
            color: #fff;
            background: #b32d2e;
            border-color: #b32d2e;
        }

        .omees-add-partner-wrap {
            margin-top: 20px;
        }

        /* Enregistrement */

        .omees-general-submit {
            margin-top: 25px;
        }

        /* Responsive */

        @media (max-width: 782px) {

            .omees-general-section {
                padding: 20px 15px;
            }

            .omees-partner-row {
                flex-direction: column;
                align-items: stretch;
            }

            .omees-partner-order {
                flex-direction: row;
            }

            .omees-partner-fields {
                flex-direction: column;
                align-items: stretch;
            }

            .omees-partner-fields > input {
                flex: auto;
                width: 100%;
            }

            .omees-media-field {
                flex-wrap: wrap;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const container = document.getElementById('omees-partners');
            const addButton = document.getElementById('omees-add-partner');

            let partnerIndex = <?php echo (int) count($partners); ?>;

            /**
             * Médiathèque WordPress
             */
            document.addEventListener('click', function (event) {

                if (!event.target.classList.contains('omees-select-media')) {
                    return;
                }

                event.preventDefault();

                const wrapper = event.target.closest('.omees-media-field');

                const input = wrapper.querySelector('.omees-media-id');
                const preview = wrapper.querySelector('.omees-media-preview');

                const frame = wp.media({
                    title: 'Choisir une image',
                    button: {
                        text: 'Utiliser cette image'
                    },
                    multiple: false
                });

                frame.on('select', function () {

                    const attachment = frame
                        .state()
                        .get('selection')
                        .first()
                        .toJSON();

                    input.value = attachment.id;

                    preview.replaceChildren();

                    const image = document.createElement('img');
                    image.src = attachment.url;
                    image.alt = '';

                    preview.appendChild(image);
                });

                frame.open();
            });

            /**
             * Suppression d'une image
             */
            document.addEventListener('click', function (event) {

                if (!event.target.classList.contains('omees-remove-media')) {
                    return;
                }

                event.preventDefault();

                const wrapper = event.target.closest('.omees-media-field');

                wrapper.querySelector('.omees-media-id').value = '';
                wrapper.querySelector('.omees-media-preview').replaceChildren();
            });

            /**
             * Réindexation des partenaires
             */
            function omeesReindexPartners() {

                const rows = container.querySelectorAll('.omees-partner-row');

                rows.forEach(function (row, index) {

                    row.querySelectorAll('input[name]').forEach(function (input) {

                        input.name = input.name.replace(
                            /omees_theme_options\[partners\]\[\d+\]/,
                            `omees_theme_options[partners][${index}]`
                        );
                    });
                });

                partnerIndex = rows.length;
            }

            /**
             * Ajouter un partenaire
             */
            addButton.addEventListener('click', function () {

                const row = document.createElement('div');

                row.className = 'omees-partner-row';

                row.innerHTML = `
                    <div class="omees-partner-order">

                        <button
                            type="button"
                            class="button omees-move-up"
                            title="Monter"
                            aria-label="Monter le partenaire"
                        >
                            ↑
                        </button>

                        <button
                            type="button"
                            class="button omees-move-down"
                            title="Descendre"
                            aria-label="Descendre le partenaire"
                        >
                            ↓
                        </button>

                    </div>

                    <div class="omees-partner-fields">

                        <input
                            type="text"
                            name="omees_theme_options[partners][${partnerIndex}][name]"
                            placeholder="Nom du partenaire"
                            class="regular-text"
                        >

                        <div class="omees-media-field">

                            <input
                                type="hidden"
                                name="omees_theme_options[partners][${partnerIndex}][logo]"
                                value=""
                                class="omees-media-id"
                            >

                            <div class="omees-media-preview"></div>

                            <button
                                type="button"
                                class="button omees-select-media"
                            >
                                Choisir un logo
                            </button>

                            <button
                                type="button"
                                class="button omees-remove-media"
                            >
                                Retirer
                            </button>

                        </div>

                        <input
                            type="url"
                            name="omees_theme_options[partners][${partnerIndex}][url]"
                            placeholder="https://..."
                            class="regular-text"
                        >

                    </div>

                    <button
                        type="button"
                        class="button omees-remove-partner"
                    >
                        Supprimer
                    </button>
                `;

                container.appendChild(row);

                partnerIndex++;

                omeesReindexPartners();
            });

            /**
             * Supprimer / déplacer un partenaire
             */
            container.addEventListener('click', function (event) {

                const button = event.target.closest('button');

                if (!button) {
                    return;
                }

                const row = button.closest('.omees-partner-row');

                if (!row) {
                    return;
                }

                if (button.classList.contains('omees-remove-partner')) {

                    event.preventDefault();

                    row.remove();

                    omeesReindexPartners();
                }

                if (button.classList.contains('omees-move-up')) {

                    event.preventDefault();

                    const previous = row.previousElementSibling;

                    if (previous) {
                        container.insertBefore(row, previous);
                        omeesReindexPartners();
                    }
                }

                if (button.classList.contains('omees-move-down')) {

                    event.preventDefault();

                    const next = row.nextElementSibling;

                    if (next) {
                        container.insertBefore(next, row);
                        omeesReindexPartners();
                    }
                }
            });

        });
    </script>

    <?php
}

/**
 * Champ image réutilisable
 */
function omees_media_field($name, $attachment_id = 0)
{
    $image_url = '';

    if ($attachment_id) {
        $image_url = wp_get_attachment_image_url(
            $attachment_id,
            'medium'
        );
    }
    ?>

    <div class="omees-media-field">

        <input
            type="hidden"
            name="<?php echo esc_attr($name); ?>"
            value="<?php echo esc_attr($attachment_id); ?>"
            class="omees-media-id"
        >

        <div class="omees-media-preview">

            <?php if ($image_url) : ?>

                <img
                    src="<?php echo esc_url($image_url); ?>"
                    alt=""
                >

            <?php endif; ?>

        </div>

        <button
            type="button"
            class="button omees-select-media"
        >
            Choisir une image
        </button>

        <button
            type="button"
            class="button omees-remove-media"
        >
            Retirer
        </button>

    </div>

    <?php
}
