
<?php
$catalogues_options = get_option('omees_catalogues_options', []);

$filter_title = $catalogues_options['filter_title'] ?? 'Filtrer les ressources';

$fixed_type = $args['fixed_type'] ?? '';

$selected_types = $args['types'] ?? [];
$selected_thematiques = $args['thematiques'] ?? [];

$filter_url = $fixed_type
    ? get_permalink()
    : get_post_type_archive_link('ressource');

$types = get_terms([
    'taxonomy'   => 'type-de-contenu',
    'hide_empty' => true,
]);

$thematiques = get_terms([
    'taxonomy'   => 'thematique',
    'hide_empty' => true,
]);
?>

<div
    id="resourceFilters"
    class="resourceFilters"
    data-url="<?php echo esc_url($filter_url); ?>"
    data-type="<?php echo esc_attr($fixed_type); ?>"
>
    <div class="resourceFilters__header mb-2">

        <h5><?php echo esc_html($filter_title); ?></h5>

        <a
            href="<?php echo esc_url($filter_url); ?>"
            class="btn btn-primary resourceFilters__reset"
        >
            <i class="ri-refresh-line"></i>
            Réinitialiser
        </a>

    </div>

    <div class="row">

        <!-- THÉMATIQUES -->
        <div class="resourceFilters__group col-12 <?php echo $fixed_type ? '' : 'col-lg-8'; ?>">

            <div class="resourceFilters__title">
                Thématiques
            </div>

            <div class="resourceFilters__options  mb-3">

                <?php if (!is_wp_error($thematiques) && !empty($thematiques)) : ?>

                    <?php foreach ($thematiques as $thematique) : ?>

                        <label class="resourceFilters__option">

                            <input
                                type="checkbox"
                                name="thematiques[]"
                                value="<?php echo esc_attr($thematique->slug); ?>"
                                <?php checked(
                                    in_array($thematique->slug, $selected_thematiques, true)
                                ); ?>
                            >

                            <span>
                                <?php echo esc_html($thematique->name); ?>
                            </span>

                        </label>

                    <?php endforeach; ?>

                <?php endif; ?>

            </div>

        </div>

        <!-- TYPES DE CONTENU -->
        <?php if (!$fixed_type) : ?>

            <div class="resourceFilters__group col-12 col-lg-4">

                <div class="resourceFilters__title">
                    Type de contenu
                </div>

                <div class="resourceFilters__options">

                    <?php if (!is_wp_error($types) && !empty($types)) : ?>

                        <?php foreach ($types as $type) : ?>

                            <label class="resourceFilters__option">

                                <input
                                    type="checkbox"
                                    name="types[]"
                                    value="<?php echo esc_attr($type->slug); ?>"
                                    <?php checked(
                                        in_array($type->slug, $selected_types, true)
                                    ); ?>
                                >

                                <span>
                                    <?php echo esc_html($type->name); ?>
                                </span>

                            </label>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </div>

            </div>

        <?php endif; ?>

    </div>

    <input
        type="hidden"
        id="resourceAjaxUrl"
        value="<?php echo esc_url(admin_url('admin-ajax.php')); ?>"
    >

    <input
        type="hidden"
        id="resourceAjaxNonce"
        value="<?php echo esc_attr(wp_create_nonce('omees_resource_filters')); ?>"
    >

</div>
