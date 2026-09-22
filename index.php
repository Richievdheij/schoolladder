<?php

/* Mijn ladder, de homepage.
 *
 * Every figure on this page comes from includes/data/dashboard.php; here it
 * is only laid out. Each block assumes its data may be missing: null is "not
 * known yet", an empty list is "nothing to show". */

$page = 'dashboard';

require_once __DIR__ . '/includes/data/user.php';
require_once __DIR__ . '/includes/data/dashboard.php';

/** @var array{id: ?int, name: ?string, initials: ?string, role: ?string, class: ?string} $currentUser */
/** @var array $dashboard */

$firstName = $currentUser['name'] === null
    ? null
    : explode(' ', $currentUser['name'])[0];

$position = $dashboard['position'];
$attendance = $dashboard['attendance'];

/* One dash for every unknown value, so one card does not say "-" while the
   next says "n.v.t.". */
$dash = '–';

$hasTrend = $position['trend'] !== null;
$trendUp = $hasTrend && $position['trend'] >= 0;

/* The lowest of the parts that have a standing at all. Without the filter a
   part nobody has looked at yet (level 0) would always come out lowest, and
   the student gets advice about something that was never assessed. From two
   upwards, because with one the only part is trivially also the weakest. */
$graded = array_values(array_filter(
    $dashboard['assessment'],
    static fn (array $row): bool => $row['level'] > 0
));

$weakest = null;

if (count($graded) > 1) {
    $levels = array_column($graded, 'level');
    $weakest = $graded[array_search(min($levels), $levels, true)];
}

$levelTone = static fn (int $level): string => match (true) {
    $level <= 0 => 'none',
    $level >= 4 => 'strong',
    $level === 3 => 'ok',
    $level === 2 => 'warn',
    default => 'weak',
};

/* Bar height scales within the series, not against the number of students:
   between place 12 and 15 out of 64 sits four percent, which would draw
   seven bars of the same height. The place is printed above every bar, so
   the exact value is always there to read. */
$barHeight = static function (int $rank, int $best, int $worst): int {
    if ($worst === $best) {
        return 100;
    }

    return (int) round(100 - ($rank - $best) / ($worst - $best) * 65);
};

$trendGlyph = ['up' => '▲', 'down' => '▼', 'flat' => $dash];
$trendWord = ['up' => 'stijgend', 'down' => 'dalend', 'flat' => 'gelijk gebleven'];

$lessonStatus = [
    'done'     => 'Afgerond',
    'now'      => 'Nu bezig',
    'upcoming' => 'Straks',
];

?>
<!DOCTYPE html>
<html lang="nl">

<?php require __DIR__ . '/includes/header.php'; ?>

<body>

<?php require __DIR__ . '/includes/navbar.php'; ?>

<main class="page">

    <div class="container section dashboard">
        <div class="dashboard__grid">

            <header class="card dashboard__hero">

                <div class="dashboard__hero-head">
                    <div>
                        <h1 class="dashboard__greeting">
                            <?= e($dashboard['greeting']) ?><?= $firstName === null ? '' : ', ' . e($firstName) ?>
                        </h1>
                        <p class="dashboard__hero-intro"><?= e($dashboard['intro']) ?></p>

                        <p class="dashboard__updated">
                            <?= icon('clock') ?><span>Bijgewerkt om <?= e($dashboard['updatedAt']) ?></span>
                        </p>
                    </div>
                    <?php if ($currentUser['initials'] !== null): ?>
                        <span class="avatar" aria-hidden="true"><?= e($currentUser['initials']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="dashboard__stats">

                    <div class="dashboard__stat">
                        <span class="dashboard__stat-label">Positie</span>
                        <strong class="dashboard__stat-value<?= $position['rank'] === null ? ' dashboard__stat-value--none' : '' ?>">
                            <?= $position['rank'] === null ? $dash : '#' . num($position['rank']) ?>
                        </strong>
                        <span class="dashboard__stat-meta">
                            <?php if ($position['rank'] === null): ?>
                                nog niet gerangschikt
                            <?php elseif ($position['total'] === null): ?>
                                op de ladder
                            <?php else: ?>
                                van <?= num($position['total']) ?> leerlingen
                            <?php endif; ?>
                        </span>
                    </div>

                    <div class="dashboard__stat">
                        <span class="dashboard__stat-label">Trend</span>
                        <strong class="dashboard__stat-value<?= $hasTrend ? ' dashboard__stat-value--' . ($trendUp ? 'up' : 'down') : ' dashboard__stat-value--none' ?>">
                            <?php if ($hasTrend): ?>
                                <?= icon($trendUp ? 'arrow-up-right' : 'trending-up') ?>
                                <?= delta($position['trend']) ?>
                            <?php else: ?>
                                <?= $dash ?>
                            <?php endif; ?>
                        </strong>
                        <span class="dashboard__stat-meta">
                            <?php if (!$hasTrend): ?>
                                nog geen eerdere meting
                            <?php elseif (($position['trendSince'] ?? null) !== null): ?>
                                plekken sinds <?= e($position['trendSince']) ?>
                            <?php else: ?>
                                plekken verschoven
                            <?php endif; ?>
                        </span>
                    </div>

                    <div class="dashboard__stat">
                        <span class="dashboard__stat-label">Zone</span>
                        <strong class="dashboard__stat-value<?= $position['zone'] === null ? ' dashboard__stat-value--none' : '' ?>">
                            <?= $position['zone'] === null ? $dash : e($position['zone']) ?>
                        </strong>
                        <span class="dashboard__stat-meta">
                            <?php if ($position['zone'] === null): ?>
                                nog niet ingedeeld
                            <?php else: ?>
                                <?= e($position['margin'] ?? 'marge nog onbekend') ?>
                            <?php endif; ?>
                        </span>
                    </div>

                </div>

                <?php /* Gone when there is nothing to warn about: a warning
                         that says all is well is not a warning. */ ?>
                <?php if ($dashboard['zoneCheck'] !== null): ?>
                    <section class="dashboard__zone" aria-labelledby="dashboard-zone">
                        <div class="dashboard__zone-head">
                            <span class="dashboard__zone-icon" aria-hidden="true"><?= icon('triangle-alert') ?></span>
                            <h2 class="dashboard__zone-title" id="dashboard-zone">
                                <?= e($dashboard['zoneCheck']['title']) ?>
                            </h2>
                        </div>

                        <p><?= e($dashboard['zoneCheck']['body']) ?></p>

                        <?php if (($dashboard['zoneCheck']['meta'] ?? '') !== ''): ?>
                            <p class="dashboard__zone-meta"><?= e($dashboard['zoneCheck']['meta']) ?></p>
                        <?php endif; ?>
                    </section>
                <?php endif; ?>

            </header>

            <section class="card dashboard__ladder" aria-labelledby="dashboard-ladder">
                <div class="card__head">
                    <h2 id="dashboard-ladder">Ranglijst</h2>
                    <a class="dashboard__more" href="<?= BASE_URL . ($navItems['ranking']['url'] ?? '') ?>">
                        Alles <?= icon('chevron-right') ?>
                    </a>
                </div>

                <?php if ($dashboard['ladder'] === []): ?>
                    <?= emptyState(
                        'trophy',
                        'Je bent nog niet ingedeeld.',
                        'Zodra je een plek hebt, zie je hier wie er om je heen staat.'
                    ) ?>
                <?php else: ?>
                    <ol class="dashboard__ranks">
                        <?php foreach ($dashboard['ladder'] as $row): ?>
                            <?php
                            $moved = $row['move'] === null
                                ? null
                                : ($row['move'] > 0 ? 'up' : ($row['move'] < 0 ? 'down' : 'flat'));
                            ?>
                            <li class="dashboard__rank<?= $row['me'] ? ' dashboard__rank--me' : '' ?>">
                                <span class="dashboard__rank-pos"><?= num($row['rank']) ?></span>
                                <span class="dashboard__rank-name">
                                    <?= e($row['me'] ? ($currentUser['name'] ?? 'Jij') : $row['name']) ?>
                                    <?php if ($row['me']): ?>
                                        <span class="dashboard__rank-you">jij</span>
                                    <?php endif; ?>
                                </span>
                                <span class="dashboard__rank-class"><?= e($row['class']) ?></span>
                                <span class="dashboard__rank-move<?= $moved === null ? '' : ' dashboard__rank-move--' . $moved ?>">
                                    <?php if ($moved === null): ?>
                                        <?= $dash ?>
                                        <span class="visually-hidden">nog niet gemeten</span>
                                    <?php else: ?>
                                        <?= $trendGlyph[$moved] ?><?= $row['move'] === 0 ? '' : ' ' . num(abs($row['move'])) ?>
                                        <span class="visually-hidden">
                                            <?= $moved === 'flat'
                                                ? 'onveranderd'
                                                : num(abs($row['move'])) . ' plekken ' . ($moved === 'up' ? 'gestegen' : 'gedaald') ?>
                                        </span>
                                    <?php endif; ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                <?php endif; ?>
            </section>

            <section class="card dashboard__today" aria-labelledby="dashboard-today">
                <div class="card__head">
                    <h2 id="dashboard-today">Vandaag</h2>
                    <a class="dashboard__more" href="<?= BASE_URL . ($navItems['schedule']['url'] ?? '') ?>">
                        Rooster <?= icon('chevron-right') ?>
                    </a>
                </div>

                <?php if ($dashboard['lessons'] === []): ?>
                    <?= emptyState(
                        'calendar',
                        'Geen lessen vandaag.',
                        'Je rooster voor de rest van de week staat op de roosterpagina.'
                    ) ?>
                <?php else: ?>
                    <ol class="dashboard__timeline">
                        <?php foreach ($dashboard['lessons'] as $lesson): ?>
                            <li class="dashboard__lesson dashboard__lesson--<?= e($lesson['status']) ?>">
                                <span class="dashboard__lesson-time"><?= e($lesson['time']) ?></span>
                                <span class="dashboard__lesson-marker" aria-hidden="true"></span>
                                <span class="dashboard__lesson-body">
                                    <span class="dashboard__lesson-subject"><?= e($lesson['subject']) ?></span>
                                    <span class="dashboard__lesson-meta">
                                        <?= icon('map-pin') ?><?= e($lesson['room']) ?>
                                        &middot; <?= e($lesson['teacher']) ?>
                                    </span>
                                </span>
                                <span class="dashboard__lesson-status">
                                    <?= e($lessonStatus[$lesson['status']] ?? '') ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                <?php endif; ?>
            </section>

            <section class="card dashboard__attendance" aria-labelledby="dashboard-attendance">
                <div class="card__head">
                    <h2 id="dashboard-attendance">Aanwezigheid</h2>
                    <a class="dashboard__more" href="<?= BASE_URL . ($navItems['attendance']['url'] ?? '') ?>">
                        Alles <?= icon('chevron-right') ?>
                    </a>
                </div>

                <?php if ($attendance === null): ?>
                    <?= emptyState(
                        'user-check',
                        'Nog geen registraties.',
                        'Vanaf je eerste les houdt het systeem je aanwezigheid bij.'
                    ) ?>
                <?php else: ?>
                    <p class="dashboard__streak">
                        <span class="dashboard__streak-icon" aria-hidden="true"><?= icon('flame') ?></span>
                        <strong class="dashboard__streak-value">
                            <?= $attendance['streak'] === null ? $dash : num($attendance['streak']) ?>
                        </strong>
                        <span class="dashboard__streak-label">dagen op rij op tijd</span>
                    </p>

                    <ul class="dashboard__figures">
                        <li>
                            <strong><?= $attendance['present'] === null ? $dash : num($attendance['present']) . '%' ?></strong>
                            <span>aanwezig</span>
                        </li>
                        <li>
                            <strong><?= $attendance['late'] === null ? $dash : num($attendance['late']) ?></strong>
                            <span>te laat</span>
                        </li>
                        <li>
                            <strong><?= $attendance['missed'] === null ? $dash : num($attendance['missed']) ?></strong>
                            <span>gemist</span>
                        </li>
                    </ul>

                    <?php if (($attendance['badges'] ?? []) !== []): ?>
                        <ul class="dashboard__badges">
                            <?php foreach ($attendance['badges'] as $badge): ?>
                                <li class="dashboard__badge<?= $badge['earned'] ? '' : ' dashboard__badge--locked' ?>">
                                    <?= icon($badge['earned'] ? $badge['icon'] : 'lock') ?>
                                    <?= e($badge['label']) ?>
                                    <?php if (!$badge['earned']): ?>
                                        <span class="visually-hidden">(nog niet behaald)</span>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                <?php endif; ?>
            </section>

            <section class="card dashboard__assessment" aria-labelledby="dashboard-assessment">
                <div class="card__head">
                    <h2 id="dashboard-assessment">Waar je op beoordeeld wordt</h2>
                    <span class="card__meta">dit blok</span>
                </div>

                <?php if ($dashboard['assessment'] === []): ?>
                    <?= emptyState(
                        'target',
                        'Nog niet genoeg om je in te delen.',
                        'Zodra het systeem genoeg van je heeft gezien, staat hier per onderdeel hoe je ervoor staat.'
                    ) ?>
                <?php else: ?>
                    <ul class="dashboard__levels">
                        <?php foreach ($dashboard['assessment'] as $row): ?>
                            <?php $tone = $levelTone($row['level']); ?>
                            <li class="dashboard__level">
                                <span class="dashboard__level-label"><?= e($row['label']) ?></span>

                                <span class="dashboard__level-status dashboard__level-status--<?= $tone ?>">
                                    <?= $row['level'] > 0 ? e($row['status']) : 'nog geen stand' ?>
                                </span>

                                <?php /* Hidden from screen readers: the word above says
                                         the same thing, and --steps keeps PHP and CSS on
                                         the same scale. */ ?>
                                <span class="dashboard__meter" style="--steps: <?= ASSESSMENT_STEPS ?>" aria-hidden="true">
                                    <?php for ($step = 1; $step <= ASSESSMENT_STEPS; $step++): ?>
                                        <span class="dashboard__meter-step<?= $step <= $row['level'] ? ' dashboard__meter-step--' . $tone : '' ?>"></span>
                                    <?php endfor; ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <?php if ($weakest !== null): ?>
                        <p class="dashboard__levels-note">
                            <strong><?= e($weakest['label']) ?></strong>
                            is het onderdeel waar je het meest kunt winnen.
                        </p>
                    <?php else: ?>
                        <p class="dashboard__levels-note">
                            Zodra een onderdeel is vastgesteld, zie je hier waar je het meest kunt winnen.
                        </p>
                    <?php endif; ?>
                <?php endif; ?>
            </section>

            <section class="card dashboard__standing" aria-labelledby="dashboard-standing">
                <div class="card__head">
                    <h2 id="dashboard-standing">Je stand</h2>
                    <?php if ($position['total'] !== null): ?>
                        <span class="card__meta">plek van <?= num($position['total']) ?></span>
                    <?php endif; ?>
                </div>

                <?php if ($dashboard['standing'] === []): ?>
                    <?= emptyState(
                        'trending-up',
                        'Nog geen metingen.',
                        'Zodra je een plek hebt, zie je hier hoe die verschuift.'
                    ) ?>
                <?php else: ?>
                    <div class="dashboard__tabs" role="tablist" aria-label="Periode">
                        <?php foreach ($dashboard['standing'] as $key => $serie): ?>
                            <?php $first = $key === array_key_first($dashboard['standing']); ?>
                            <button
                                type="button"
                                class="dashboard__tab<?= $first ? ' is-active' : '' ?>"
                                role="tab"
                                id="tab-<?= e($key) ?>"
                                aria-controls="panel-<?= e($key) ?>"
                                aria-selected="<?= $first ? 'true' : 'false' ?>"
                            ><?= e($serie['label']) ?></button>
                        <?php endforeach; ?>
                    </div>

                    <?php foreach ($dashboard['standing'] as $key => $serie): ?>
                        <?php
                        $first = $key === array_key_first($dashboard['standing']);
                        $ranks = array_column($serie['points'], 'rank');
                        ?>
                        <div
                            class="dashboard__panel"
                            id="panel-<?= e($key) ?>"
                            role="tabpanel"
                            aria-labelledby="tab-<?= e($key) ?>"
                            <?= $first ? '' : 'hidden' ?>
                        >
                            <?php if ($ranks === []): ?>
                                <?php /* One period can be empty while another is not. */ ?>
                                <?= emptyState(
                                    'trending-up',
                                    'Deze periode is nog niet gemeten.',
                                    'Kies een kortere periode om te zien hoe je er nu voor staat.'
                                ) ?>
                            <?php else: ?>
                                <?php
                                $best = min($ranks);
                                $worst = max($ranks);
                                ?>
                                <ol class="dashboard__chart" aria-label="Je plek per <?= e($serie['label']) ?>">
                                    <?php foreach ($serie['points'] as $point): ?>
                                        <li class="dashboard__chart-col<?= $point['current'] ? ' dashboard__chart-col--today' : '' ?>">
                                            <span class="dashboard__chart-points">#<?= num($point['rank']) ?></span>
                                            <span
                                                class="dashboard__chart-bar"
                                                style="--height: <?= $barHeight($point['rank'], $best, $worst) ?>%"
                                                aria-hidden="true"
                                            ></span>
                                            <span class="dashboard__chart-day"><?= e($point['label']) ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ol>

                                <?php if ($serie['note'] !== ''): ?>
                                    <p class="dashboard__chart-note"><?= e($serie['note']) ?></p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </section>

            <section class="card dashboard__recent" aria-labelledby="dashboard-recent">
                <div class="card__head">
                    <h2 id="dashboard-recent">Recent beoordeeld</h2>
                    <a class="dashboard__more" href="<?= BASE_URL . ($navItems['grades']['url'] ?? '') ?>">
                        Alles <?= icon('chevron-right') ?>
                    </a>
                </div>

                <?php if ($dashboard['recent'] === []): ?>
                    <?= emptyState(
                        'file-text',
                        'Er is nog niets beoordeeld.',
                        'Je eerste cijfer verschijnt hier zodra een docent het invoert.'
                    ) ?>
                <?php else: ?>
                    <ul class="dashboard__list">
                        <?php foreach ($dashboard['recent'] as $row): ?>
                            <li class="dashboard__grade">
                                <span class="dashboard__grade-mark dashboard__grade-mark--<?= $row['grade'] >= 5.5 ? 'pass' : 'fail' ?>">
                                    <?= num($row['grade'], 1) ?>
                                </span>
                                <span class="dashboard__grade-body">
                                    <span class="dashboard__grade-subject"><?= e($row['subject']) ?></span>
                                    <span class="dashboard__grade-meta"><?= e($row['when']) ?></span>
                                </span>
                                <?php if (($row['type'] ?? null) !== null): ?>
                                    <span class="dashboard__grade-type"><?= e($row['type']) ?></span>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>

            <section class="card dashboard__subjects" aria-labelledby="dashboard-subjects">
                <div class="card__head">
                    <h2 id="dashboard-subjects">Vakken</h2>
                    <a class="dashboard__more" href="<?= BASE_URL . ($navItems['subjects']['url'] ?? '') ?>">
                        Alles <?= icon('chevron-right') ?>
                    </a>
                </div>

                <?php if ($dashboard['subjects'] === []): ?>
                    <?= emptyState(
                        'book-open',
                        'Nog geen vakken toegewezen.',
                        'Zodra je bent ingedeeld, staat hier je gemiddelde per vak.'
                    ) ?>
                <?php else: ?>
                    <ul class="dashboard__list">
                        <?php foreach ($dashboard['subjects'] as $subject): ?>
                            <?php $trend = $subject['average'] === null ? null : $subject['trend']; ?>
                            <li class="dashboard__subject">
                                <span class="dashboard__subject-name"><?= e($subject['name']) ?></span>
                                <span class="dashboard__subject-average<?= $subject['average'] === null ? ' dashboard__subject-average--none' : '' ?>">
                                    <?= $subject['average'] === null ? $dash : num($subject['average'], 1) ?>
                                </span>
                                <span class="dashboard__subject-trend<?= $trend === null ? '' : ' dashboard__subject-trend--' . e($trend) ?>">
                                    <?php if ($trend === null): ?>
                                        <span class="visually-hidden">nog niets beoordeeld</span>
                                    <?php else: ?>
                                        <?= $trendGlyph[$trend] ?>
                                        <span class="visually-hidden"><?= e($trendWord[$trend]) ?></span>
                                    <?php endif; ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <p class="dashboard__subjects-note">Je gemiddelde per vak, over alle beoordelingen heen.</p>
                <?php endif; ?>
            </section>

            <section class="card dashboard__actions" aria-labelledby="dashboard-actions">
                <div class="card__head">
                    <h2 id="dashboard-actions">Wat nu</h2>
                    <span class="dashboard__actions-icon" aria-hidden="true"><?= icon('sparkles') ?></span>
                </div>

                <?php if ($dashboard['actions'] === []): ?>
                    <?= emptyState(
                        'circle-check',
                        'Niets te doen.',
                        'Zodra er iets van je wordt verwacht, staat het hier.'
                    ) ?>
                <?php else: ?>
                    <p class="dashboard__actions-lead">
                        Je bent op schema. Dit levert vandaag het meeste op.
                    </p>

                    <ul class="dashboard__steps">
                        <?php foreach ($dashboard['actions'] as $action): ?>
                            <li>
                                <a class="dashboard__step" href="<?= BASE_URL . ($navItems[$action['page']]['url'] ?? '') ?>">
                                    <span class="dashboard__step-icon" aria-hidden="true"><?= icon($action['icon']) ?></span>
                                    <span class="dashboard__step-body">
                                        <strong class="dashboard__step-title"><?= e($action['title']) ?></strong>
                                        <span class="dashboard__step-text"><?= e($action['body']) ?></span>
                                    </span>
                                    <span class="dashboard__step-chevron" aria-hidden="true"><?= icon('chevron-right') ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>

        </div>
    </div>

</main>

<?php require __DIR__ . '/includes/bottom-nav.php'; ?>

<script src="<?= BASE_URL ?>js/navbar.js"></script>
<script src="<?= BASE_URL ?>js/dashboard.js"></script>
<script src="<?= BASE_URL ?>js/main.js"></script>

</body>
</html>
