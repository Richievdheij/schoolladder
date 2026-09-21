<?php

/* Mijn ladder, de homepage.
 *
 * The title sits inside the first card here, not above the content:
 * "Goedemorgen, …" is something else than the page name in the browser tab,
 * which header.php takes from data/pages.php. */

$page = 'dashboard';

?>
<!DOCTYPE html>
<html lang="nl">

<?php require __DIR__ . '/includes/header.php'; ?>

<body>

<?php require __DIR__ . '/includes/navbar.php'; ?>

<main class="page">

    <?php /* .dashboard is het blok van deze pagina. Alles wat alleen hier
             hoort krijgt die prefix en staat in css/pages/dashboard.css. */ ?>
    <div class="container section dashboard">
        <div class="stack">

            <div class="card dashboard__hero">
                <h1 class="dashboard__greeting">Goedemorgen, Rick</h1>
                <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
                    tempor incididunt ut labore et dolore magna aliqua.
                </p>
            </div>

            <div class="card">
                <div class="card__head">
                    <h2>Score-opbouw</h2>
                    <span class="card__meta">vandaag</span>
                </div>
                <p>
                    Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi
                    ut aliquip ex ea commodo consequat.
                </p>
            </div>

            <div class="card">
                <div class="card__head">
                    <h2>Lorem ipsum</h2>
                    <span class="card__meta">deze week</span>
                </div>
                <p>
                    Duis aute irure dolor in reprehenderit in voluptate velit esse cillum
                    dolore eu fugiat nulla pariatur.
                </p>
            </div>

        </div>
    </div>

</main>

<?php require __DIR__ . '/includes/bottom-nav.php'; ?>

<script src="<?= BASE_URL ?>js/navbar.js"></script>
<script src="<?= BASE_URL ?>js/main.js"></script>

</body>
</html>
