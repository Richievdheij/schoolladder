<?php

/* Which pages the site has. Pure data: no HTML, no output.
 *
 * Three files read this list — header.php for the title, navbar.php for the
 * topbar and the menu panel, bottom-nav.php for the bar at the bottom. That
 * is exactly why the list lives here and not inside navbar.php: the bottom
 * nav would otherwise have to include the whole navbar to get at it, and so
 * render the topbar a second time.
 *
 * Adding a page is one line below. */

declare(strict_types=1);

require_once __DIR__ . '/../config.php';

/* The KEY is the page's name in code: lower case, English. Every page sets
   $page to its own key, and both the active state and the browser tab title
   hang off it. Deliberately not matched on the URL, because that breaks as
   soon as a querystring shows up or the project moves.

   label   what the visitor reads. The capitals live here
   url     relative to BASE_URL. Every page is a folder with an index.php
           inside, so it ends in a slash. '' is the homepage
   icon    the file in images/icons, without .svg
   group   which block of the menu panel it lands in
   bottom  in the bottom nav (max 5) and in the topbar on desktop
   accent  the highlighted circle in the middle of the bottom nav. Exactly
           one item, and it has to be the third of the five with 'bottom',
           otherwise that circle sits off-centre

   The order below is the order everywhere: topbar, menu panel and bottom
   nav. */
$navItems = [
    'dashboard' => [
        'label'  => 'Ladder',
        'url'    => '',
        'icon'   => 'trending-up',
        'group'  => 'pages',
        'bottom' => true,
    ],
    'grades' => [
        'label'  => 'Cijfers',
        'url'    => 'grades/',
        'icon'   => 'file-text',
        'group'  => 'pages',
        'bottom' => true,
    ],
    'ranking' => [
        'label'  => 'Ranglijst',
        'url'    => 'ranking/',
        'icon'   => 'trophy',
        'group'  => 'pages',
        'bottom' => true,
        'accent' => true,
    ],
    'schedule' => [
        'label'  => 'Rooster',
        'url'    => 'schedule/',
        'icon'   => 'calendar',
        'group'  => 'pages',
        'bottom' => true,
    ],
    'subjects' => [
        'label'  => 'Vakken',
        'url'    => 'subjects/',
        'icon'   => 'book-open',
        'group'  => 'pages',
        'bottom' => true,
    ],
    'attendance' => [
        'label'  => 'Aanwezigheid',
        'url'    => 'attendance/',
        'icon'   => 'user-check',
        'group'  => 'pages',
        'bottom' => false,
    ],
    'settings' => [
        'label'  => 'Instellingen',
        'url'    => 'portal/settings/',
        'icon'   => 'settings',
        'group'  => 'account',
        'bottom' => false,
    ],
    'logout' => [
        'label'  => 'Uitloggen',
        'url'    => 'portal/logout/',
        'icon'   => 'log-out',
        'group'  => 'account',
        'bottom' => false,
    ],
];

/* A page sets $page before it requires anything. This catches the one that
   forgets, so the menu simply marks nothing as active instead of warning. */
$page ??= '';
