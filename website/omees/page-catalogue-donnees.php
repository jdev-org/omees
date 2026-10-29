<?php
/**
 * Template Name: Catalogue de données
 */

get_header();
?>

<section class="dataCatalogue">
        <div class="dataCatalogue__content">
            <gn-datahub text-language="de" 
                config-url="<?php echo esc_url(
                    get_theme_file_uri(
                        'assets/geonetwork/datahub-config.toml'
                    )
                ); ?>"
            ></gn-datahub>
            <!--<gn-datahub
                config-url="https://geodata.inrae.fr/geonetwork/OMEES/datahub/assets/configuration/default.toml"
            ></gn-datahub>-->
        </div>
</section>

<?php get_footer(); ?>