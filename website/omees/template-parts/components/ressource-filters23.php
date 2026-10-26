<?php
$types_actifs =
    isset($_GET['types']) && is_string($_GET['types'])
        ? explode(',', sanitize_text_field(wp_unslash($_GET['types'])))
        : [];

$thematiques_actives =
    isset($_GET['thematiques']) && is_string($_GET['thematiques'])
        ? explode(',', sanitize_text_field(wp_unslash($_GET['thematiques'])))
        : [];

$types = get_terms([
    'taxonomy' => 'type-de-contenu',
    'hide_empty' => true,
]);

$thematiques = get_terms([
    'taxonomy' => 'thematique',
    'hide_empty' => true,
]);
?>

<div
    id="resourceFilters"
    class="resourceFilters"
    data-url="<?php echo esc_url(get_post_type_archive_link('ressource')); ?>"
>
    <div class="resourceFilters__header">    
        <h5> Filtrer les ressources</h5>
        
    </div>  
    <div class="row">
        
        <div class="resourceFilters__group col-lg-12">

            <div class="resourceFilters__title">Thématiques</div>

            <div class="resourceFilters__options">

                <?php if (!is_wp_error($thematiques)): ?>
                    <?php foreach ($thematiques as $thematique): ?>

                        <label class="resourceFilters__option">

                            <input
                                type="checkbox"
                                name="thematiques[]"
                                value="<?php echo esc_attr($thematique->slug); ?>"
                                <?php checked(in_array($thematique->slug, $thematiques_actives, true)); ?>
                            >

                            <span><?php echo esc_html($thematique->name); ?></span>

                        </label>

                    <?php endforeach; ?>
                <?php endif; ?>

            </div>

        </div>
        <div class="resourceFilters__group col-lg-12 my-4">

            <div class="resourceFilters__title">Type de contenu</div>

            <div class="resourceFilters__options">

                <?php if (!is_wp_error($types)): ?>
                    <?php foreach ($types as $type): ?>

                        <label class="resourceFilters__option">

                            <input
                                type="checkbox"
                                name="types[]"
                                value="<?php echo esc_attr($type->slug); ?>"
                                <?php checked(in_array($type->slug, $types_actifs, true)); ?>
                            >

                            <span><?php echo esc_html($type->name); ?></span>

                        </label>

                    <?php endforeach; ?>
                <?php endif; ?>

            </div>

        </div>
        <a
            href="<?php echo esc_url(get_post_type_archive_link('ressource')); ?>"
            class="btn btn-primary"
        >
            <i class="ri-refresh-line"></i>
            Réinitialiser les filtres
        </a>

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