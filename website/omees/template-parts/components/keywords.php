<?php
$etiquettes = get_the_terms(get_the_ID(), 'post_tag');

if (!empty($etiquettes) && !is_wp_error($etiquettes)) : ?>

    <hr class="mt-5">

    <h6>Les mots clés associés à cette ressource</h6>

    <div class="mt-3">
        <?php foreach ($etiquettes as $etiquette) : ?>
            <a
                class="singleBadge singleBadge__keyWord"
                href="<?php echo esc_url(
                    add_query_arg('s', $etiquette->name, home_url('/'))
                ); ?>"
            >
                <?php echo esc_html($etiquette->name); ?>
            </a>
        <?php endforeach; ?>
    </div>

<?php endif; ?>