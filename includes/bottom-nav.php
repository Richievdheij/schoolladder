<?php

/* The bar along the bottom of the screen, below 900px. Still empty: this file
 * is where that component goes.
 *
 * The five items are waiting in includes/data/pages.php. Reading them looks like
 * this:
 *
 *     <nav class="bottom-nav" aria-label="Snelnavigatie">
 *         <?php foreach ($navItems as $key => $item): ?>
 *             <?php if (!$item['bottom']) { continue; } ?>
 *             <?php $active = $key === $page; ?>
 *             <a class="bottom-nav__item<?= $active ? ' is-active' : '' ?>"
 *                href="<?= BASE_URL . $item['url'] ?>"
 *                <?= $active ? 'aria-current="page"' : '' ?>>
 *                 <?= icon($item['icon']) ?>
 *                 <span><?= e($item['label']) ?></span>
 *             </a>
 *         <?php endforeach; ?>
 *     </nav>
 *
 * Two things to keep in mind:
 *
 *   - Give the bar the height from --bottom-nav-height. <main> keeps exactly
 *     that much room free at the bottom, so text ends up behind it otherwise.
 *   - Hide it from 900px up. The same five links are already in the topbar
 *     there. */

declare(strict_types=1);
