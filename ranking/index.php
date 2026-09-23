<?php

$page = 'ranking';
require_once __DIR__ . '/../includes/config.php';
/** @var mysqli $pdo */

//give students points, year_group and class_id.
//Accounts need to be made first (on other page) in order to get student names from user_id
//put the student names in #ranking in order of who has the most points

?>
    <!DOCTYPE html>
    <html lang="nl">

<?php require __DIR__ . '/../includes/header.php'; ?>
    <body>

    <?php require __DIR__ . '/../includes/navbar.php'; ?>

    <main>

        <div class="navy-space" style="background-color: var(--surface-page);">
        </div>

        <section id="your-position" class="section container"
                 style="background-color: var(--surface-1);">
            <h1>Ranglijst</h1>
            <p>Bekijk de stand in jouw klas, jaarlaag en school. Zie wat je wint of verliest per positie.</p>

            <div class="row-containers">
                <div id="second-surface" class="container" style="background-color: var(--surface-band);">
                    <p><strong>Positie</strong></p>
                    <h4>#10</h4>
                    <p>van 26 leerlingen</p>
                </div>
                <div class="container" style="background-color: var(--surface-band);">
                    <p><strong>Trend</strong></p>
                    <h4>+3</h4>
                    <p>Sinds gisteren</p>
                </div>
            </div>
            <div class="container" style="background-color: var(--surface-band);">
                <p><strong>Totale score</strong></p>
                <h4>950</h4>
                <p>van 26 leerlingen</p>
            </div>
        </section>

        <div class="navy-space" style="background-color: var(--surface-page);">
        </div>

        <section id="ranking-filters" class="">
            <button class="filter-button" style="background-color: var(--brand-violet)">Klas</button>
            <button class="filter-button" style="background-color: var(--brand-violet)">Jaarlaag</button>
            <button class="filter-button" style="background-color: var(--brand-violet)">School</button>
        </section>

        <div class="navy-space" style="background-color: var(--surface-page);">
        </div>

        <section id="ranking" class="section container" style="background-color: var(--surface-2)">

            <h3>Topselectie</h3>
            <p>Klas 4A</p>

            <div class="ranking-row">
                <div>
                    <h4 class="first-ranking-position">1</h4>
                </div>
                <div>
                    <p style="color: var(--white);"><strong>Emma</strong></p>
                    <p>1310 punten</p>
                </div>
                <div class="growth">+1</div>
            </div>

            <div class="ranking-row">
                <div>
                    <h4 class="other-ranking-position">2</h4>
                </div>
                <div>
                    <p style="color: var(--white);"><strong>Bas</strong></p>
                    <p>1280 punten</p>
                </div>
                <div class="growth">-1</div>
            </div>

            <div class="ranking-row">
                <div>
                    <h4 class="other-ranking-position">3</h4>
                </div>
                <div>
                    <p style="color: var(--white);"><strong>John</strong></p>
                    <p>1200 punten</p>
                </div>
                <div class="growth">+2</div>
            </div>

        </section>

    </main>

    <?php require __DIR__ . '/../includes/bottom-nav.php'; ?>
    <script src="<?= BASE_URL ?>js/navbar.js"></script>
    <script src="<?= BASE_URL ?>js/ranking.js"></script>
    <script src="<?= BASE_URL ?>js/main.js"></script>

    </body>
    </html>
