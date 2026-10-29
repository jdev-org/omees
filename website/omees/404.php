<?php get_header(); ?>

<div id="main-content" class="error404Page">

    <div class="container">

        <div class="error404Page__content">
            <span class="error404Page__eyebrow">
                    OMEES
                </span>
            <div class="error404Page__visual" aria-hidden="true">
                <span class="error404Page__number">404</span>
                
            </div>

            <div class="error404Page__text">

                <h1>
                    Oups, cette page est introuvable.
                </h1>

                <p>
                    Le chemin que vous cherchez n'existe plus
                    ou a changé d'adresse.
                </p>

                <div class="error404Page__actions">

                    <a
                        href="<?php echo esc_url(home_url('/')); ?>"
                        class="error404Page__button error404Page__button--primary"
                    >
                        <i class="ri-arrow-left-line" aria-hidden="true"></i>
                        Retour à l'accueil
                    </a>

                    <a
                        href="<?php echo esc_url(home_url('/mediatheque/')); ?>"
                        class="error404Page__button error404Page__button--outline"
                    >
                        Explorer les ressources
                        <i class="ri-arrow-right-up-line" aria-hidden="true"></i>
                    </a>

                </div>

            </div>

            <div class="error404Page__search">

                <p>Vous recherchez quelque chose en particulier ?</p>

                <form
                    role="search"
                    method="get"
                    action="<?php echo esc_url(home_url('/')); ?>"
                    class="error404Page__searchForm"
                >
                    <label class="visually-hidden" for="error404-search">
                        Rechercher sur le site
                    </label>

                    <input
                        id="error404-search"
                        type="search"
                        name="s"
                        placeholder="Rechercher un contenu..."
                        required
                    >

                    <button type="submit" aria-label="Lancer la recherche">
                        <i class="ri-search-line" aria-hidden="true"></i>
                    </button>
                </form>

            </div>

        </div>

    </div>

</div>

<?php get_footer(); ?>
