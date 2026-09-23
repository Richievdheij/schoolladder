<?php

declare(strict_types=1);

const SITE_NAME = 'Schoolladder';

/* Path the project is served from. On Herd that is '/'. Running it in a
   subfolder instead? Then set '/schoolladder/'. */
const BASE_URL = '/';

/* Database. Herd defaults: MySQL on 127.0.0.1, user root without a password. */
const DB_HOST = '127.0.0.1';
const DB_NAME = 'schoolladder';
const DB_USER = 'root';
const DB_PASS = '';

/* Set to false before handing the project in or putting it online. */
const DEBUG = true;

ini_set('display_errors', DEBUG ? '1' : '0');
error_reporting(E_ALL);

/** Escape a value before printing it in HTML. */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/**
 * Print an icon from images/icons. The SVG is put straight into the page so
 * stroke="currentColor" works: the icon takes the colour of the text next to
 * it, which is what makes the active state colour its icon along with it.
 */
function icon(string $name): string
{
    $file = __DIR__ . '/../images/icons/' . basename($name) . '.svg';

    if (!is_file($file)) {
        /* Without this a typo vanishes without a trace: no icon shows up and
           you go hunting through your CSS. Now it is in the page source. */
        return DEBUG ? '<!-- icon "' . e($name) . '" does not exist -->' : '';
    }

    return file_get_contents($file);
}

/**
 * What a card shows when it has nothing in it yet: an icon, a title and a
 * short line of text. Styling lives in .empty-state in components.css.
 */
function emptyState(string $iconName, string $title, string $text): string
{
    return '<div class="empty-state">'
        . '<span class="empty-state__icon">' . icon($iconName) . '</span>'
        . '<p class="empty-state__title">' . e($title) . '</p>'
        . '<p class="empty-state__text">' . e($text) . '</p>'
        . '</div>';
}

/** Return the database connection. Connects on first use. */
function db(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $pdo = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    }

    return $pdo;
}
