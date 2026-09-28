<?php

require_once __DIR__ . '/../includes/data/user.php';

$page = 'subjects';

$db = db();

// Functie die op basis van het gemiddelde cijfer van een leerling het statuslabel, de kleur en het advies per vak bepaalt
function maakStatus($gemiddelde, $vaknaam)
{
    // Als er nog geen cijfers zijn voor dit vak
    if ($gemiddelde === null) {
        return ['tekst' => 'Geen cijfers', 'class' => 'no-grades', 'advies' => 'Je hebt nog geen cijfers voor ' . $vaknaam . '.'];
    }
    // Als gemiddelde cijfer lager dan 5,5 is
    if ($gemiddelde < 5.5) {
        return ['tekst' => 'Aandacht', 'class' => 'attention', 'advies' => 'Je loopt achter met ' . $vaknaam . '. Ga door met oefenen!'];
    }
    // Als gemiddelde cijfer 5,5 of hoger is
    return ['tekst' => 'Op schema', 'class' => 'on-track', 'advies' => 'Je bent goed op weg met ' . $vaknaam . '. Blijf zo doorgaan!'];
}

// Zoekt leerlinmg en klas van ingelogde user op
$studentId = null;
$klasId = null;

if ($currentUser['id'] !== null) {
    $statement = $db->prepare('SELECT id, class_id FROM students WHERE user_id = ?');
    $statement->execute([$currentUser['id']]);
    $leerling = $statement->fetch();

    if ($leerling !== false) {
        $studentId = $leerling['id'];
        $klasId = $leerling['class_id'];
    }
}
// Haalt alle vakken van de leerling op (alfabetische volgorde)
$vakken = [];

if ($studentId !== null) {
    $statement = $db->prepare('
        SELECT subjects.id, subjects.name
        FROM student_subjects
        JOIN subjects ON subjects.id = student_subjects.subject_id
        WHERE student_subjects.student_id = ?
        ORDER BY subjects.name
    ');
    $statement->execute([$studentId]);
    $vakken = $statement->fetchAll();
}

// Gemiddelde cijfer van de leerling voor een vak
$gemiddeldeQuery = $db->prepare('SELECT AVG(grade) FROM grades WHERE student_id = ? AND subject_id = ?');

// Docent en het lokaal van de laatste les van een vak
$docentQuery = $db->prepare('
    SELECT users.name AS docent, events.location AS lokaal
    FROM events
    JOIN users ON users.id = events.teacher_id
    WHERE events.subject_id = ? AND events.class_id = ?
    ORDER BY events.start_time DESC
    LIMIT 1
');

// Volgende toets van een vak
$toetsQuery = $db->prepare('
    SELECT events.start_time
    FROM events
    JOIN tests ON tests.event_id = events.id
    WHERE events.subject_id = ? AND events.class_id = ? AND events.start_time >= NOW()
    ORDER BY events.start_time
    LIMIT 1
');

// Vult elk vak aan met de docent, het lokaal, de volgende toets en de status
foreach ($vakken as $nummer => $vak) {
    $gemiddeldeQuery->execute([$studentId, $vak['id']]);
    $gemiddelde = $gemiddeldeQuery->fetchColumn();

    if ($gemiddelde !== null) {
        $gemiddelde = round((float)$gemiddelde, 1);
    }

    $docentQuery->execute([$vak['id'], $klasId]);
    $les = $docentQuery->fetch();
    $vakken[$nummer]['docent'] = $les ? $les['docent'] : null;
    $vakken[$nummer]['lokaal'] = $les ? $les['lokaal'] : null;

    $toetsQuery->execute([$vak['id'], $klasId]);
    $toets = $toetsQuery->fetchColumn();
    $vakken[$nummer]['volgende_toets'] = $toets === false ? null : $toets;

    $vakken[$nummer]['status'] = maakStatus($gemiddelde, $vak['name']);
}

// Overzicht bovenaan de pagina
$aantalVakken = count($vakken);
$aantalOpSchema = 0;
$aantalAandacht = 0;
$eerstvolgendeToets = null;

// Telt hoeveel vakken op schema staan en hoeveel vakken er nog aandacht nodig hebben
foreach ($vakken as $vak) {
    if ($vak['status']['class'] === 'attention') {
        $aantalAandacht++;
    } elseif ($vak['status']['class'] === 'on-track') {
        $aantalOpSchema++;
    }
}

// Zoekt het vak waarvan de volgende toets het eerst is
foreach ($vakken as $vak) {
    // Slaat vak over als geen toets is gepland
    if ($vak['volgende_toets'] === null) {
        continue;
    }

    if ($eerstvolgendeToets === null) {
        $eerstvolgendeToets = $vak;
    } elseif ($vak['volgende_toets'] < $eerstvolgendeToets['volgende_toets']) {
        $eerstvolgendeToets = $vak;
    }
}

?>

<!DOCTYPE html>
<html lang="nl">
<?php require __DIR__ . '/../includes/header.php'; ?>
<body>
<?php require __DIR__ . '/../includes/navbar.php'; ?>

<main class="page">
    <div class="container section subjects">
        <div class="page__head">
            <h1>Vakken</h1>
            <p class="lead">Bekijk per vak hoe je ervoor staat, wie je docent is en wanneer je volgende toets gepland
                staat.</p>
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
