<?php

if (!defined('ABSPATH')) {
    exit;
}
function omees_home_get_link($link)
{
    if (!is_array($link)) {
        return '';
    }

    if (!empty($link['page_id'])) {
        $permalink = get_permalink((int) $link['page_id']);

        if ($permalink) {
            return $permalink;
        }
    }

    return $link['url'] ?? '';
}

/**
 * Champs de la page d'accueil
 */
function omees_home_fields()
{
    return [
        'Hero' => [
            'hero_title' => ['Titre', 'html'],
            'hero_image' => ['Image de fond', 'image'],
        ],
        "Présentation de l'OMEES" => [
            'presentation_title' => ['Titre', 'text'],
            'presentation_text' => ['Texte', 'html'],
            'presentation_button_label' => ['Label bouton', 'text'],
            'presentation_button_url' => ['URL du bouton', 'link'],
            'presentation_map_title' => ['Titre de la carte', 'text'],
            'presentation_map_url' => ['URL vers la carte', 'link'],
            'presentation_map_image' => ['Image de fond de la carte', 'image'],
        ],
        'Thématiques' => [
            'themes_title' => ['Titre', 'text'],
        ],
        'Décryptages' => [
            'decryptages_title' => ['Titre', 'text'],
            'decryptages_subtitle' => ['Sous-titre pour consulter les décryptages selon les thématiques', 'text'],
        ],
        'Outils' => [
            'tools_title' => ['Titre', 'text'],
        ],
        'Ressources' => [
            'resources_title' => ['Titre', 'text'],
        ],
    ];
}

/**
 * Enregistrement
 */
add_action('admin_init', function () {
    register_setting('omees_home_group', 'omees_home_options', [
        'type' => 'array',
        'default' => [],
        'sanitize_callback' => 'omees_sanitize_home_options',
    ]);
});

function omees_sanitize_home_options($input)
{
    $output = [];

    foreach (omees_home_fields() as $fields) {
        foreach ($fields as $key => $field) {
            $type = $field[1];
            $value = $input[$key] ?? '';

            switch ($type) {
                case 'image':
                    $output[$key] = absint($value);
                    break;

                case 'html':
                    $output[$key] = wp_kses_post($value);
                    break;

                case 'textarea':
                    $output[$key] = sanitize_textarea_field($value);
                    break;

                case 'link':
                    $output[$key] = [
                        'url' => esc_url_raw($value['url'] ?? ''),
                        'page_id' => absint($value['page_id'] ?? 0),
                    ];
                    break;

                default:
                    $output[$key] = sanitize_text_field($value);
            }
        }
    }

    $output['tools'] = [];

    if (!empty($input['tools']) && is_array($input['tools'])) {
        foreach ($input['tools'] as $tool) {
            $title = sanitize_text_field($tool['title'] ?? '');
            $text = sanitize_textarea_field($tool['text'] ?? '');
            $url = esc_url_raw($tool['url'] ?? '');
            $page_id = absint($tool['page_id'] ?? 0);

            if (!$title && !$text && !$url && !$page_id) {
                continue;
            }

            $output['tools'][] = [
                'title' => $title,
                'text' => $text,
                'url' => $url,
                'page_id' => $page_id,
            ];
        }
    }

    return $output;
}

/**
 * Chargement de la médiathèque
 */
add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook === 'theme-omees_page_omees-home') {
        wp_enqueue_media();
    }
});

/**
 * Champ URL + page WordPress
 */
function omees_home_link_field($name, $value = [])
{
    $value = is_array($value) ? $value : [];

    $url = $value['url'] ?? '';
    $page_id = $value['page_id'] ?? 0;
    ?>

    <div class="omees-home-link">
        <input
            type="url"
            class="regular-text"
            name="<?php echo esc_attr($name); ?>[url]"
            value="<?php echo esc_attr($url); ?>"
            placeholder="https://..."
        >

        <span>ou sélectionner une page :</span>

        <?php
        wp_dropdown_pages([
            'name' => $name . '[page_id]',
            'selected' => $page_id,
            'show_option_none' => '— Choisir une page —',
            'option_none_value' => '0',
        ]);
        ?>
    </div>

    <?php
}

/**
 * Affichage d'un outil
 */
function omees_home_tool_row($index, $tool = [])
{
    $title = $tool['title'] ?? '';
    $text = $tool['text'] ?? '';
    $url = $tool['url'] ?? '';
    $page_id = $tool['page_id'] ?? 0;
    $name = "omees_home_options[tools][$index]";
    ?>

    <div class="omees-home-tool">
        <div class="omees-home-tool__header">
            <strong>Outil</strong>

            <div>
                <button type="button" class="button omees-tool-up">↑</button>
                <button type="button" class="button omees-tool-down">↓</button>
                <button type="button" class="button omees-tool-remove">
                    Supprimer
                </button>
            </div>
        </div>

        <p>
            <label>Titre</label>
            <input
                type="text"
                class="large-text"
                name="<?php echo esc_attr($name); ?>[title]"
                value="<?php echo esc_attr($title); ?>"
            >
        </p>

        <p>
            <label>Texte</label>
            <textarea
                class="large-text"
                rows="3"
                name="<?php echo esc_attr($name); ?>[text]"
            ><?php echo esc_textarea($text); ?></textarea>
        </p>

        <p>
            <label>URL</label>
            <?php omees_home_link_field($name, [
                'url' => $url,
                'page_id' => $page_id,
            ]); ?>
        </p>
    </div>

    <?php
}

/**
 * Page d'administration
 */
function omees_theme_home_page()
{
    if (!current_user_can('manage_options')) {
        return;
    }

    $options = get_option('omees_home_options', []);
    $tools = $options['tools'] ?? [];
    ?>

    <div class="wrap omees-home-admin">

        <h1>Configuration de l'accueil</h1>

        <p>Personnalisez les contenus de la page d'accueil d'OMEES.</p>

        <form method="post" action="options.php">

            <?php settings_fields('omees_home_group'); ?>

            <?php foreach (omees_home_fields() as $section => $fields) : ?>

                <div class="omees-home-section">

                    <h2><?php echo esc_html($section); ?></h2>

                    <table class="form-table">

                        <?php foreach ($fields as $key => $field) :

                            [$label, $type] = $field;
                            $value = $options[$key] ?? '';
                            $name = "omees_home_options[$key]";
                        ?>

                            <tr>
                                <th scope="row">
                                    <?php echo esc_html($label); ?>
                                </th>

                                <td>

                                    <?php if ($type === 'image') : ?>

                                        <?php
                                        omees_media_field($name, absint($value));
                                        ?>

                                    <?php elseif ($type === 'html') : ?>

                                        <?php
                                        wp_editor($value, $key, [
                                            'textarea_name' => $name,
                                            'textarea_rows' => 5,
                                            'media_buttons' => false,
                                            'teeny' => true,
                                        ]);
                                        ?>

                                    <?php elseif ($type === 'textarea') : ?>

                                        <textarea
                                            name="<?php echo esc_attr($name); ?>"
                                            class="large-text"
                                            rows="5"
                                        ><?php echo esc_textarea($value); ?></textarea>

                                    <?php elseif ($type === 'link') : ?>

                                        <?php omees_home_link_field($name, $value); ?>

                                    <?php else : ?>

                                        <input
                                            type="text"
                                            class="regular-text"
                                            name="<?php echo esc_attr($name); ?>"
                                            value="<?php echo esc_attr($value); ?>"
                                        >

                                    <?php endif; ?>

                                </td>
                            </tr>

                        <?php endforeach; ?>

                    </table>

                    <?php if ($section === 'Outils') : ?>

                        <h3>Liste des outils</h3>

                        <div id="omees-home-tools">
                            <?php foreach ($tools as $index => $tool) : ?>
                                <?php omees_home_tool_row($index, $tool); ?>
                            <?php endforeach; ?>
                        </div>

                        <button
                            type="button"
                            class="button button-secondary"
                            id="omees-add-tool"
                        >
                            + Ajouter un outil
                        </button>

                    <?php endif; ?>

                </div>

            <?php endforeach; ?>

            <?php submit_button('Enregistrer les modifications'); ?>

        </form>

    </div>

    <style>
        .omees-home-admin {
            max-width: 1100px;
        }

        .omees-media-preview img {
            max-width: 70%;
        }

        .omees-home-section {
            margin: 25px 0;
            padding: 20px 25px;
            background: #fff;
            border: 1px solid #dcdcde;
            border-radius: 8px;
        }

        .omees-home-section h2 {
            margin: 0 0 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .omees-home-link {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 12px;
        }

        .omees-home-tool {
            padding: 20px;
            margin-bottom: 15px;
            background: #f6f7f7;
            border: 1px solid #dcdcde;
            border-radius: 6px;
        }

        .omees-home-tool__header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .omees-home-tool label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            function reindexTools() {
                container.querySelectorAll('.omees-home-tool').forEach(function (row, index) {
                    row.querySelectorAll('[name]').forEach(function (field) {
                        field.name = field.name.replace(
                            /omees_home_options\[tools\]\[\d+\]/,
                            `omees_home_options[tools][${index}]`
                        );
                    });
                });
            }

            const container = document.getElementById('omees-home-tools');
            const addButton = document.getElementById('omees-add-tool');

            let nextIndex = <?php echo (int) count($tools); ?>;

            // Ajouter un outil
            addButton.addEventListener('click', function () {

                const index = nextIndex++;
                const row = document.createElement('div');

                row.className = 'omees-home-tool';

                row.innerHTML = `
                    <div class="omees-home-tool__header">
                        <strong>Outil</strong>
                        <div>
                            <button type="button" class="button omees-tool-up">↑</button>
                            <button type="button" class="button omees-tool-down">↓</button>
                            <button type="button" class="button omees-tool-remove">Supprimer</button>
                        </div>
                    </div>

                    <p>
                        <label>Titre</label>
                        <input type="text" class="large-text"
                            name="omees_home_options[tools][${index}][title]">
                    </p>

                    <p>
                        <label>Texte</label>
                        <textarea class="large-text" rows="3"
                            name="omees_home_options[tools][${index}][text]"></textarea>
                    </p>

                    <p>
                        <label>URL</label>
                        <input type="url" class="regular-text"
                            name="omees_home_options[tools][${index}][url]"
                            placeholder="https://...">
                        <span>ou sélectionner une page :</span>
                        <select name="omees_home_options[tools][${index}][page_id]">
                            ${pageOptions}
                        </select>
                    </p>
                `;

                container.appendChild(row);
            });

            // Options de pages disponibles
            const pageSelect = document.querySelector(
                '.omees-home-tool select[name$="[page_id]"]'
            );

            const pageOptions = <?php
                $pages = get_pages(['sort_column' => 'post_title']);
                $page_options = '<option value="0">— Choisir une page —</option>';

                foreach ($pages as $page) {
                    $page_options .= '<option value="' . (int) $page->ID . '">'
                        . esc_html($page->post_title) . '</option>';
                }

                echo wp_json_encode($page_options);
            ?>;

            // Supprimer / réordonner
            container.addEventListener('click', function (event) {

                const button = event.target.closest('button');
                if (!button) return;

                const row = button.closest('.omees-home-tool');
                if (!row) return;

                if (button.classList.contains('omees-tool-remove')) {
                    row.remove();
                }

                if (button.classList.contains('omees-tool-up') && row.previousElementSibling) {
                    container.insertBefore(row, row.previousElementSibling);
                }

                if (button.classList.contains('omees-tool-down') && row.nextElementSibling) {
                    container.insertBefore(row.nextElementSibling, row);
                }
            });

            // Médiathèque WordPress
            document.querySelectorAll('.omees-home-admin .omees-select-media').forEach(function (button) {

                button.addEventListener('click', function (event) {
                    event.preventDefault();

                    const wrapper = button.closest('.omees-media-field');

                    const frame = wp.media({
                        title: 'Choisir une image',
                        button: { text: 'Utiliser cette image' },
                        multiple: false
                    });

                    frame.on('select', function () {
                        const attachment = frame.state().get('selection').first().toJSON();

                        wrapper.querySelector('.omees-media-id').value = attachment.id;

                        const preview = wrapper.querySelector('.omees-media-preview');
                        preview.replaceChildren();

                        const img = document.createElement('img');
                        img.src = attachment.url;
                        img.alt = '';
                        preview.appendChild(img);
                    });

                    frame.open();
                });
            });

            document.querySelectorAll('.omees-home-admin .omees-remove-media').forEach(function (button) {

                button.addEventListener('click', function (event) {
                    event.preventDefault();

                    const wrapper = button.closest('.omees-media-field');

                    wrapper.querySelector('.omees-media-id').value = '';
                    wrapper.querySelector('.omees-media-preview').replaceChildren();
                });
            });

        });
    </script>

    <?php
}