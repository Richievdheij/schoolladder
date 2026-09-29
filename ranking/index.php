<?php

$page = 'ranking';
require_once __DIR__ . '/../includes/config.php';
/** @var mysqli $pdo */

//Make the page look like the wireframe - gedaan
//Accounts need to be made first (preferably on other page, but I can do it here for now) in order to get student names from user_id - gedaan
//give students points, year_group and class_id. - gedaan
//put the student names in #ranking in order of who has the most points - gedaan
//make the filters work (with js) - gedaan
//make the growth work based on last update - ik weet niet helemaal hoe ik dit doe...


//inserting all data in de database (once)

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
    ('Ahmet Demir', 'ahmetdemir@hr.nl', 'password', 'student'),
    ('James de Groot', 'jamesdegroot@hr.nl', 'password', 'student'),
    ('Noor Vos', 'noorvos@hr.nl', 'password', 'student'),
    ('Finn Kok', 'finnkok@hr.nl', 'password', 'student'),
    ('Peter van Leeuwen', 'petervanleeuwen@hr.nl', 'password', 'student'),
    ('Leo Peters', 'leopeters@hr.nl', 'password', 'student'),
    ('Christina van Dijk', 'christinavandijk@hr.nl', 'password', 'student'),
    ('Chris van Dijk', 'chrisvandijk@hr.nl', 'password', 'student'),
    ('Tess van Dijk', 'tessvandijk@hr.nl', 'password', 'student'),
    ('Klaas de Haan', 'klaasdehaan@hr.nl', 'password', 'student'), 
    ('Sarah Timmermans', 'sarahtimmermans@hr.nl', 'password', 'student'),
    ('Saartje Timmermans', 'saartjetimmermans@hr.nl', 'password', 'student')";

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
    $query = "INSERT INTO schoolladder.students (user_id, points, previous_points, year_group, class_id)
VALUES
    (1, 1356, 1343, 4, 10),
    (2, 1350, 1346, 4, 10),
    (3, 1302, 1293, 4, 10),
    (4, 1285, 1280, 4, 10),
    (5, 1260, 1250, 4, 10),
    (6, 1248, 1225, 4, 10),
    (7, 1233, 1228, 4, 10),
    (8, 1210, 1205, 4, 10),
    (9, 1194, 1194, 4, 10),
    (10, 1150, 1140, 4, 10),
    (11, 1145, 1142, 4, 10),
    (12, 1369, 1345, 4, 12),
    (13, 1341, 1336, 1, 2),
    (14, 1290, 1286, 4, 12),
    (15, 1245, 1240, 4, 12),
    (16, 1330, 1325, 2, 6),
    (17, 1262, 1257, 4, 11),
    (18, 1299, 1284, 4, 11),
    (19, 1300, 1296, 5, 14),
    (20, 1375, 1360, 6, 17), 
    (21, 1352, 1348, 6, 14),
    (22, 1340, 1335, 3, 8)";
    $result = db()->query($query);
}

//current rankings

$query = "
    SELECT students.*, users.name
    FROM schoolladder.students
    JOIN schoolladder.users ON students.user_id = users.id
    ORDER BY students.points DESC
";
$result = db()->query($query);

$all_students = $result->fetchAll();


$query = "
    SELECT students.*, users.name
    FROM schoolladder.students
    JOIN schoolladder.users ON students.user_id = users.id
    JOIN schoolladder.classes ON students.class_id = classes.id   
    WHERE class_id = 10
    ORDER BY students.points DESC
";
$result = db()->query($query);

$class4A_students = $result->fetchAll();


$query = "
    SELECT students.*, users.name
    FROM schoolladder.students
    JOIN schoolladder.users ON students.user_id = users.id   
    WHERE year_group = 4
    ORDER BY students.points DESC
";
$result = db()->query($query);

$year_group_4_students = $result->fetchAll();


//calculating previous ranking for growth

$query = "
    SELECT students.*, users.name
    FROM schoolladder.students
    JOIN schoolladder.users ON students.user_id = users.id
    ORDER BY students.previous_points DESC
";

$result = db()->query($query);

$previous_all_students = $result->fetchAll();

$previous_all_students_positions = [];

foreach ($previous_all_students as $position => $student) {
    $previous_all_students_positions[$student['user_id']] = $position + 1;
}


$query = "
    SELECT students.*, users.name
    FROM schoolladder.students
    JOIN schoolladder.users ON students.user_id = users.id
    JOIN schoolladder.classes ON students.class_id = classes.id   
    WHERE class_id = 10
    ORDER BY students.previous_points DESC
";
$result = db()->query($query);

$previous_class4A_students = $result->fetchAll();

$previous_class4A_positions = [];

foreach ($previous_class4A_students as $position => $student) {
    $previous_class4A_positions[$student['user_id']] = $position + 1;
}


$query = "
    SELECT students.*, users.name
    FROM schoolladder.students
    JOIN schoolladder.users ON students.user_id = users.id   
    WHERE year_group = 4
    ORDER BY students.previous_points DESC
";
$result = db()->query($query);

$previous_year_group_4_students = $result->fetchAll();


$previous_year_group_4_positions = [];

foreach ($previous_year_group_4_students as $position => $student) {
    $previous_year_group_4_positions[$student['user_id']] = $position + 1;
}

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
        <div class="summary-row">
            <div>
                <h1 class="left-margin">Ranglijst</h1>
                <p class="left-margin">Bekijk de stand in jouw klas, jaarlaag en school. Zie wat je wint of verliest per
                    positie.</p>
            </div>
            <div>
                <h2 class="profile">N</h2>
            </div>
        </div>

        <div class="row-containers">
            <div class="container second-surface" style="background-color: var(--surface-band);">
                <p><strong>Positie</strong></p>
                <h4 id="position">#6</h4>
                <p id="student-amount">van 26 leerlingen</p>
            </div>
            <div class="container second-surface" style="background-color: var(--surface-band);">
                <p><strong>Trend</strong></p>
                <h4>+2</h4>
                <p>Sinds gisteren</p>
            </div>
        </div>
        <div class="container second-surface score" style="background-color: var(--surface-band);">
            <p><strong>Totale score</strong></p>
            <h4>1248</h4>
            <p id="points-away">Je bent 12 punten verwijdert van plek 5 op de klas ranglijst.</p>
        </div>
    </section>

    <div class="navy-space" style="background-color: var(--surface-page);">
    </div>

    <section id="ranking-filters" class="">
        <button class="filter-button" onclick="showClassRanking();
        changeContent('position', (element) => {
            element.textContent = '#6';
        });
        changeContent('student-amount', (element) => {
            element.textContent = 'van 26 leerlingen';
        });
        changeContent('points-away', (element) => {
            element.textContent = 'Je bent 12 punten verwijdert van plek 5 op de klas ranglijst.';
        });">
            Klas
        </button>
        <button class="filter-button" onclick="showYearGroupRanking();
        changeContent('position', (element) => {
            element.textContent = '#10';
        });
        changeContent('student-amount', (element) => {
            element.textContent = 'van 76 leerlingen';
        });
        changeContent('points-away', (element) => {
            element.textContent = 'Je bent 12 punten verwijdert van plek 9 op de jaarlaag ranglijst.';
        });">
            Jaarlaag
        </button>
        <button class="filter-button" onclick="showSchoolRanking();
        changeContent('position', (element) => {
            element.textContent = '#16';
        });
        changeContent('student-amount', (element) => {
            element.textContent = 'van 450 leerlingen';
        });
        changeContent('points-away', (element) => {
            element.textContent = 'Je bent 12 punten verwijdert van plek 15 op de school ranglijst.';
        });">
            School
        </button>
    </section>

    <div class="navy-space" style="background-color: var(--surface-page);">
    </div>

    <section id="class-ranking" class="section container" style="background-color: var(--surface-2)">

        <div class="text-row">
            <h3>Topselectie</h3>
            <p>Klas 4A</p>
        </div>

        <?php foreach ($class4A_students as $position => $student): ?>

            <?php if ($position >= 10) break; ?>

            <?php
            $current_position = $position + 1;
            $previous_position = $previous_class4A_positions[$student['user_id']];
            $growth = $previous_position - $current_position;
            ?>

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

                <div class="growth">
                    <?php if ($growth > 0): ?>
                        +<?= $growth ?>
                    <?php elseif ($growth < 0): ?>
                        <?= $growth ?>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </section>

    <section id="year-group-ranking" class="section container"
             style="background-color: var(--surface-2); display: none;">

        <div class="text-row">
            <h3>Topselectie</h3>
            <p>Jaarlaag 4</p>
        </div>

        <?php foreach ($year_group_4_students as $position => $student): ?>

            <?php if ($position >= 10) break; ?>
            <?php
            $current_position = $position + 1;
            $previous_position = $previous_year_group_4_positions[$student['user_id']];
            $growth = $previous_position - $current_position;
            ?>

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

                <div class="growth">
                    <?php if ($growth > 0): ?>
                        +<?= $growth ?>
                    <?php elseif ($growth < 0): ?>
                        <?= $growth ?>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </section>

    <section id="school-ranking" class="section container"
             style="background-color: var(--surface-2); display: none;">

        <div class="text-row">
            <h3>Topselectie</h3>
            <p>School</p>
        </div>

        <?php foreach ($all_students as $position => $student): ?>

            <?php if ($position >= 10) break; ?>

            <?php
            $current_position = $position + 1;
            $previous_position = $previous_all_students_positions[$student['user_id']];
            $growth = $previous_position - $current_position;
            ?>

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

                <div class="growth">
                    <?php if ($growth > 0): ?>
                        +<?= $growth ?>
                    <?php elseif ($growth < 0): ?>
                        <?= $growth ?>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </div>
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
