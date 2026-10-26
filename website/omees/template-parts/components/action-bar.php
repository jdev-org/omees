<?php
$show_actions = $args['show_actions'] ?? false;
$show_share = $args['show_share'] ?? false;

$url = get_permalink();
$titre = get_the_title();

$mail_subject = rawurlencode($titre);
$mail_body = rawurlencode($titre . "\n\n" . $url);

$linkedin_url = 'https://www.linkedin.com/sharing/share-offsite/?url='
    . rawurlencode($url);

$whatsapp_url = 'https://api.whatsapp.com/send?text='
    . rawurlencode($titre . ' ' . $url);
?>


<div class="actionBar">
<?php if ($show_actions) : ?>
    <div class="actionBar__tools">

        <button type="button" class="actionBar__tool" aria-label="Imprimer" onclick="window.print()">
            <i class="ri-printer-line" aria-hidden="true"></i>
            <span>Imprimer</span>
        </button>

        <button type="button" class="actionBar__tool" aria-label="Exporter en PDF" data-bs-toggle="modal" data-bs-target="#exportPdfModal">
            <i class="ri-file-pdf-2-line" aria-hidden="true"></i>
            <span>Exporter en PDF</span>
        </button>
    </div>

<?php endif; ?>
<?php if ($show_share) : ?>

    <div class="actionBar__share">

        <a
            href="mailto:?subject=<?php echo esc_attr($mail_subject); ?>&body=<?php echo esc_attr($mail_body); ?>"
            aria-label="Partager par e-mail"
            title="Partager par e-mail"
        >
            <i class="ri-mail-line" aria-hidden="true"></i>
        </a>

        <a
            href="<?php echo esc_url($linkedin_url); ?>"
            target="_blank"
            rel="noopener noreferrer"
            aria-label="Partager sur LinkedIn"
            title="Partager sur LinkedIn"
        >
            <i class="ri-linkedin-fill" aria-hidden="true"></i>
        </a>

        <a
            href="<?php echo esc_url($whatsapp_url); ?>"
            target="_blank"
            rel="noopener noreferrer"
            aria-label="Partager sur WhatsApp"
            title="Partager sur WhatsApp"
        >
            <i class="ri-whatsapp-line" aria-hidden="true"></i>
        </a>

    </div>
<?php endif; ?>

</div>

<style>
    .actionBar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 2rem;
}

.actionBar__tools,
.actionBar__share {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 2rem;
}

.actionBar__tool {
    display: inline-flex;
    align-items: center;
    gap: 0.6rem;
    border: 0;
    padding: 0;
    background: transparent;
    color: inherit;
    font-weight: 600;
    text-transform: uppercase;
    cursor: pointer;
}


.actionBar__share {
    gap: 0.25rem;
}

.actionBar__share a {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    border: 2px solid var(--bs-primary);
    background-color: var(--bs-primary);
    color: white!important;
    border-radius: 50%;
    color: inherit;
    text-decoration: none;
    transition: background-color 0.2s ease, color 0.2s ease;
}

.actionBar__share a:hover {
    opacity: .9;
}

.actionBar__tool:hover {
    opacity: 0.7;
}

@media (max-width: 767px) {
    .actionBar {
        gap: 1.5rem;
    }

    .actionBar__tools {
        gap: 1rem;
    }

    .actionBar__tool > span {
        display: none;
    }
}

</style>
