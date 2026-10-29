
<?php
if (!defined('ABSPATH')) {
    exit;
}

$locations = get_nav_menu_locations();
$menu_id = $locations['primary'] ?? 0;

if (!$menu_id) {
    return;
}

$items = wp_get_nav_menu_items($menu_id);

if (!$items || is_wp_error($items)) {
    return;
}

// Organiser les éléments par parent
$children = [];

foreach ($items as $item) {
    $parent_id = (int) $item->menu_item_parent;
    $children[$parent_id][] = $item;
}

// Déterminer si un lien correspond à la page actuelle
$is_current = function ($item) {
    return in_array('current-menu-item', $item->classes, true)
        || in_array('current-menu-ancestor', $item->classes, true)
        || in_array('current-menu-parent', $item->classes, true);
};
?>

<ul class="navbar-nav omeesMenu">

    <?php foreach ($children[0] ?? [] as $level1) : ?>

        <?php
        $level2_items = $children[$level1->ID] ?? [];
        $has_children = !empty($level2_items);
        $submenu_id = 'omees-submenu-' . $level1->ID;
        ?>

        <li class="nav-item omeesMenu__item <?php echo $has_children ? 'omeesMenu__item--parent' : ''; ?>">

            <div class="omeesMenu__itemHeader">

                <?php if ($has_children) : ?>

                    <span class="nav-link omeesMenu__link is-disabled d-lg-none">
                        <?php echo esc_html($level1->title); ?>
                    </span>

                    <button
                        type="button"
                        class="nav-link omeesMenu__link omeesMenu__desktopToggle d-none d-lg-block"
                        aria-expanded="false"
                        aria-controls="<?php echo esc_attr($submenu_id); ?>"
                    >
                        <?php echo esc_html($level1->title); ?>
                    </button>

                <?php else : ?>

                    <a
                        class="nav-link omeesMenu__link <?php echo $is_current($level1) ? 'active' : ''; ?>"
                        href="<?php echo esc_url($level1->url); ?>"
                    >
                        <?php echo esc_html($level1->title); ?>
                    </a>

                <?php endif; ?>

                <?php if ($has_children) : ?>

                    <button
                        type="button"
                        class="omeesMenu__toggle"
                        aria-expanded="false"
                        aria-controls="<?php echo esc_attr($submenu_id); ?>"
                        aria-label="<?php echo esc_attr('Afficher le sous-menu : ' . $level1->title); ?>"
                    >
                        <i class="ri-arrow-down-s-line" aria-hidden="true"></i>
                    </button>

                <?php endif; ?>

            </div>

            <?php if ($has_children) : ?>

                <div
                    class="omeesMenu__mega"
                    id="<?php echo esc_attr($submenu_id); ?>"
                >

                    <div class="container-xxl">

                        <div class="omeesMenu__grid">

                            <?php foreach ($level2_items as $level2) : ?>

                                <?php
                                $level3_items = $children[$level2->ID] ?? [];
                                $has_level3 = !empty($level3_items);
                                $group_id = 'omees-group-' . $level2->ID;
                                ?>

                                <div class="omeesMenu__group <?php echo esc_attr(implode(' ', array_filter($level2->classes))); ?>">

                                    <div class="omeesMenu__groupHeader">

                                        <a
                                            href="<?php echo $has_level3 ? '#' : esc_url($level2->url); ?>"
                                            class="omeesMenu__groupTitle <?php echo $has_level3 ? 'is-disabled' : 'is-simple'; ?>"
                                            <?php if ($has_level3) : ?>
                                                tabindex="-1"
                                                aria-disabled="true"
                                            <?php endif; ?>
                                        >
                                            <?php echo esc_html($level2->title); ?>
                                        </a>

                                        <?php if ($has_level3) : ?>

                                            <button
                                                type="button"
                                                class="omeesMenu__groupToggle"
                                                aria-expanded="false"
                                                aria-controls="<?php echo esc_attr($group_id); ?>"
                                                aria-label="<?php echo esc_attr('Afficher les liens : ' . $level2->title); ?>"
                                            >
                                                <i class="ri-arrow-down-s-line" aria-hidden="true"></i>
                                            </button>

                                        <?php endif; ?>

                                    </div>

                                    <?php if ($has_level3) : ?>

                                        <ul
                                            class="omeesMenu__groupLinks"
                                            id="<?php echo esc_attr($group_id); ?>"
                                        >

                                            <?php foreach ($level3_items as $level3) : ?>

                                                <li>
                                                    <a
                                                        href="<?php echo esc_url($level3->url); ?>"
                                                        class="<?php echo $is_current($level3) ? 'active' : ''; ?>"
                                                    >
                                                        <?php echo esc_html($level3->title); ?>
                                                    </a>
                                                </li>

                                            <?php endforeach; ?>

                                        </ul>

                                    <?php endif; ?>

                                </div>

                            <?php endforeach; ?>

                        </div>

                    </div>

                </div>

            <?php endif; ?>

        </li>

    <?php endforeach; ?>

</ul>
