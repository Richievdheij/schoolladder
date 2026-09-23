<?php

/* The whole navigation: the bar at the top and the panel behind it. Both
 * here, because one button operates both.
 *
 * Which item goes where is not decided in this file but in data/pages.php: 'bottom'
 * drives the topbar, 'group' drives the panel. */

declare(strict_types=1);

require_once __DIR__ . '/data/pages.php';
require_once __DIR__ . '/data/user.php';

/** @var array<string, array{label: string, url: string, icon: string, group: string, bottom: bool}> $navItems */
/** @var array{name: string, initials: string, role: string, class: string} $currentUser */
/** @var string $page */

$logo = BASE_URL . 'images/schoolladder-logo-trimmed.png';

?>
<header class="topbar">
    <div class="topbar__inner">

        <a class="topbar__logo" href="<?= BASE_URL ?>">
            <img src="<?= $logo ?>" alt="Schoolladder" width="1360" height="258">
        </a>

        <?php /* Only from 900px, where the bottom nav is gone. */ ?>
        <nav class="topnav" aria-label="Hoofdpagina's">
            <ul>
                <?php foreach ($navItems as $key => $item): ?>
                    <?php if (!$item['bottom']) { continue; } ?>
                    <?php $active = $key === $page; ?>
                    <li>
                        <a class="topnav__link<?= $active ? ' is-active' : '' ?>"
                           href="<?= BASE_URL . $item['url'] ?>"
                           <?= $active ? 'aria-current="page"' : '' ?>>
                            <?= e($item['label']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <button class="topbar__menu" type="button"
                data-menu-toggle
                aria-controls="main-menu"
                aria-expanded="false">
            <?= icon('menu') ?>
            <span class="topbar__menu-label">Menu</span>
        </button>

    </div>
</header>

<div class="menu" id="main-menu">
    <div class="menu__inner">

        <div class="menu__top">
            <img class="menu__logo" src="<?= $logo ?>" alt="Schoolladder" width="1360" height="258">
            <button class="menu__close" type="button" data-menu-close>
                <?= icon('x') ?>
                <span class="visually-hidden">Menu sluiten</span>
            </button>
        </div>

        <?php
        /* Role and class can each be missing on their own, so the line is
           built from whatever is there instead of "Leerling · klas " with a
           gap behind it. */
        $profileMeta = array_filter([
            $currentUser['role'],
            $currentUser['class'] === null ? null : 'klas ' . $currentUser['class'],
        ]);
        ?>
        <div class="profile">
            <?php if ($currentUser['initials'] !== null): ?>
                <span class="avatar" aria-hidden="true"><?= e($currentUser['initials']) ?></span>
            <?php endif; ?>
            <span class="profile__text">
                <strong class="profile__name">
                    <?= e($currentUser['name'] ?? 'Niet ingelogd') ?>
                </strong>
                <span class="profile__meta">
                    <?= $profileMeta === []
                        ? 'Log in om je gegevens te zien'
                        : e(implode(' · ', $profileMeta)) ?>
                </span>
            </span>
        </div>

        <details class="menu__group" open>
            <summary class="menu__group-title">
                <span>Pagina's</span>
                <span class="menu__chevron"><?= icon('chevron-down') ?></span>
            </summary>

            <nav aria-label="Alle pagina's">
                <ul class="menu__list">
                    <?php foreach ($navItems as $key => $item): ?>
                        <?php if ($item['group'] !== 'pages') { continue; } ?>
                        <?php $active = $key === $page; ?>
                        <li class="menu__item">
                            <a class="menu__link<?= $active ? ' is-active' : '' ?>"
                               href="<?= BASE_URL . $item['url'] ?>"
                               <?= $active ? 'aria-current="page"' : '' ?>>
                                <?= icon($item['icon']) ?>
                                <span class="menu__link-label"><?= e($item['label']) ?></span>
                                <span class="menu__arrow"><?= icon('chevron-right') ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        </details>

        <div class="menu__group">
            <p class="menu__group-title">Account</p>

            <ul class="menu__list">
                <?php foreach ($navItems as $key => $item): ?>
                    <?php if ($item['group'] !== 'account') { continue; } ?>

                    <?php
                    $active = $key === $page;

                    $classes = ['menu__link'];

                    if ($key === 'logout') {
                        $classes[] = 'menu__link--danger';
                    }

                    if ($active) {
                        $classes[] = 'is-active';
                    }
                    ?>

                    <li class="menu__item">
                        <a class="<?= implode(' ', $classes) ?>"
                           href="<?= BASE_URL . $item['url'] ?>"
                           <?= $active ? 'aria-current="page"' : '' ?>>
                            <?= icon($item['icon']) ?>
                            <span class="menu__link-label"><?= e($item['label']) ?></span>
                            <span class="menu__arrow"><?= icon('chevron-right') ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

    </div>
</div>
