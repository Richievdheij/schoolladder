<?php

$page = 'ranking';
require_once __DIR__ . '/../includes/config.php';
/** @var mysqli $pdo */

//Make the page look like the wireframe - gedaan
//Accounts need to be made first (preferably on other page, but I can do it here for now) in order to get student names from user_id - gedaan
//give students points, year_group and class_id. - gedaan
//put the student names in #ranking in order of who has the most points - gedaan
//make the filters work (with js) - moet ik nog doen, maar kom ik denk ik wel uit met eerder gebruikte code
//make the growth work based on last update - ik weet niet hoe ik dit doe...

$query = "SELECT * FROM schoolladder.users";
$result = db()->query($query);

if ($result->rowCount() == 0) {

    $query = "INSERT INTO schoolladder.users (name, email, password, role)
    VALUES
    ('Emma de Vries', 'emmadevries@hr.nl', 'password', 'student'),
    ('Bas de Boot', 'basdeboot@hr.nl', 'password', 'student'),
    ('John de Boer', 'johndeboer@hr.nl', 'password', 'student'),
    ('Lars Jansen', 'larsjansen@hr.nl', 'password', 'student'),
    ('Emily den Bosch', 'emilydenbosch@hr.nl', 'password', 'student'),
    ('Noah Bakker', 'noahbakker@hr.nl', 'password', 'student'),
    ('Anna van den Berg', 'annavandenberg@hr.nl', 'password', 'student'),
    ('Jayden Smits', 'jaydensmits@hr.nl', 'password', 'student'),
    ('Fatma Yilmaz', 'fatmayilmaz@hr.nl', 'password', 'student'),
    ('Melisa Yilmaz', 'melisayilmaz@hr.nl', 'password', 'student'),
    ('James de Groot', 'jamesdegroot@hr.nl', 'password', 'student'),
    ('Noor Vos', 'noorvos@hr.nl', 'password', 'student'),
    ('Finn Kok', 'finnkok@hr.nl', 'password', 'student'),
    ('Peter van Leeuwen', 'petervanleeuwen@hr.nl', 'password', 'student'),
    ('Leo Peters', 'leopeters@hr.nl', 'password', 'student'),
    ('Christina van Dijk', 'christinavandijk@hr.nl', 'password', 'student'),
    ('Chris van Dijk', 'chrisvandijk@hr.nl', 'password', 'student'),
    ('Tess van Dijk', 'tessvandijk@hr.nl', 'password', 'student'),
    ('Klaas de Haan', 'klaasdehaan@hr.nl', 'password', 'student')";

    $result = db()->query($query);
}

$query = "SELECT * FROM schoolladder.classes";
$result = db()->query($query);

if ($result->rowCount() == 0) {
    $query = "INSERT INTO schoolladder.classes (name)
VALUES
    ('1A'),
    ('1B'),
    ('1C'),
    ('2A'),
    ('2B'),
    ('2C'),
    ('3A'),
    ('3B'),
    ('3C'),
    ('4A'),
    ('4B'),
    ('4C'),
    ('5A'),
    ('5B'),
    ('5C'),
    ('6A'),
    ('6B'),
    ('6C')";
    $result = db()->query($query);
}

$query = "SELECT * FROM schoolladder.students";
$result = db()->query($query);

if ($result->rowCount() == 0) {
    $query = "INSERT INTO schoolladder.students (user_id, points, year_group, class_id)
VALUES
    (1, 1356, 4, 10),
    (2, 1350, 4, 10),
    (3, 1302, 4, 10),
    (4, 1285, 4, 10),
    (5, 1260, 4, 10),
    (6, 1248, 4, 10),
    (7, 1233, 4, 10),
    (8, 1210, 4, 10),
    (9, 1194, 4, 10),
    (10, 1150, 4, 10),
    (11, 1369, 4, 12),
    (12, 1341, 1, 2),
    (13, 1290, 3, 8),
    (14, 1245, 4, 12),
    (15, 1330, 2, 6),
    (16, 1262, 4, 11),
    (17, 1299, 4, 11),
    (18, 1300, 5, 14),
    (19, 1375, 6, 17)";
    $result = db()->query($query);
}

$query = "
    SELECT students.*, users.name
    FROM schoolladder.students
    JOIN schoolladder.users ON students.user_id = users.id
    ORDER BY students.points DESC
";
$result = db()->query($query);

$students = $result->fetchAll();

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
        <h1 class="left-margin">Ranglijst</h1>
        <p class="left-margin">Bekijk de stand in jouw klas, jaarlaag en school. Zie wat je wint of verliest per
            positie.</p>

        <div class="row-containers">
            <div class="container second-surface" style="background-color: var(--surface-band);">
                <p><strong>Positie</strong></p>
                <h4>#10</h4>
                <p>van 26 leerlingen</p>
            </div>
            <div class="container second-surface" style="background-color: var(--surface-band);">
                <p><strong>Trend</strong></p>
                <h4>+3</h4>
                <p>Sinds gisteren</p>
            </div>
        </div>
        <div class="container second-surface score" style="background-color: var(--surface-band);">
            <p><strong>Totale score</strong></p>
            <h4>1248</h4>
            <p>Je bent 12 punten verwijdert van plek 5 op de klas ranglijst.</p>
        </div>
    </section>

    <div class="navy-space" style="background-color: var(--surface-page);">
    </div>

    <section id="ranking-filters" class="">
        <button class="filter-button">Klas</button>
        <button class="filter-button">Jaarlaag</button>
        <button class="filter-button">School</button>
    </section>

    <div class="navy-space" style="background-color: var(--surface-page);">
    </div>

    <section id="ranking" class="section container" style="background-color: var(--surface-2)">

        <h3>Topselectie</h3>
        <p>Klas 4A</p>

        <?php foreach ($students as $position => $student): ?>

            <div class="ranking-row">

                <div>
                    <?php if ($position === 0): ?>
                        <h4 class="first-ranking-position">1</h4>

                    <?php elseif ($position === 9): ?>
                        <h4 class="tenth-ranking-position">10</h4>

                    <?php else: ?>
                        <h4 class="other-ranking-position">
                            <?= $position + 1 ?>
                        </h4>
                    <?php endif; ?>
                </div>

                <div>
                    <p class="ranking-name" style="color: var(--white);">
                        <strong><?= e($student['name']) ?></strong>
                    </p>
                </div>

                <div class="growth">+2</div>

            </div>

        <?php endforeach; ?>

    </section>

</main>

<?php require __DIR__ . '/../includes/bottom-nav.php'; ?>
<script src="<?= BASE_URL ?>js/navbar.js"></script>
<script src="<?= BASE_URL ?>js/ranking.js"></script>
<script src="<?= BASE_URL ?>js/main.js"></script>

</body>
</html>
