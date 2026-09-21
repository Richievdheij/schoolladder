<?php

/* The <head> of every page. Opens and closes here, so the page itself keeps
 * <!DOCTYPE>, <html> and <body>.
 *
 * The page sets $page before requiring this file, which is how the title ends
 * up right. Requiring the page list here also puts e(), icon(), BASE_URL and
 * $navItems within reach of the rest of the page, so nothing else has to be
 * required at the top. */

declare(strict_types=1);

require_once __DIR__ . '/data/pages.php';

/* The title in the browser tab. If the page is in the list in data/pages.php, its
   label is the title, so 'Cijfers' is written down in exactly one place. If
   it is not, the key gets a capital and becomes readable anyway. A page
   without $page just gets the site name, without repeating it twice. */
$label = $navItems[$page]['label'] ?? ucfirst(str_replace('-', ' ', $page));
$pageTitle = $label === '' ? SITE_NAME : $label . ' · ' . SITE_NAME;

/* A page can bring its own stylesheet: css/pages/<$page>.css. It is linked
   only when that file really exists, so a page without one costs nothing and
   nobody has to create empty files. */
$pageStyle = $page === '' ? '' : 'css/pages/' . basename($page) . '.css';
$hasPageStyle = $pageStyle !== '' && is_file(dirname(__DIR__) . '/' . $pageStyle);

?>

<head>
    <meta charset="UTF-8">
    <?php /* viewport-fit=cover is what makes env(safe-area-inset-*) return
             anything but 0 on an iPhone. */ ?>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title><?= e($pageTitle) ?></title>

    <link rel="icon" href="<?= BASE_URL ?>images/schoolladder-favicon.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&family=Inter:wght@400;600&family=JetBrains+Mono:wght@400&display=swap">

    <link rel="stylesheet" href="<?= BASE_URL ?>css/tokens.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/base.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/components.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/components/navbar.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/components/bottom-nav.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/pages/portal.css">
    <?php if ($hasPageStyle): ?>
        <link rel="stylesheet" href="<?= BASE_URL . $pageStyle ?>">
    <?php endif; ?>
</head>