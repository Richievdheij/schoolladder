<?php

declare(strict_types=1);

$page = 'grades';

require __DIR__ . '/../includes/config.php';
require __DIR__ . '/../includes/data/user.php';

// All grades of this student with the subject's name and icon, newest first.
$stmt = db()->prepare(
    'SELECT g.type, g.weight, g.grade, g.created_at, s.name AS subject_name, s.icon
     FROM grades g
     JOIN subjects s ON s.id = g.subject_id
     WHERE g.student_id = :student_id
     ORDER BY g.created_at DESC'
);
$stmt->execute(['student_id' => $currentUser['student_id']]);

$grades = [];    // all grades in one list (for the overall average)
$bySubject = []; // the same grades, grouped by subject

foreach ($stmt->fetchAll() as $row) {
    // The database returns numbers as text; turn them into real numbers.
    $row['grade'] = (float) $row['grade'];
    $row['weight'] = (float) $row['weight'];

    $grades[] = $row;
    $bySubject[$row['subject_name']]['icon'] = $row['icon'];
    $bySubject[$row['subject_name']]['grades'][] = $row;
}

// Weighted average: a grade with weight 2 counts twice as much.
function weightedAverage(array $rows): float
{
    $sum = 0;
    $totalWeight = 0;

    foreach ($rows as $row) {
        $sum += $row['grade'] * $row['weight'];
        $totalWeight += $row['weight'];
    }

    return $sum / $totalWeight;
}

// Change grade from x.x to x,x
function formatGrade(float $grade): string
{
    return number_format($grade, 1, ',', '');
}

// Date display
function relativeDate(string $createdAt): string
{
    $date = new DateTime($createdAt);
    $days = $date->diff(new DateTime())->days;

    return match (true) {
        $days === 0 => 'vandaag',
        $days === 1 => 'gisteren',
        $days <= 6  => $days . ' dagen geleden',
        default     => $date->format('d-m-Y'),
    };
}

// Renders one grade as an <li>. It's a function because we need it in two
// places: for the newest grade and for the older grades.
function renderGradeRow(array $row): void
{
    ?>
    <li class="grades__row">
        <?php // Below 6 is a fail and gets the red "fail" style. ?>
        <span class="grades__badge grades__badge--<?= $row['grade'] < 6 ? 'fail' : 'pass' ?>">
            <?= e(formatGrade($row['grade'])) ?>
        </span>

        <span class="grades__meta">
            <span class="grades__type"><?= e(ucfirst($row['type'])) ?></span>
            <span class="grades__date"><?= e(relativeDate($row['created_at'])) ?></span>
        </span>

        <?php // Only show the weight if it isn't the default (1). 1.5 is shown as "1,5". ?>
        <?php if ($row['weight'] !== 1.0): ?>
            <span class="grades__weight-pill">&times;<?= e(str_replace('.', ',', (string) $row['weight'])) ?></span>
        <?php endif; ?>
    </li>
    <?php
}

?>
<!DOCTYPE html>
<html lang="nl">

<?php require __DIR__ . '/../includes/header.php'; ?>

<body>

<?php require __DIR__ . '/../includes/navbar.php'; ?>

<main class="page">

    <div class="container section grades">

        <div class="page__head">
            <h1>Cijfers</h1>
            <p class="lead">Bekijk je gemiddelde en je cijfers per vak.</p>
        </div>

        <?php if ($grades === []): ?>

            <!-- No grades yet: show a message instead of an empty page. -->
            <div class="card">
                <p class="text-muted">Zodra een docent een cijfer invoert, verschijnt het hier.</p>
            </div>

        <?php else: ?>

            <div class="stack">

                <!-- Overall average across all subjects. -->
                <div class="card grades__summary">
                    <div>
                        <h2>Algemeen gemiddelde</h2>
                        <p class="text-muted">Gewogen gemiddelde over <?= count($grades) ?> cijfers.</p>
                    </div>
                    <span class="grades__summary-value"><?= e(formatGrade(weightedAverage($grades))) ?></span>
                </div>

                <!-- One card per subject. -->
                <?php foreach ($bySubject as $subject => $data): ?>
                    <?php
                    $newest = $data['grades'][0];                 // newest grade, always visible
                    $older = array_slice($data['grades'], 1);     // the rest, collapsed by default
                    ?>

                    <div class="card grades__subject">
                        <div class="card__head">
                            <span class="grades__subject-title">
                                <?= icon($data['icon']) ?>
                                <h2><?= e($subject) ?></h2>
                            </span>
                            <span class="grades__subject-average"><?= e(formatGrade(weightedAverage($data['grades']))) ?></span>
                        </div>

                        <ul class="grades__list">
                            <?php renderGradeRow($newest); ?>
                        </ul>

                        <?php if ($older !== []): ?>
                            <!-- <details> opens/closes without JavaScript. -->
                            <details class="grades__more">
                                <summary class="grades__more-toggle">
                                    <span>
                                        Toon <?= count($older) ?> eerdere <?= count($older) === 1 ? 'cijfer' : 'cijfers' ?>
                                    </span>
                                    <span class="grades__more-chevron"><?= icon('chevron-down') ?></span>
                                </summary>

                                <ul class="grades__list">
                                    <?php foreach ($older as $row): ?>
                                        <?php renderGradeRow($row); ?>
                                    <?php endforeach; ?>
                                </ul>
                            </details>
                        <?php endif; ?>
                    </div>

                <?php endforeach; ?>
            </div>

        <?php endif; ?>

    </div>

</main>

<?php require __DIR__ . '/../includes/bottom-nav.php'; ?>

<script src="<?= BASE_URL ?>js/navbar.js"></script>
<script src="<?= BASE_URL ?>js/main.js"></script>

</body>
</html>
