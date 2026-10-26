<?php
$options = get_field('options_du_lien_utile');

$url = $options['url_de_redirection'] ?? '';

?>

<div class="row">
<div class="col-md-8 offset-md-2">
<?php if (!empty($url)) : ?>
    <a
        href="<?php echo esc_url($url); ?>"
        class="externalCard mb-5"
        target="_blank"
        rel="noopener noreferrer"
        aria-label="Explorer la carte (nouvel onglet)"
    >

        <div class="externalCard__image">
            <?php if (has_post_thumbnail()) : ?>

                <?php the_post_thumbnail('medium_large', [
                    'class' => 'img-fluid',
                ]); ?>

            <?php else : ?>

                <img
                    src="<?php echo esc_url(
                        get_template_directory_uri() . '/assets/img/default_map.png'
                    ); ?>"
                    alt=""
                    class="img-fluid"
                >

            <?php endif; ?>
        </div>

        <div class="externalCard__content">

            <div class="externalCard__text">
                <h3>
                    Consulter la ressource externe
                </h3>

                <p>S'ouvre dans un nouvel onglet</p>
                <span class="externalCard__printUrl">
                    <?php echo esc_html(
                        preg_replace('#^https?://#', '', $url)
                    ); ?>
                </span>
            </div>

            <span class="externalCard__icon" aria-hidden="true">
                <i class="ri-arrow-right-up-line"></i>
            </span>

        </div>

    </a>

    <?php endif; ?>
                <?php if (trim(get_the_content()) !== '') : ?>

        <h3 class="mb-3">À propos de cette ressource</h3>

        <?php the_content(); ?>

    <?php endif; ?>
    <?php
        get_template_part('template-parts/components/keywords');
    ?>
    </div>                
</div>