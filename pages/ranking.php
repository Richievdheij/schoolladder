<?php

$pageTitle = 'Ranking page';
require_once 'includes/config.php';
/** @var mysqli $pdo */

?>
    <!DOCTYPE html>
    <html lang="nl">

<?php require __DIR__ . '/../includes/header.php'; ?>
    <body>

    <main>

        <section id="your-position" class="section container">
            <h1>Ranglijst</h1>
            <p>Bekijk de stand in jouw klas, jaarlaag en school. Zie wat je wint of verliest per positie.</p>

            <div class="container">
                <h2>Positie</h2>
                <!--                <p>--><?php
                //                echo $position;
                //                ?><!--</p>-->
            </div>
        </section>

        <div>
            <button>Klas</button>
            <button>Jaarlaag</button>
            <button>School</button>
        </div>

        <section id="ranking" class="section container"></section>

    </main>

    <?php require __DIR__ . '/../includes/footer.php'; ?>

    </body>
    </html><?php
