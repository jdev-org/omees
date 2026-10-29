/* Conserver les ancres existantes et les deux conventions historiques de génération. */
window.omeesEnsureHeadingId = function omeesEnsureHeadingId(heading, base) {
    if (heading.id) return heading.id;

    let id = base;
    let suffix = 2;

    while (document.getElementById(id)) {
        id = `${base}-${suffix++}`;
    }

    heading.id = id;
    return id;
};
