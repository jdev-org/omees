<?php
// La page 404 conserve sa fin de document sans pied de page visible.
if (is_404()) {
    ?>
</main>
<?php wp_footer(); ?>
</body>
</html>
    <?php
    return;
}

$options = get_option('omees_theme_options', []);

$footer_logo   = wp_get_attachment_url($options['footer_logo'] ?? 0);
$footer_slogan = $options['footer_slogan'] ?? '';
$partners      = $options['partners'] ?? [];
$email    = $options['contact_email'] ?? '';
$linkedin = $options['linkedin_url'] ?? '';
?>

<div
    class="modal fade exportPdfModal no-print"
    id="exportPdfModal"
    tabindex="-1"
    aria-labelledby="exportPdfModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <div class="exportPdfModal__heading">
                    <div class="exportPdfModal__icon">
                        <i class="ri-file-pdf-2-line" aria-hidden="true"></i>
                    </div>

                    <div>
                        <h2 class="modal-title" id="exportPdfModalLabel">
                            Enregistrer en PDF
                        </h2>
                    </div>
                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Fermer"
                ></button>
            </div>

            <div class="modal-body">
                <h3 class="exportPdfModal__subtitle">
                    Comment faire ?
                </h3>

                <ol class="exportPdfModal__steps">
                    <li>
                        <strong>Ouvrez l'impression</strong>
                        <span>Cliquez sur le bouton ci-dessous.</span>
                    </li>

                    <li>
                        <strong>Choisissez le format PDF</strong>
                        <span>
                            Dans « Destination » ou « Imprimante »,
                            sélectionnez « Enregistrer au format PDF ».
                        </span>
                    </li>

                    <li>
                        <strong>Enregistrez le document</strong>
                        <span>Choisissez où conserver votre fichier.</span>
                    </li>
                </ol>
            </div>

            <div class="modal-footer">
                <button
                    type="button"
                    class="btn btn-outline-secondary"
                    data-bs-dismiss="modal"
                >
                    Annuler
                </button>

                <button
                    type="button"
                    class="btn btn-primary"
                    id="confirmExportPdf"
                >
                    <i class="ri-printer-line" aria-hidden="true"></i>
                    Ouvrir l'impression
                </button>
            </div>

        </div>
    </div>
</div>

</main>
    <footer class="site-footer mt-5" id="omees__footer">
        <div class="bg-primary text-white" style="background-color: #133e5f !important;">
            <?php if (!empty($partners)) : ?>

    <div class="partners container-xxl">

        <?php foreach ($partners as $partner) : ?>

            <?php
            $logo = $partner['logo'] ?? 0;
            $url  = $partner['url'] ?? '';

            if (!$logo) {
                continue;
            }
            ?>

            <div class="partner">

                <?php if ($url) : ?>

                    <a
                        href="<?= esc_url($url); ?>"
                        target="_blank"
                        rel="noopener noreferrer"
                    >

                <?php endif; ?>


                <?= wp_get_attachment_image(
                    $logo,
                    'medium',
                    false,
                    [
                        'loading' => 'lazy',
                    ]
                ); ?>


                <?php if ($url) : ?>

                    </a>

                <?php endif; ?>

            </div>

        <?php endforeach; ?>

    </div>

<?php endif; ?>
        </div>
        <div class="text-white py-4" style="background-color: #0f324d !important;">
            <div class="container-xxl">
                <div class="row mt-4 mb-5">
                    <div class="col-md-3">
                        <?php if ($footer_logo) : ?>
                        <img
                            src="<?= esc_url($footer_logo); ?>"
                            alt="Logo OMEES"
                            height="80"
                        />
                    <?php endif; ?>

                    <?php if ($footer_slogan) : ?>
                        <h5 class="mt-4 mb-5">
                            <?= esc_html($footer_slogan); ?>
                        </h5>
                    <?php endif; ?>                 
                    </div>
                    <div class="col-md-2 offset-md-3">
                        <?php
                        wp_nav_menu([
                            'menu'       => 'Footer - col 1',
                            'container'  => false,
                            'menu_class' => 'footer-menu',
                        ]);
                        ?>
                    </div>
                    <div class="col-md-2">
                        <?php
                        wp_nav_menu([
                            'menu'       => 'Footer - col 2',
                            'container'  => false,
                            'menu_class' => 'footer-menu',
                        ]);
                        ?>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <?php
                            wp_nav_menu([
                                'menu'       => 'Footer - col 3',
                                'container'  => false,
                                'menu_class' => 'footer-menu',
                            ]);
                            ?>
                        </div>
                        <div>
                            <?php if ($email || $linkedin) : ?>

    <div class="footer-contact mt-4">

        <p class="footerMenu__title">Nous contacter</p>

        <div class="footer-contact-links">
            

            <?php if ($email) : ?>
                <a
                    href="mailto:<?= esc_attr(antispambot($email)); ?>"
                    aria-label="Nous contacter par email"
                    class="hover-zoom"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M3 3H21C21.5523 3 22 3.44772 22 4V20C22 20.5523 21.5523 21 21 21H3C2.44772 21 2 20.5523 2 20V4C2 3.44772 2.44772 3 3 3ZM20 7.23792L12.0718 14.338L4 7.21594V19H20V7.23792ZM4.51146 5L12.0619 11.662L19.501 5H4.51146Z"></path></svg>
                </a>
            <?php endif; ?>

            <?php if ($linkedin) : ?>
                <a
                    href="<?= esc_url($linkedin); ?>"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="LinkedIn"
                    class="hover-zoom"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M6.94048 4.99993C6.94011 5.81424 6.44608 6.54702 5.69134 6.85273C4.9366 7.15845 4.07187 6.97605 3.5049 6.39155C2.93793 5.80704 2.78195 4.93715 3.1105 4.19207C3.43906 3.44699 4.18654 2.9755 5.00048 2.99993C6.08155 3.03238 6.94097 3.91837 6.94048 4.99993ZM7.00048 8.47993H3.00048V20.9999H7.00048V8.47993ZM13.3205 8.47993H9.34048V20.9999H13.2805V14.4299C13.2805 10.7699 18.0505 10.4299 18.0505 14.4299V20.9999H22.0005V13.0699C22.0005 6.89993 14.9405 7.12993 13.2805 10.1599L13.3205 8.47993Z"></path></svg>
                </a>
            <?php endif; ?>

        </div>

    </div>

<?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="footer-menuBottom">    
                    <?php
                        wp_nav_menu([
                            'menu'       => 'Footer - bas',
                            'container'  => false,
                            'menu_class' => 'footer-menu',
                        ]);
                        ?>            
                    <p class="mb-0">| &copy; <?php echo esc_html(date('Y')); ?> <?php bloginfo('name'); ?></p>
                </div>
            </div>
        </div>

    </footer>
<?php wp_footer(); ?>
</body>
</html>
