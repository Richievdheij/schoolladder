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

define("db", mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME));


/** Escape a value before printing it in HTML. */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
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
