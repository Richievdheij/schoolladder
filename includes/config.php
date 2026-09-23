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

/* PHP draait standaard in UTC en MySQL in de systeemtijd. Dat scheelt hier
   twee uur, en dan staat een les van 10:45 volgens de klok van de pagina nog
   te beginnen terwijl hij al voorbij is. Allebei op Amsterdam, en db() zet de
   verbinding straks op dezelfde stand. */
const TIMEZONE = 'Europe/Amsterdam';

date_default_timezone_set(TIMEZONE);

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
 * What a card shows when it has nothing to show yet.
 *
 * Every page needs this the moment its data comes out of a database instead
 * of out of a fixture, so it sits here next to icon() and not in one page's
 * stylesheet. $icon is a file from images/icons. The hint is optional: leave
 * it out when there is nothing useful to add beyond the title.
 *
 * Say what will appear here, not that something is missing. "Je eerste cijfer
 * verschijnt hier" reads as a system that is waiting; "geen data" reads as a
 * system that is broken.
 */
function emptyState(string $icon, string $title, string $hint = ''): string
{
    $html = '<div class="empty-state">'
        . '<span class="empty-state__icon" aria-hidden="true">' . icon($icon) . '</span>'
        . '<p class="empty-state__title">' . e($title) . '</p>';

    if ($hint !== '') {
        $html .= '<p class="empty-state__text">' . e($hint) . '</p>';
    }

    return $html . '</div>';
}

/**
 * How long ago something was, in plain Dutch: vandaag, gisteren, 3 dagen
 * geleden. Anything further back than a week gets a plain date, because
 * "23 dagen geleden" is harder to place than 30-08-2026.
 */
function relativeDay(?string $timestamp): string
{
    if ($timestamp === null || trim($timestamp) === '') {
        return '';
    }

    $then = new DateTimeImmutable($timestamp);

    /* Compared by day and not by the clock: something from late last night is
       "gisteren", not "20 hours ago". */
    $days = (int) (new DateTimeImmutable('today'))
        ->diff(new DateTimeImmutable($then->format('Y-m-d')))
        ->format('%r%a');

    return match (true) {
        $days === 0 => 'vandaag',
        $days === -1 => 'gisteren',
        $days === 1 => 'morgen',
        $days < -1 && $days > -8 => abs($days) . ' dagen geleden',
        default => $then->format('d-m-Y'),
    };
}

/**
 * A number the Dutch way: 1.248 and 8,4. Scores, points and grades turn up on
 * every page of this site, so the separators are settled in one place instead
 * of in each template.
 */
function num(int|float $value, int $decimals = 0): string
{
    return number_format($value, $decimals, ',', '.');
}

/**
 * A signed number: +34, −18, 0. Uses a real minus sign (U+2212) and not a
 * hyphen, because a hyphen is a fraction of the width of a plus and makes a
 * column of numbers look crooked.
 */
function delta(int|float $value, int $decimals = 0): string
{
    if ($value > 0) {
        return '+' . num($value, $decimals);
    }

    if ($value < 0) {
        return "\u{2212}" . num(abs($value), $decimals);
    }

    return num(0, $decimals);
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

        /* Zet de verbinding op dezelfde tijd als PHP. De verschuiving wordt nu
           berekend, dus in de winter komt er vanzelf +01:00 uit. */
        $pdo->exec("SET time_zone = '" . (new DateTimeImmutable())->format('P') . "'");
    }

    return $pdo;
}
