/* JavaScript custom du theme */
const searchToggle = document.querySelector('.search-toggle');
const searchContainer = document.querySelector('.search-container');

if (searchToggle && searchContainer) {

    searchToggle.addEventListener('click', () => {

        searchContainer.classList.toggle('is-open');

        const isOpen = searchContainer.classList.contains('is-open');

        searchToggle.setAttribute('aria-expanded', isOpen);

    });

}

const menuParents = document.querySelectorAll(
    '#menu-header > .menu-item-has-children'
);

menuParents.forEach((menuItem) => {

    const link = menuItem.querySelector(':scope > a');

    if (!link) {
        return;
    }

    link.setAttribute('aria-expanded', 'false');

    link.addEventListener('click', (event) => {

        event.preventDefault();

        const isOpen = menuItem.classList.contains('is-open');


        // Ferme les autres sous-menus
        menuParents.forEach((item) => {

            item.classList.remove('is-open');

            const itemLink = item.querySelector(':scope > a');

            if (itemLink) {
                itemLink.setAttribute('aria-expanded', 'false');
            }

        });


        // Ouvre celui sur lequel on vient de cliquer
        if (!isOpen) {

            menuItem.classList.add('is-open');

            link.setAttribute('aria-expanded', 'true');

        }

    });

});

document.addEventListener('click', (event) => {

    if (!event.target.closest('#menu-header')) {

        menuParents.forEach((menuItem) => {

            menuItem.classList.remove('is-open');

            const link = menuItem.querySelector(':scope > a');

            if (link) {
                link.setAttribute('aria-expanded', 'false');
            }

        });

    }

});


document.addEventListener('DOMContentLoaded', function () {

    const navbar = document.querySelector('.scrollNavbar');
    const subNavbar = document.querySelector('.subNavbar');

    let lastScroll = window.scrollY;

    // Calcul automatique des hauteurs
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

    // Gestion du scroll
    if (!navbar) return;

    window.addEventListener('scroll', function () {

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

/* Custom filter AJAX */

document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('#resourceFilters');
    const results = document.querySelector('#resourceResults');

    if (!form || !results) return;

    const ajaxUrl = document.querySelector('#resourceAjaxUrl')?.value;
    const nonce = document.querySelector('#resourceAjaxNonce')?.value;

    if (!ajaxUrl || !nonce) {
        console.error('URL AJAX ou nonce manquant.');
        return;
    }

    let controller;

    async function updateResources(page = 1) {
        const types = [
            ...form.querySelectorAll('input[name="types[]"]:checked'),
        ].map((input) => input.value);

        const thematiques = [
            ...form.querySelectorAll('input[name="thematiques[]"]:checked'),
        ].map((input) => input.value);

        // Mettre à jour l'URL
        const url = new URL(form.dataset.url, window.location.origin);

        if (types.length) {
            url.searchParams.set('types', types.join(','));
        }

        if (thematiques.length) {
            url.searchParams.set('thematiques', thematiques.join(','));
        }

        if (page > 1) {
            url.searchParams.set('paged', page);
        }

        // Annuler une éventuelle requête précédente
        if (controller) controller.abort();

        const currentController = new AbortController();
        controller = currentController;

        results.classList.add('is-loading');
        results.setAttribute('aria-busy', 'true');

        const data = new FormData();

        data.append('action', 'filter_resources');
        data.append('nonce', nonce);
        data.append('page', page);

        types.forEach((type) => data.append('types[]', type));
        thematiques.forEach((theme) => data.append('thematiques[]', theme));

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

            // Remplacer uniquement les cartes et la pagination
            results.innerHTML = result.data.html;

            // Conserver les filtres dans l'URL
            window.history.pushState({}, '', url.toString());

        } catch (error) {
            if (error.name !== 'AbortError') {
                console.error('Erreur de filtrage :', error);
            }
        } finally {
            if (controller === currentController) {
                results.classList.remove('is-loading');
                results.removeAttribute('aria-busy');
            }
        }
    }

    // Filtrer dès qu'une case est cochée
    form.addEventListener('change', () => {
        updateResources(1);
    });

    // Pagination AJAX
    results.addEventListener('click', (event) => {
        const button = event.target.closest('[data-page]');

        if (!button) return;

        updateResources(Number(button.dataset.page));
    });

    // Navigation précédent / suivant du navigateur
    window.addEventListener('popstate', () => {
        window.location.reload();
    });
});
