<?php

/* Kopieer dit bestand voor een nieuwe pagina.
 *
 * 1. Maak een map met de naam van je pagina en zet dit bestand daarin neer
 *    als index.php. Dus grades/index.php, niet grades.php. Dan is de URL
 *    /grades/ en heb je geen rewrite-regels nodig.
 *
 * 2. Zet $page op de sleutel van je pagina in includes/data/pages.php, en zet die
 *    pagina daar ook in de lijst. Meer variabelen zijn er niet: het menu
 *    licht 'm op, en de browsertab pakt het label uit data/pages.php. Zo schrijf je
 *    'Cijfers' met hoofdletter op precies één plek op.
 *
 * 3. Tel de ../ in de requires hieronder na. Die staan ingesteld op één map
 *    diep:
 *
 *        grades/index.php         ->  __DIR__ . '/../includes/header.php'
 *        portal/login/index.php   ->  __DIR__ . '/../../includes/header.php'
 *        index.php in de root     ->  __DIR__ . '/includes/header.php'
 *
 * De <h1> zet je zelf neer, want die staat niet altijd op dezelfde plek:
 * hieronder in .page__head boven de inhoud, of in een kaart zoals index.php
 * dat doet. Een licht thema? Dan <body class="theme-light">. */

$page = '';

?>
<!DOCTYPE html>
<html lang="nl">

<?php require __DIR__ . '/../includes/header.php'; ?>

<body>

<?php require __DIR__ . '/../includes/navbar.php'; ?>

<main class="page">

    <div class="container section">

        <div class="page__head">
            <h1>Nieuwe pagina</h1>
            <p class="lead">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
        </div>

        <div class="stack">
            <div class="card">
                <div class="card__head">
                    <h2>Lorem ipsum</h2>
                    <span class="card__meta">vandaag</span>
                </div>
                <p>
                    Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                </p>
            </div>
        </div>

    </div>

</main>

<?php require __DIR__ . '/../includes/bottom-nav.php'; ?>

<script src="<?= BASE_URL ?>js/navbar.js"></script>
<script src="<?= BASE_URL ?>js/main.js"></script>

</body>
</html>
