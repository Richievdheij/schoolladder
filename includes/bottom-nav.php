<?php

/* The bar along the bottom of the screen, below 900px. It shows the five
 * items with 'bottom' => true from data/pages.php; from 900px the same five
 * sit in the topbar and this bar disappears.
 *
 * The label is only on the current page. Its icon lifts and makes room for it
 * underneath, so the bar does not jump around while you navigate.
 *
 * One item carries 'accent' and gets the raised circle in the middle. That is
 * only a looks thing: it still marks itself active like any other item. */

declare(strict_types=1);

require_once __DIR__ . '/data/pages.php';

/** @var array<string, array{label: string, url: string, icon: string, group: string, bottom: bool}> $navItems */
/** @var string $page */

?>
<nav class="bottom-nav" aria-label="Snelnavigatie">

    <?php foreach ($navItems as $key => $item): ?>
        <?php if (!$item['bottom']) { continue; } ?>

        <?php
        $active = $key === $page;

        $classes = ['bottom-nav__item'];

        if (!empty($item['accent'])) {
            $classes[] = 'bottom-nav__item--accent';
        }

        if ($active) {
            $classes[] = 'is-active';
        }
        ?>

        <?php /* aria-label, because the visible label only shows on the
                 current page and would leave the other links nameless. */ ?>
        <a class="<?= implode(' ', $classes) ?>"
           href="<?= BASE_URL . $item['url'] ?>"
           aria-label="<?= e($item['label']) ?>"
           <?= $active ? 'aria-current="page"' : '' ?>>

            <span class="bottom-nav__icon-wrap">
                <?= icon($item['icon']) ?>
                <span class="bottom-nav__label"><?= e($item['label']) ?></span>
            </span>
        </a>

    <?php endforeach; ?>

</nav>
