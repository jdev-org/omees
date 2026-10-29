
/* Aller au contenu : conserver la navigation native vers l'ancre. */
document.addEventListener('DOMContentLoaded', function initOmeesSkipLink() {
    const skipLink = document.querySelector('.skip-link');
    const content = document.querySelector('#omees-main-content');

    if (!skipLink || !content) return;

    skipLink.addEventListener('click', () => {
        content.focus({ preventScroll: true });
    });
});

/* =========================================
   RECHERCHE
========================================= */

document.addEventListener('DOMContentLoaded', function initOmeesSearch() {
    const searchToggle = document.querySelector('.search-toggle');
    const searchPanel = document.querySelector('#searchContainer');

    if (!searchToggle || !searchPanel || !window.bootstrap?.Collapse) return;

    const backgroundStates = new Map();
    let state = 'closed';
    let closeAfterOpen = false;

    function focusableElements() {
        return [...searchPanel.querySelectorAll(
            'a[href], button, input, select, textarea, [tabindex], [contenteditable="true"]'
        )].filter((element) => !element.disabled && element.tabIndex >= 0
            && !element.closest('[inert]') && element.getClientRects().length > 0);
    }

    function focusSearch() {
        const elements = focusableElements();
        const input = elements.find((element) => element.matches('input[type="search"]'));
        (input || elements[0] || searchPanel).focus({ preventScroll: true });
    }

    function isolateBackground() {
        // Isoler les branches voisines, sans rendre le header parent inerte.
        for (let branch = searchPanel; branch.parentElement; branch = branch.parentElement) {
            for (const sibling of branch.parentElement.children) {
                if (sibling === branch || sibling.matches('script, style, link, meta, template')) continue;
                backgroundStates.set(sibling, sibling.inert);
                sibling.inert = true;
            }

            if (branch.parentElement === document.body) break;
        }
    }

    function closeSearch() {
        // Collapse ignore hide() pendant sa transition d'ouverture.
        if (state === 'opening') {
            closeAfterOpen = true;
        } else if (state === 'open') {
            bootstrap.Collapse.getOrCreateInstance(searchPanel, { toggle: false }).hide();
        }
    }

    searchPanel.addEventListener('click', (event) => {
        // Le bouton reste utilisable pendant l'ouverture animée de Collapse.
        if (state !== 'opening' || !event.target.closest('.search-close')) return;
        event.preventDefault();
        event.stopPropagation();
        closeSearch();
    });

    searchPanel.addEventListener('show.bs.collapse', (event) => {
        if (event.target !== searchPanel || event.defaultPrevented) return;
        state = 'opening';
        closeAfterOpen = false;
        searchPanel.removeAttribute('aria-hidden');
    });

    searchPanel.addEventListener('shown.bs.collapse', (event) => {
        if (event.target !== searchPanel) return;
        state = 'open';
        searchToggle.setAttribute('aria-expanded', 'true');

        if (closeAfterOpen) {
            closeSearch();
            return;
        }

        searchPanel.setAttribute('aria-modal', 'true');
        focusSearch();
        isolateBackground();
    });

    searchPanel.addEventListener('hide.bs.collapse', (event) => {
        if (event.target !== searchPanel || event.defaultPrevented) return;
        state = 'closing';
    });

    searchPanel.addEventListener('hidden.bs.collapse', (event) => {
        if (event.target !== searchPanel) return;
        state = 'closed';
        backgroundStates.forEach((wasInert, element) => { element.inert = wasInert; });
        backgroundStates.clear();
        searchPanel.removeAttribute('aria-modal');
        searchToggle.setAttribute('aria-expanded', 'false');

        searchToggle.focus({ preventScroll: true });
        searchPanel.setAttribute('aria-hidden', 'true');
    });

    document.addEventListener('keydown', (event) => {
        if (state === 'closed') return;

        if (event.key === 'Escape') {
            event.preventDefault();
            event.stopPropagation();
            closeSearch();
            return;
        }

        if (event.key !== 'Tab') return;

        if (state !== 'open') {
            event.preventDefault();
            return;
        }

        const elements = focusableElements();
        const first = elements[0];
        const last = elements[elements.length - 1];

        if (!first) {
            event.preventDefault();
            searchPanel.focus({ preventScroll: true });
        } else if (!elements.includes(document.activeElement)
            || (event.shiftKey && document.activeElement === first)
            || (!event.shiftKey && document.activeElement === last)) {
            event.preventDefault();
            (event.shiftKey ? last : first).focus({ preventScroll: true });
        }
    });

    document.addEventListener('focusin', (event) => {
        if (state === 'open' && !searchPanel.contains(event.target)) focusSearch();
    });
});


/* =========================================
   NAVBAR AU SCROLL
========================================= */

document.addEventListener('DOMContentLoaded', function initOmeesScrollNavbar() {
    // Le catalogue laisse le header dans le flux pour le sticky du Datahub.
    if (document.body.classList.contains('page-template-page-catalogue-donnees-php')) return;

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

document.addEventListener('DOMContentLoaded', function initOmeesResourceFilters() {
    const form = document.querySelector('#resourceFilters');
    const results = document.querySelector('#resourceResults');
    const count = document.querySelector('#resourceCount');

    if (!form || !results) return;

    const ajaxUrl = document.querySelector('#resourceAjaxUrl')?.value;
    const nonce = document.querySelector('#resourceAjaxNonce')?.value;

    // Type fixe de la page : carte, decryptage ou vide
    const fixedType = form.dataset.type || '';

    // En dehors des résultats remplacés par AJAX pour conserver le bouton de reprise.
    const errorBox = document.createElement('div');
    errorBox.className = 'alert alert-warning';
    errorBox.hidden = true;
    const errorMessage = document.createElement('p');
    errorMessage.setAttribute('role', 'alert');
    const retryButton = document.createElement('button');
    retryButton.type = 'button';
    retryButton.className = 'btn';
    retryButton.textContent = 'Réessayer';
    errorBox.append(errorMessage, retryButton);
    results.before(errorBox);

    function showError(message) {
        errorBox.hidden = false;
        errorMessage.textContent = message;
    }

    if (!ajaxUrl || !nonce) {
        console.error('URL AJAX ou nonce manquant.');
        retryButton.hidden = true;
        showError('Le filtrage est indisponible. Rechargez la page pour réessayer.');
        return;
    }

    let controller = null;
    let lastPage = 1;

    retryButton.addEventListener('click', () => updateResources(lastPage));

    async function updateResources(page = 1) {
        lastPage = page;
        if (errorBox.contains(document.activeElement)) {
            results.setAttribute('tabindex', '-1');
            results.focus({ preventScroll: true });
        }
        errorBox.hidden = true;
        errorMessage.textContent = '';

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

            // Une réponse ancienne ne doit jamais remplacer les résultats courants.
            if (currentController.signal.aborted || controller !== currentController) return;

            if (!result?.success || typeof result.data?.html !== 'string') {
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
            if (error.name !== 'AbortError' && controller === currentController
                && !currentController.signal.aborted) {
                console.error('Erreur de filtrage :', error);
                showError('Impossible de mettre à jour les ressources. Les derniers résultats restent affichés. Réessayez ou rechargez la page.');
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


document.addEventListener('DOMContentLoaded', function initOmeesPrimaryMenu() {

    const menu = document.querySelector('.omeesMenu');

    if (!menu) return;

    const desktop = window.matchMedia('(min-width: 992px)');
    const desktopItems = [...menu.querySelectorAll('.omeesMenu__item--parent')]
        .map((item) => ({
            item,
            trigger: item.querySelector('.omeesMenu__desktopToggle'),
            mobileTrigger: item.querySelector('.omeesMenu__toggle'),
        }))
        .filter(({ trigger }) => trigger);

    function setDesktopOpen(entry, open) {
        entry.item.classList.toggle('is-open', open);
        entry.trigger.setAttribute('aria-expanded', String(open));
        entry.mobileTrigger?.setAttribute('aria-expanded', String(open));
    }

    function openDesktopItem(entry) {
        desktopItems.forEach((other) => setDesktopOpen(other, other === entry));
    }

    desktopItems.forEach((entry) => {
        entry.trigger.addEventListener('click', () => {
            if (!desktop.matches) return;

            if (entry.item.classList.contains('is-open')) {
                setDesktopOpen(entry, false);
            } else {
                openDesktopItem(entry);
            }
        });

        entry.item.addEventListener('pointerenter', (event) => {
            if (desktop.matches && event.pointerType !== 'touch') {
                openDesktopItem(entry);
            }
        });

        entry.item.addEventListener('pointerleave', () => {
            if (desktop.matches && !entry.item.contains(document.activeElement)) {
                setDesktopOpen(entry, false);
            }
        });

        entry.item.addEventListener('focusout', (event) => {
            if (desktop.matches && !entry.item.contains(event.relatedTarget)) {
                setDesktopOpen(entry, false);
            }
        });

        entry.item.addEventListener('keydown', (event) => {
            if (!desktop.matches || event.key !== 'Escape'
                || !entry.item.classList.contains('is-open')) return;

            event.preventDefault();
            setDesktopOpen(entry, false);
            entry.trigger.focus({ preventScroll: true });
        });
    });

    // Le même état pilote l'affichage et aria-expanded sur desktop.
    menu.setAttribute('data-desktop-ready', '');

    document.addEventListener('click', (event) => {
        if (desktop.matches && !menu.contains(event.target)) {
            desktopItems.forEach((entry) => setDesktopOpen(entry, false));
        }
    });

    desktop.addEventListener('change', () => {
        const activeEntry = desktopItems.find(({ item }) => item.contains(document.activeElement));

        menu.querySelectorAll('.is-open').forEach((element) => element.classList.remove('is-open'));
        menu.querySelectorAll('[aria-expanded]').forEach((element) => {
            element.setAttribute('aria-expanded', 'false');
        });

        if (activeEntry) {
            const target = desktop.matches
                ? activeEntry.trigger
                : document.querySelector('#mainNavbar.show .omeesMenu__toggle')
                    ? activeEntry.mobileTrigger
                    : document.querySelector('#omees__btnNavMobileColl');

            target?.focus({ preventScroll: true });
        }
    });

    menu.addEventListener('click', function (event) {

        const button = event.target.closest(
            '.omeesMenu__toggle, .omeesMenu__groupToggle'
        );

        if (!button) return;

        if (desktop.matches) return;

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

document.addEventListener('DOMContentLoaded', function initOmeesHeadingAnchors() {
    const headings = document.querySelectorAll('body .omees__contentMain h2');

    headings.forEach((h2) => {
        // Générer un ID si le titre n'en possède pas
        if (!h2.id) {
            const base = h2.textContent
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-|-$/g, '') || 'section';

            window.omeesEnsureHeadingId(h2, base);
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

document.addEventListener('DOMContentLoaded', function initOmeesPrintExport() {
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
