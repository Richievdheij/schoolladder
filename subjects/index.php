
<!DOCTYPE html>
<html lang="nl">
<?php require __DIR__ . '/../includes/header.php'; ?>
<body>
<?php require __DIR__ . '/../includes/navbar.php'; ?>

<main class="page">
    <div class="container section subjects">
        <div class="page__head">
            <h1>Vakken</h1>
            <p class="lead">Bekijk per vak hoe je ervoor staat, wie je docent is en wanneer je volgende toets gepland staat.</p>
        </div>

        <div class="card hero-card">
            <div class="card__head">
                <h2>Overzicht</h2>
                <div class="overview-pills">
                    <span class="pill-shapes on-track"><?= $aantalOpSchema; ?> Op schema</span>
                    <span class="pill-shapes attention"><?= $aantalAandacht; ?> Aandacht</span>
                </div>
            </div>
            <p>
                <?php if ($aantalVakken === 0) : ?>
                    Log in om je vakken te zien.
                <?php elseif ($aantalAandacht === 0) : ?>
                    Je staat er goed voor. Geen vak vraagt op dit moment extra aandacht.
                <?php else : ?>
                    Let op, <?= $aantalAandacht; ?> van je vakken staat onder de 5,5.
                <?php endif; ?>
            </p>
        </div>

        <div class="stack">
            <div class="tiles">
                <div class="card">
                    <span class="card__meta">Vakken</span>
                    <strong class="subject-amount"><?= $aantalVakken; ?></strong>
                    <span class="card__meta">actief dit blok</span>
                </div>
                <div class="card">
                    <span class="card__meta">Eerstvolgende toets</span>
                    <?php if ($eerstvolgendeToets !== null) : ?>
                        <strong><?= e($eerstvolgendeToets['name']); ?></strong>
                        <span class="card__meta"><?= e($eerstvolgendeToets['volgende_toets']); ?></span>
                    <?php else : ?>
                        <strong>Niets gepland</strong>
                        <span class="card__meta">geen toetsen gepland</span>
                    <?php endif; ?>
                </div>
            </div>

            <?php foreach ($vakken as $vak) : ?>
                <div class="card">
                    <div class="card__head">
                        <h2><?= e($vak['name']); ?></h2>
                        <span class="status-label <?= $vak['status']['class']; ?>"><?= $vak['status']['tekst']; ?></span>
                    </div>

                    <p class="card__meta">
                        <?php if ($vak['docent'] !== null) : ?>
                            <?= e($vak['docent']); ?> &middot; Lokaal <?= e($vak['lokaal']); ?>
                        <?php else : ?>
                            Docent en lokaal nog niet bekend
                        <?php endif; ?>
                    </p>

                    <p><?= e($vak['status']['advies']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</main>

<?php require __DIR__ . '/../includes/bottom-nav.php'; ?>

<script src="<?= BASE_URL ?>js/navbar.js"></script>
<script src="<?= BASE_URL ?>js/main.js"></script>

</body>
</html>
