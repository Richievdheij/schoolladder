<?php

$page = 'ranking';
require_once __DIR__ . '/../includes/config.php';
/** @var mysqli $pdo */

// Bepaal de eerste klas en jaarlaag met studenten (dynamisch)
$first_student = db()->query("
    SELECT s.class_id, s.year_group, c.name AS class_name
    FROM students s
    JOIN classes c ON s.class_id = c.id
    ORDER BY s.id
    LIMIT 1
")->fetch();

$current_class_id   = $first_student ? (int) $first_student['class_id'] : 1;
$current_year_group = $first_student ? (int) $first_student['year_group'] : 4;
$current_class_name = $first_student ? $first_student['class_name'] : 'Onbekend';

// --- School ranking (alle studenten) ---

$all_students = db()->query("
    SELECT students.*, users.name
    FROM students
    JOIN users ON students.user_id = users.id
    ORDER BY students.points DESC
")->fetchAll();

// --- Klas ranking ---

$stmt = db()->prepare("
    SELECT students.*, users.name
    FROM students
    JOIN users ON students.user_id = users.id
    WHERE students.class_id = ?
    ORDER BY students.points DESC
");
$stmt->execute([$current_class_id]);
$class_students = $stmt->fetchAll();

// --- Jaarlaag ranking ---

$stmt = db()->prepare("
    SELECT students.*, users.name
    FROM students
    JOIN users ON students.user_id = users.id
    WHERE students.year_group = ?
    ORDER BY students.points DESC
");
$stmt->execute([$current_year_group]);
$year_group_students = $stmt->fetchAll();

// --- Vorige posities voor groei-berekening ---

$previous_all_students = db()->query("
    SELECT students.*, users.name
    FROM students
    JOIN users ON students.user_id = users.id
    ORDER BY students.previous_points DESC
")->fetchAll();

$previous_all_positions = [];
foreach ($previous_all_students as $pos => $s) {
    $previous_all_positions[$s['user_id']] = $pos + 1;
}

$stmt = db()->prepare("
    SELECT students.*, users.name
    FROM students
    JOIN users ON students.user_id = users.id
    WHERE students.class_id = ?
    ORDER BY students.previous_points DESC
");
$stmt->execute([$current_class_id]);
$previous_class_positions = [];
foreach ($stmt->fetchAll() as $pos => $s) {
    $previous_class_positions[$s['user_id']] = $pos + 1;
}

$stmt = db()->prepare("
    SELECT students.*, users.name
    FROM students
    JOIN users ON students.user_id = users.id
    WHERE students.year_group = ?
    ORDER BY students.previous_points DESC
");
$stmt->execute([$current_year_group]);
$previous_year_positions = [];
foreach ($stmt->fetchAll() as $pos => $s) {
    $previous_year_positions[$s['user_id']] = $pos + 1;
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
            <p>Klas <?= e($current_class_name) ?></p>
        </div>

        <?php foreach ($class_students as $position => $student): ?>

            <?php if ($position >= 10) break; ?>

            <?php
            $current_position = $position + 1;
            $previous_position = $previous_class_positions[$student['user_id']] ?? $current_position;
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
            <p>Jaarlaag <?= $current_year_group ?></p>
        </div>

        <?php foreach ($year_group_students as $position => $student): ?>

            <?php if ($position >= 10) break; ?>
            <?php
            $current_position = $position + 1;
            $previous_position = $previous_year_positions[$student['user_id']] ?? $current_position;
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
            $previous_position = $previous_all_positions[$student['user_id']] ?? $current_position;
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
