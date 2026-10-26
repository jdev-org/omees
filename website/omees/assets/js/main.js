
/* =========================================
   RECHERCHE
========================================= */

const searchToggle = document.querySelector('.search-toggle');
const searchContainer = document.querySelector('.search-container');

if (searchToggle && searchContainer) {
    searchToggle.addEventListener('click', () => {
        searchContainer.classList.toggle('is-open');

        const isOpen = searchContainer.classList.contains('is-open');

        searchToggle.setAttribute('aria-expanded', String(isOpen));
    });
}


/* =========================================
   MENU PRINCIPAL
========================================= */

const menuParents = document.querySelectorAll(
    '#menu-header > .menu-item-has-children'
);

menuParents.forEach((menuItem) => {
    const link = menuItem.querySelector(':scope > a');

    if (!link) return;

    link.setAttribute('aria-expanded', 'false');

    link.addEventListener('click', (event) => {
        event.preventDefault();

        const isOpen = menuItem.classList.contains('is-open');

        // Fermer les autres sous-menus
        menuParents.forEach((item) => {
            item.classList.remove('is-open');

            const itemLink = item.querySelector(':scope > a');

            if (itemLink) {
                itemLink.setAttribute('aria-expanded', 'false');
            }
        });

        // Ouvrir le sous-menu sélectionné
        if (!isOpen) {
            menuItem.classList.add('is-open');
            link.setAttribute('aria-expanded', 'true');
        }
    });
});

// Fermer les menus au clic extérieur
document.addEventListener('click', (event) => {
    if (event.target.closest('#menu-header')) return;

    menuParents.forEach((menuItem) => {
        menuItem.classList.remove('is-open');

        const link = menuItem.querySelector(':scope > a');

        if (link) {
            link.setAttribute('aria-expanded', 'false');
        }
    });
});


/* =========================================
   NAVBAR AU SCROLL
========================================= */

document.addEventListener('DOMContentLoaded', () => {
    const navbar = document.querySelector('.scrollNavbar');
    const subNavbar = document.querySelector('.subNavbar');

    let lastScroll = window.scrollY;

    function updateNavbarHeights() {
        const navbarHeight = navbar ? navbar.offsetHeight : 0;
        const subNavbarHeight = subNavbar ? subNavbar.offsetHeight : 0;

        document.documentElement.style.setProperty(
            '--navbar-height',
            `${navbarHeight}px`
        );

        document.documentElement.style.setProperty(
            '--subnavbar-height',
            `${subNavbarHeight}px`
        );
    }

    updateNavbarHeights();

    window.addEventListener('resize', updateNavbarHeights);

    if (!navbar) return;

    window.addEventListener('scroll', () => {
        const currentScroll = window.scrollY;

        if (currentScroll <= 100) {
            navbar.classList.remove('is-hidden');
            document.body.classList.remove('navbar-hidden');

        } else if (currentScroll > lastScroll) {
            navbar.classList.add('is-hidden');
            document.body.classList.add('navbar-hidden');

        } else if (currentScroll < lastScroll) {
            navbar.classList.remove('is-hidden');
            document.body.classList.remove('navbar-hidden');
        }

        lastScroll = currentScroll;
    }, { passive: true });
});


/* =========================================
   FILTRES RESSOURCES AJAX
   Médiathèque / Cartothèque / Décryptages
========================================= */

document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('#resourceFilters');
    const results = document.querySelector('#resourceResults');
    const count = document.querySelector('#resourceCount');

    if (!form || !results) return;

    const ajaxUrl = document.querySelector('#resourceAjaxUrl')?.value;
    const nonce = document.querySelector('#resourceAjaxNonce')?.value;

    // Type fixe de la page : carte, decryptage ou vide
    const fixedType = form.dataset.type || '';

    console.log('Type fixe :', fixedType);
    
    if (!ajaxUrl || !nonce) {
        console.error('URL AJAX ou nonce manquant.');
        return;
    }

    let controller = null;

    async function updateResources(page = 1) {

        // Récupérer les filtres sélectionnés
        const types = [
            ...form.querySelectorAll('input[name="types[]"]:checked'),
        ].map((input) => input.value);

        const thematiques = [
            ...form.querySelectorAll('input[name="thematiques[]"]:checked'),
        ].map((input) => input.value);

        // Construire l'URL de la page
        const url = new URL(
            form.dataset.url,
            window.location.origin
        );

        if (types.length && !fixedType) {
            url.searchParams.set('types', types.join(','));
        }

        if (thematiques.length) {
            url.searchParams.set('thematiques', thematiques.join(','));
        }

        if (page > 1) {
            url.searchParams.set('paged', page);
        }

        // Annuler la requête précédente
        if (controller) {
            controller.abort();
        }

        const currentController = new AbortController();
        controller = currentController;

        results.classList.add('is-loading');
        results.setAttribute('aria-busy', 'true');

        // Construire les données AJAX
        const data = new FormData();

        data.append('action', 'filter_resources');
        data.append('nonce', nonce);
        data.append('page', String(page));

        // IMPORTANT : toujours envoyer le type fixe,
        // même lorsqu'aucune thématique n'est cochée.
        if (fixedType) {
            data.append('fixed_type', fixedType);
        }

        // Filtres de la médiathèque
        if (!fixedType) {
            types.forEach((type) => {
                data.append('types[]', type);
            });
        }

        // Thématiques pour toutes les pages
        thematiques.forEach((thematique) => {
            data.append('thematiques[]', thematique);
        });

        console.log('Type fixe :', fixedType);
        console.log('Données AJAX :', [...data.entries()]);

        try {
            const response = await fetch(ajaxUrl, {
                method: 'POST',
                body: data,
                signal: currentController.signal,
            });

            if (!response.ok) {
                throw new Error(`Erreur HTTP ${response.status}`);
            }

            const result = await response.json();

            if (!result.success) {
                throw new Error('Erreur AJAX WordPress');
            }

            // Remplacer les résultats et la pagination
            results.innerHTML = result.data.html;

            // Mettre à jour le compteur
            if (count) {
                const total = Number(result.data.total) || 0;

                count.textContent =
                    `${total} ressource${total > 1 ? 's' : ''} ` +
                    `trouvée${total > 1 ? 's' : ''}`;
            }

            // Mettre à jour l'URL sans rechargement
            window.history.pushState({}, '', url.toString());

        } catch (error) {
            if (error.name !== 'AbortError') {
                console.error('Erreur de filtrage :', error);
            }

        } finally {
            if (controller === currentController) {
                results.classList.remove('is-loading');
                results.removeAttribute('aria-busy');
                controller = null;
            }
        }
    }

    // Filtrage automatique au changement
    form.addEventListener('change', (event) => {
        if (!event.target.matches('input[type="checkbox"]')) {
            return;
        }

        updateResources(1);
    });

    // Pagination AJAX
    results.addEventListener('click', (event) => {
        const button = event.target.closest('[data-page]');

        if (!button || button.disabled) return;

        const page = Number(button.dataset.page);

        if (!Number.isInteger(page) || page < 1) return;

        updateResources(page);
    });

    // Réinitialisation des filtres
    form.querySelector('.resourceFilters__reset')
        ?.addEventListener('click', (event) => {
            event.preventDefault();

            form.querySelectorAll('input[type="checkbox"]')
                .forEach((checkbox) => {
                    checkbox.checked = false;
                });

            // Conserve automatiquement le type fixe
            updateResources(1);
        });

    // Bouton de réinitialisation dans l'état vide
    results.addEventListener('click', (event) => {
        const button = event.target.closest('.resourceEmpty__reset');

        if (!button) return;

        form.querySelectorAll('input[type="checkbox"]')
            .forEach((checkbox) => {
                checkbox.checked = false;
            });

        updateResources(1);
    });

    // Navigation précédent / suivant du navigateur
    window.addEventListener('popstate', () => {
        window.location.reload();
    });
});


document.addEventListener('DOMContentLoaded', function () {

    const menu = document.querySelector('.omeesMenu');

    if (!menu) return;

    menu.addEventListener('click', function (event) {

        const button = event.target.closest(
            '.omeesMenu__toggle, .omeesMenu__groupToggle'
        );

        if (!button) return;

        // Sur desktop, le menu est géré en CSS
        if (window.innerWidth >= 992) return;

        event.preventDefault();

        const isLevel1 = button.classList.contains('omeesMenu__toggle');

        const parent = button.closest(
            isLevel1 ? '.omeesMenu__item' : '.omeesMenu__group'
        );

        if (!parent) return;

        const isOpen = parent.classList.toggle('is-open');

        button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

    });

});

document.addEventListener('DOMContentLoaded', () => {
    const headings = document.querySelectorAll('body .omees__contentMain h2');
    const usedIds = new Set(
        [...document.querySelectorAll('[id]')].map(el => el.id)
    );

    headings.forEach((h2) => {
        // Générer un ID si le titre n'en possède pas
        if (!h2.id) {
            const base = h2.textContent
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-|-$/g, '') || 'section';

            let id = base;
            let index = 2;

            while (usedIds.has(id)) {
                id = `${base}-${index++}`;
            }

            h2.id = id;
            usedIds.add(id);
        }

        // Créer le bouton d'ancre
        const link = document.createElement('a');
        link.href = `#${h2.id}`;
        link.className = 'heading-anchor';
        link.setAttribute('aria-label', `Copier le lien vers ${h2.textContent.trim()}`);
        link.title = 'Copier le lien vers cette section';
        link.innerHTML = '<i class="ri-link" aria-hidden="true"></i>';

        link.addEventListener('click', async (event) => {
            event.preventDefault();

            const url = new URL(window.location.href);
            url.hash = h2.id;

            try {
                await navigator.clipboard.writeText(url.toString());
                link.title = 'Lien copié !';
            } catch {
                // Repli : naviguer vers l'ancre
                window.location.hash = h2.id;
            }
        });

        h2.appendChild(link);
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const modalElement = document.querySelector('#exportPdfModal');
    const printButton = document.querySelector('#confirmExportPdf');

    if (!modalElement || !printButton) return;

    printButton.addEventListener('click', () => {
        const modal = bootstrap.Modal.getInstance(modalElement);

        if (!modal) return;

        modalElement.addEventListener('hidden.bs.modal', () => {
            window.print();
        }, { once: true });

        modal.hide();
    });
});