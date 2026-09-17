<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle ?? SITE_NAME) ?></title>

    <link rel="icon" href="<?= BASE_URL ?>images/schoolladder-favicon.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&family=Inter:wght@400;600&family=JetBrains+Mono:wght@400&display=swap">

    <link rel="stylesheet" href="<?= BASE_URL ?>css/tokens.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/base.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/components.css">
</head>
<body>
<main>
