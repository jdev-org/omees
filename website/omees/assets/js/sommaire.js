document.addEventListener('DOMContentLoaded', () => {

    const sommaire = document.querySelector('.sommaire');
    const content = document.querySelector('.pageLayout__content');

    if (!sommaire || !content) return;

    const list = sommaire.querySelector('.sommaire__list');
    if (!list) return;

    // Détection automatique des H2
    const headings = [...content.querySelectorAll('h2')]
        .filter(heading => heading.textContent.trim());

    if (!headings.length) {
        sommaire.hidden = true;
        return;
    }

    list.replaceChildren();

    // Génération automatique du sommaire
    headings.forEach((heading, index) => {

        // Ajout d'un ID unique si absent
        if (!heading.id) {
            const baseId = `section-${index + 1}`;
            let id = baseId;
            let suffix = 2;

            while (document.getElementById(id)) {
                id = `${baseId}-${suffix++}`;
            }

            heading.id = id;
        }

        // Ajout automatique de la classe
        heading.classList.add('js-sommaire-section');

        const li = document.createElement('li');
        const link = document.createElement('a');

        link.href = `#${heading.id}`;
        link.textContent = heading.textContent.trim();

        li.appendChild(link);
        list.appendChild(li);
    });

    const links = [...list.querySelectorAll('a')];

    // Gestion de la section active
    const updateActive = () => {

        let activeIndex = 0;
        const offset = 150;

        headings.forEach((heading, index) => {
            if (heading.getBoundingClientRect().top <= offset) {
                activeIndex = index;
            }
        });

        links.forEach((link, index) => {
            const active = index === activeIndex;

            link.classList.toggle('is-active', active);

            if (active) {
                link.setAttribute('aria-current', 'location');
            } else {
                link.removeAttribute('aria-current');
            }
        });
    };

    window.addEventListener('scroll', updateActive, {
        passive: true
    });

    window.addEventListener('resize', updateActive);

    updateActive();

});