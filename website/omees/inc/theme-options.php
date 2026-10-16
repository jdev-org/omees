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
});


/**
 * Enregistrement des options
 */
add_action('admin_init', function () {

    register_setting(
        'omees_theme_options',
        'omees_theme_options',
        [
            'type' => 'array',
            'sanitize_callback' => 'omees_sanitize_theme_options',
            'default' => [],
        ]
    );
});


/**
 * Nettoyage / validation des données
 */
function omees_sanitize_theme_options($input)
{
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
 * Page d'options
 */
function omees_theme_general_page()
{
    if (!current_user_can('manage_options')) {
        return;
    }

    $options = get_option('omees_theme_options', []);

    $nav_logo = $options['nav_logo'] ?? 0;
    $footer_logo = $options['footer_logo'] ?? 0;
    $footer_slogan = $options['footer_slogan'] ?? '';
    $contact_email = $options['contact_email'] ?? '';
    $linkedin_url = $options['linkedin_url'] ?? '';
    $partners = $options['partners'] ?? [];
    ?>

    <div class="wrap">

        <h1>Thème OMEES</h1>

        <form method="post" action="options.php">

            <?php settings_fields('omees_theme_options'); ?>

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


            <hr>


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
                        ><?= esc_textarea($footer_slogan); ?></textarea>
                    </td>
                </tr>

            </table>


            <hr>


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
                            value="<?= esc_attr($contact_email); ?>"
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
                            value="<?= esc_attr($linkedin_url); ?>"
                            class="regular-text"
                            placeholder="https://www.linkedin.com/..."
                        >
                    </td>
                </tr>

            </table>


            <hr>


            <h2>Partenaires</h2>

            <p>
                Ajoutez autant de partenaires que nécessaire.
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
                >
                    ↑
                </button>

                <button
                    type="button"
                    class="button omees-move-down"
                    title="Descendre"
                >
                    ↓
                </button>
            </div>

            <div class="omees-partner-fields">

                <input
                    type="text"
                    name="omees_theme_options[partners][<?= esc_attr($index); ?>][name]"
                    value="<?= esc_attr($name); ?>"
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
                    name="omees_theme_options[partners][<?= esc_attr($index); ?>][url]"
                    value="<?= esc_attr($url); ?>"
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

            <p>
                <button
                    type="button"
                    id="omees-add-partner"
                    class="button button-secondary"
                >
                    Ajouter un partenaire
                </button>
            </p>

            <?php submit_button(); ?>

        </form>

    </div>


    <style>


        .omees-media-field {
            display: flex;
            align-items: center;
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
    background: #dcdcde;
    border: 1px solid #c3c4c7;
    border-radius: 6px;
}

        .omees-media-preview img {
            display: block;
            max-width: 120px;
            max-height: 80px;
            object-fit: contain;
        }

        .omees-partner-row {
            display: flex;
            align-items: center;
            gap: 20px;

            padding: 15px;
            margin-bottom: 10px;

            background: #fff;
            border: 1px solid #dcdcde;
        }

        .omees-partner-row {
    display: flex;
    align-items: center;
    gap: 20px;

    padding: 15px;
    margin-bottom: 10px;

    background: #fff;
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
    gap: 20px;
    flex: 1;
}

    </style>


    <script>

        document.addEventListener('DOMContentLoaded', function () {

            let partnerIndex = <?= (int) count($partners); ?>;

            /**
             * Médiathèque WordPress
             */
            document.addEventListener('click', function (event) {

                if (!event.target.classList.contains('omees-select-media')) {
                    return;
                }

                event.preventDefault();

                const wrapper = event.target.closest('.omees-media-field');

                const input = wrapper.querySelector(
                    '.omees-media-id'
                );

                const preview = wrapper.querySelector(
                    '.omees-media-preview'
                );

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

                    preview.innerHTML =
                        '<img src="' + attachment.url + '" alt="">';

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
                wrapper.querySelector('.omees-media-preview').innerHTML = '';

            });


            /**
             * Ajout d'un partenaire
             */
            document.getElementById('omees-add-partner')
                .addEventListener('click', function () {

                    const container = document.getElementById(
                        'omees-partners'
                    );

                    const row = document.createElement('div');

                    row.className = 'omees-partner-row';

                    row.innerHTML = `
    <div class="omees-partner-order">
        <button
            type="button"
            class="button omees-move-up"
            title="Monter"
        >
            ↑
        </button>

        <button
            type="button"
            class="button omees-move-down"
            title="Descendre"
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

                });


            /**
             * Suppression d'un partenaire
             */
            document.addEventListener('click', function (event) {

                if (!event.target.classList.contains('omees-remove-partner')) {
                    return;
                }

                event.preventDefault();

                event.target
                    .closest('.omees-partner-row')
                    .remove();

            });

        });

        document.addEventListener('click', function (event) {

    if (event.target.classList.contains('omees-move-up')) {

        const row = event.target.closest('.omees-partner-row');
        const previous = row.previousElementSibling;

        if (previous) {
            row.parentNode.insertBefore(row, previous);
            omeesReindexPartners();
        }
    }


    if (event.target.classList.contains('omees-move-down')) {

        const row = event.target.closest('.omees-partner-row');
        const next = row.nextElementSibling;

        if (next) {
            row.parentNode.insertBefore(next, row);
            omeesReindexPartners();
        }
    }

});

function omeesReindexPartners() {

    const rows = document.querySelectorAll('.omees-partner-row');

    rows.forEach(function (row, index) {

        row.querySelectorAll('input').forEach(function (input) {

            if (!input.name) {
                return;
            }

            input.name = input.name.replace(
                /omees_theme_options\[partners\]\[\d+\]/,
                `omees_theme_options[partners][${index}]`
            );

        });

    });

}

if (event.target.classList.contains('omees-remove-partner')) {

    event.preventDefault();

    event.target
        .closest('.omees-partner-row')
        .remove();

    omeesReindexPartners();
}

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
            name="<?= esc_attr($name); ?>"
            value="<?= esc_attr($attachment_id); ?>"
            class="omees-media-id"
        >

        <div class="omees-media-preview">

            <?php if ($image_url) : ?>

                <img
                    src="<?= esc_url($image_url); ?>"
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