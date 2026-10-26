
<h1 class="text-white mb-3">Que recherchez-vous ?</h1>
<form
    role="search"
    method="get"
    class="search-form container-xl d-flex"
    action="<?php echo esc_url(home_url('/')); ?>"
>
    <input
        type="search"
        class="search-form__input w-100 my-5 "
        name="s"
        value="<?php echo esc_attr(get_search_query()); ?>"
        placeholder="Ex. : abeilles, santé, nature en ville…"
        aria-label="Rechercher"
    >
    <button
        type="submit"
        class="btn btn-outline-light btn-lg"
        aria-label="Lancer la recherche"
    >
        <i class="ri-search-line"></i> Rechercher
    </button>
</form>