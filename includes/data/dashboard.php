<?php

/* What the dashboard shows, read from the database. Pure data: no HTML.
 *
 * index.php reads these keys and nothing else, so this is the only file that
 * knows about tables. Two rules hold throughout:
 *
 *   No points. A student is ranked but never sees the sum behind it.
 *   students.points is read here - you cannot rank without it - but never
 *   ends up in $dashboard.
 *
 *   null means "not known yet", an empty array means "nothing to show".
 *   Never 0 or "-", because the template has to tell "no lessons today"
 *   apart from "the schedule did not load". Formatting is its job, not ours.
 *
 * The blocks that stay empty need tables this schema does not have yet.
 * database-dashboard.sql holds them, with a note on what each one feeds. */

declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/user.php';

/** @var array{id: ?int, name: ?string, initials: ?string, role: ?string, class: ?string, student_id: ?int} $currentUser */

/** Steps on the meters under "Waar je op beoordeeld wordt". */
const ASSESSMENT_STEPS = 4;

/** The student row for the logged-in user: null for a teacher, or for nobody. */
function dashboardStudent(?int $userId): ?array
{
    if ($userId === null) {
        return null;
    }

    $statement = db()->prepare(
        'SELECT s.id, s.points, s.year_group, s.class_id
           FROM students s
          WHERE s.user_id = ?'
    );
    $statement->execute([$userId]);

    return $statement->fetch() ?: null;
}

/**
 * Place and total. Counted on every request rather than read from a stored
 * standing, so the ladder is right again the moment someone's points change.
 * Equal scores share a place.
 */
function dashboardPosition(?array $student): array
{
    $unknown = [
        'rank' => null, 'total' => null, 'trend' => null,
        'trendSince' => null, 'zone' => null, 'margin' => null,
    ];

    if ($student === null) {
        return $unknown;
    }

    $total = (int) db()->query('SELECT COUNT(*) FROM students')->fetchColumn();

    if ($total === 0) {
        return $unknown;
    }

    $statement = db()->prepare('SELECT COUNT(*) + 1 FROM students WHERE points > ?');
    $statement->execute([$student['points']]);

    return [
        'rank'       => (int) $statement->fetchColumn(),
        'total'      => $total,
        'trend'      => null,
        'trendSince' => null,
        'zone'       => null,
        'margin'     => null,
    ];
}

/**
 * The two students above and the two below. At the top or the bottom of the
 * list there are simply fewer, which the template already handles.
 *
 * Fetching everyone is fine: a school has hundreds of students, not millions.
 */
function dashboardLadder(?array $student): array
{
    if ($student === null) {
        return [];
    }

    /* points is selected to rank on and is never carried into the result. */
    $rows = db()->query(
        'SELECT s.id, s.points, u.name, c.name AS class
           FROM students s
           JOIN users u ON u.id = s.user_id
           LEFT JOIN classes c ON c.id = s.class_id
          ORDER BY s.points DESC, u.name ASC'
    )->fetchAll();

    $index = null;

    foreach ($rows as $offset => $row) {
        if ((int) $row['id'] === (int) $student['id']) {
            $index = $offset;
            break;
        }
    }

    if ($index === null) {
        return [];
    }

    /* Equal scores share a place, exactly the way dashboardPosition() counts
       it. Numbering by row number instead would show the same student as #3
       in the hero and #4 in this list. */
    $places = [];
    $place = 1;

    foreach ($rows as $offset => $row) {
        if ($offset > 0 && $row['points'] < $rows[$offset - 1]['points']) {
            $place = $offset + 1;
        }

        $places[$offset] = $place;
    }

    $slice = array_slice($rows, max(0, $index - 2), 5, true);

    /* Places moved, against each one's own last snapshot. Whoever has no
       snapshot yet keeps null, and the template writes a dash. */
    $ids = array_column($slice, 'id');
    $marks = [];

    if ($ids !== []) {
        $statement = db()->query(
            'SELECT student_id, SUBSTRING_INDEX(
                        GROUP_CONCAT(rank_position ORDER BY recorded_at DESC), ",", 1
                    ) AS was
               FROM standing_snapshots
              WHERE student_id IN (' . implode(',', array_map('intval', $ids)) . ')
              GROUP BY student_id'
        );

        foreach ($statement as $row) {
            $marks[(int) $row['student_id']] = (int) $row['was'];
        }
    }

    $ladder = [];

    foreach ($slice as $offset => $row) {
        $id = (int) $row['id'];
        $isMe = $id === (int) $student['id'];

        $ladder[] = [
            'rank'  => $places[$offset],
            /* Your own name comes from data/user.php, not from here. */
            'name'  => $isMe ? '' : (string) $row['name'],
            'class' => (string) ($row['class'] ?? ''),
            'move'  => isset($marks[$id]) ? $marks[$id] - $places[$offset] : null,
            'me'    => $isMe,
        ];
    }

    return $ladder;
}

/** Today's lessons for this student's class, with a status read off the clock. */
function dashboardLessons(?array $student): array
{
    if ($student === null) {
        return [];
    }

    $statement = db()->prepare(
        'SELECT e.start_time, e.end_time, e.name, e.location,
                subject.name AS subject, teacher.name AS teacher
           FROM events e
           LEFT JOIN subjects subject ON subject.id = e.subject_id
           LEFT JOIN users teacher ON teacher.id = e.teacher_id
          WHERE e.class_id = ? AND DATE(e.start_time) = CURDATE()
          ORDER BY e.start_time'
    );
    $statement->execute([$student['class_id']]);

    $now = new DateTimeImmutable();
    $lessons = [];

    foreach ($statement as $row) {
        $start = new DateTimeImmutable($row['start_time']);
        $end = new DateTimeImmutable($row['end_time']);

        $lessons[] = [
            'time' => $start->format('H:i'),
            /* events.name is a lesson's own title; without one the subject
               name says enough. */
            'subject' => (string) ($row['name'] ?: ($row['subject'] ?? '')),
            'room'    => (string) $row['location'],
            'teacher' => (string) ($row['teacher'] ?? ''),
            'status'  => match (true) {
                $now > $end    => 'done',
                $now >= $start => 'now',
                default        => 'upcoming',
            },
        ];
    }

    return $lessons;
}

/** The three most recent marks. */
function dashboardRecent(?array $student): array
{
    if ($student === null) {
        return [];
    }

    $statement = db()->prepare(
        'SELECT g.grade, g.created_at, subject.name AS subject
           FROM grades g
           LEFT JOIN subjects subject ON subject.id = g.subject_id
          WHERE g.student_id = ?
          ORDER BY g.created_at DESC, g.id DESC
          LIMIT 3'
    );
    $statement->execute([$student['id']]);

    $recent = [];

    foreach ($statement as $row) {
        $recent[] = [
            'subject' => (string) ($row['subject'] ?? ''),
            'grade'   => (float) $row['grade'],
            'type'    => null,
            'when'    => relativeDay($row['created_at']),
        ];
    }

    return $recent;
}

/**
 * The average per subject. Which subjects a student takes is not stored
 * anywhere, so these are the subjects their class has lessons in. A subject
 * without marks keeps an average of null, which is not the same as a zero.
 */
function dashboardSubjects(?array $student): array
{
    if ($student === null) {
        return [];
    }

    $statement = db()->prepare(
        'SELECT subject.name, AVG(g.grade) AS average
           FROM subjects subject
           LEFT JOIN grades g ON g.subject_id = subject.id AND g.student_id = :student
          WHERE subject.id IN (SELECT DISTINCT subject_id FROM events WHERE class_id = :class)
          GROUP BY subject.id, subject.name
          ORDER BY subject.name'
    );
    $statement->execute([':student' => $student['id'], ':class' => $student['class_id']]);

    $subjects = [];

    foreach ($statement as $row) {
        $subjects[] = [
            'name'    => (string) $row['name'],
            'average' => $row['average'] === null ? null : round((float) $row['average'], 1),
            'trend'   => null,
        ];
    }

    return $subjects;
}

/**
 * The zone a total falls in, plus the margin as a word. The margin is a
 * points difference, and a student does not get to see those.
 */
function dashboardZone(int $points): array
{
    $statement = db()->prepare(
        'SELECT letter, min_points FROM zones WHERE min_points <= ? ORDER BY min_points DESC LIMIT 1'
    );
    $statement->execute([$points]);
    $zone = $statement->fetch();

    if ($zone === false) {
        return ['zone' => null, 'margin' => null];
    }

    $margin = $points - (int) $zone['min_points'];

    return [
        'zone'   => $zone['letter'],
        'margin' => match (true) {
            $margin < 50  => 'krappe marge',
            $margin < 150 => 'redelijke marge',
            default       => 'ruime marge',
        },
    ];
}

/** Places moved since the last snapshot. A lower place number is a gain. */
function dashboardTrend(int $studentId, int $liveRank): array
{
    $statement = db()->prepare(
        'SELECT rank_position, recorded_at FROM standing_snapshots
          WHERE student_id = ? ORDER BY recorded_at DESC LIMIT 1'
    );
    $statement->execute([$studentId]);
    $last = $statement->fetch();

    if ($last === false) {
        return ['trend' => null, 'trendSince' => null];
    }

    $moment = new DateTimeImmutable($last['recorded_at']);

    return [
        'trend' => (int) $last['rank_position'] - $liveRank,
        'trendSince' => $moment->format('Y-m-d') === date('Y-m-d')
            ? $moment->format('H:i')
            : $moment->format('d-m'),
    ];
}

/**
 * Where a student stands per category, as a band. level 0 means nothing has
 * been recorded for it yet, which is not the same as scoring nothing.
 */
function dashboardAssessment(?array $student): array
{
    if ($student === null) {
        return [];
    }

    $statement = db()->prepare(
        'SELECT c.name, c.max_points, COALESCE(SUM(e.points), 0) AS earned
           FROM point_categories c
           LEFT JOIN point_events e ON e.category_id = c.id AND e.student_id = ?
          GROUP BY c.id, c.name, c.max_points, c.sort_order
          ORDER BY c.sort_order'
    );
    $statement->execute([$student['id']]);

    $rows = [];

    foreach ($statement as $row) {
        $max = (int) $row['max_points'];
        $earned = (int) $row['earned'];
        $share = $max > 0 ? $earned / $max : 0;

        [$level, $status] = match (true) {
            $earned === 0  => [0, ''],
            $share >= 0.85 => [4, 'Sterk'],
            $share >= 0.70 => [3, 'Voldoende'],
            $share >= 0.50 => [2, 'Let op'],
            default        => [1, 'Zwak'],
        };

        $rows[] = ['label' => $row['name'], 'level' => $level, 'status' => $status];
    }

    return $rows;
}

/**
 * Your place over time, in three periods. Each period keeps the most recent
 * snapshot per day, week or month; a period without snapshots stays empty and
 * the card says so itself.
 */
function dashboardStanding(?array $student): array
{
    if ($student === null) {
        return [];
    }

    $days = ['zo', 'ma', 'di', 'wo', 'do', 'vr', 'za'];
    $months = [1 => 'jan', 'feb', 'mrt', 'apr', 'mei', 'jun', 'jul', 'aug', 'sep', 'okt', 'nov', 'dec'];

    $periods = [
        'days'   => ['label' => '7 dagen',     'group' => '%Y-%m-%d', 'take' => 7],
        'weeks'  => ['label' => 'Wekelijks',   'group' => '%x-%v',    'take' => 6],
        'months' => ['label' => 'Maandelijks', 'group' => '%Y-%m',    'take' => 6],
    ];

    $standing = [];

    foreach ($periods as $key => $period) {
        $statement = db()->prepare(
            'SELECT SUBSTRING_INDEX(
                        GROUP_CONCAT(rank_position ORDER BY recorded_at DESC), ",", 1
                    ) AS rank_position,
                    MAX(recorded_at) AS moment
               FROM standing_snapshots
              WHERE student_id = ?
              GROUP BY DATE_FORMAT(recorded_at, ?)
              ORDER BY moment DESC
              LIMIT ' . (int) $period['take']
        );
        $statement->execute([$student['id'], $period['group']]);

        /* Newest first out of the database, oldest first on the screen. */
        $rows = array_reverse($statement->fetchAll());
        $points = [];
        $last = count($rows) - 1;

        foreach ($rows as $index => $row) {
            $moment = new DateTimeImmutable($row['moment']);

            $points[] = [
                'label' => match ($key) {
                    'days'   => $days[(int) $moment->format('w')],
                    'weeks'  => 'wk ' . $moment->format('W'),
                    'months' => $months[(int) $moment->format('n')],
                },
                'rank'    => (int) $row['rank_position'],
                'current' => $index === $last,
            ];
        }

        $standing[$key] = [
            'label'  => $period['label'],
            'note'   => '',
            'points' => $points,
        ];
    }

    return $standing;
}

/** Attendance, the streak and the badges. null when nothing is recorded yet. */
function dashboardAttendance(?array $student): ?array
{
    if ($student === null) {
        return null;
    }

    $statement = db()->prepare(
        'SELECT status, COUNT(*) AS total FROM attendance_records
          WHERE student_id = ? GROUP BY status'
    );
    $statement->execute([$student['id']]);

    $counts = ['present' => 0, 'late' => 0, 'absent' => 0];

    foreach ($statement as $row) {
        $counts[$row['status']] = (int) $row['total'];
    }

    $total = array_sum($counts);

    if ($total === 0) {
        return null;
    }

    /* The streak: days back from the most recent one on which every lesson
       was attended on time. One late arrival ends it. */
    $statement = db()->prepare(
        "SELECT DATE(e.start_time) AS day, SUM(a.status <> 'present') AS slips
           FROM attendance_records a
           JOIN events e ON e.id = a.event_id
          WHERE a.student_id = ?
          GROUP BY day ORDER BY day DESC"
    );
    $statement->execute([$student['id']]);

    $streak = 0;

    foreach ($statement as $day) {
        if ((int) $day['slips'] > 0) {
            break;
        }

        $streak++;
    }

    $statement = db()->prepare(
        'SELECT b.name, b.icon, sb.student_id IS NOT NULL AS earned
           FROM badges b
           LEFT JOIN student_badges sb ON sb.badge_id = b.id AND sb.student_id = ?
          ORDER BY b.sort_order'
    );
    $statement->execute([$student['id']]);

    $badges = [];

    foreach ($statement as $badge) {
        $badges[] = [
            'label'  => $badge['name'],
            'icon'   => $badge['icon'],
            'earned' => (bool) $badge['earned'],
        ];
    }

    return [
        'streak'  => $streak,
        'present' => (int) round($counts['present'] / $total * 100),
        'late'    => $counts['late'],
        'missed'  => $counts['absent'],
        'badges'  => $badges,
    ];
}

/** The scheduled zone check for this class, or null when none is coming. */
function dashboardZoneCheck(?array $student, ?string $zone, ?string $margin): ?array
{
    if ($student === null || $zone === null) {
        return null;
    }

    $statement = db()->prepare(
        'SELECT scheduled_at, note FROM zone_checks
          WHERE (class_id = ? OR class_id IS NULL)
            AND (year_group = ? OR year_group IS NULL)
            AND scheduled_at >= NOW()
          ORDER BY scheduled_at LIMIT 1'
    );
    $statement->execute([$student['class_id'], $student['year_group']]);
    $check = $statement->fetch();

    if ($check === false) {
        return null;
    }

    $statement = db()->prepare(
        'SELECT letter FROM zones
          WHERE min_points < (SELECT min_points FROM zones WHERE letter = ?)
          ORDER BY min_points DESC LIMIT 1'
    );
    $statement->execute([$zone]);
    $below = $statement->fetchColumn() ?: null;

    $days = (int) (new DateTimeImmutable('today'))
        ->diff(new DateTimeImmutable((new DateTimeImmutable($check['scheduled_at']))->format('Y-m-d')))
        ->format('%a');

    return [
        'title' => match (true) {
            $days === 0 => 'Zonecontrole vandaag',
            $days === 1 => 'Zonecontrole morgen',
            default     => "Zonecontrole over $days dagen",
        },
        'body' => $below === null
            ? "Je zit in zone $zone, de onderste."
            : ($margin === 'krappe marge'
                ? "Je marge tot zone $below is krap. Je hebt tot de controle om dat te keren."
                : "Je zit met een $margin boven zone $below."),
        'meta' => (string) $check['note'],
    ];
}

/** What a student can do now, worked out from what actually happened. */
function dashboardActions(?array $student, array $assessment, array $lessons): array
{
    if ($student === null) {
        return [];
    }

    $actions = [];

    $statement = db()->prepare(
        'SELECT COUNT(*) FROM grades
          WHERE student_id = ? AND created_at >= CURDATE() - INTERVAL 1 DAY'
    );
    $statement->execute([$student['id']]);
    $fresh = (int) $statement->fetchColumn();

    if ($fresh > 0) {
        $actions[] = [
            'page'  => 'grades',
            'icon'  => 'file-text',
            'title' => 'Bekijk je nieuwe cijfers',
            'body'  => $fresh === 1
                ? 'Er is 1 beoordeling bijgekomen.'
                : "Er zijn $fresh beoordelingen bijgekomen.",
        ];
    }

    $left = count(array_filter($lessons, static fn (array $l): bool => $l['status'] !== 'done'));

    if ($left > 0) {
        $actions[] = [
            'page'  => 'schedule',
            'icon'  => 'calendar',
            'title' => $left === 1 ? 'Nog 1 les vandaag' : "Nog $left lessen vandaag",
            'body'  => 'Op tijd binnen houdt je reeks in stand.',
        ];
    }

    /* Only from two assessed parts up: with one, that one is trivially the
       lowest and the advice says nothing. */
    $graded = array_values(array_filter($assessment, static fn (array $r): bool => $r['level'] > 0));

    if (count($graded) > 1) {
        $levels = array_column($graded, 'level');
        $weakest = $graded[array_search(min($levels), $levels, true)];

        $actions[] = [
            'page'  => 'ranking',
            'icon'  => 'target',
            'title' => 'Werk aan ' . lcfirst($weakest['label']),
            'body'  => 'Daar valt op dit moment het meeste te winnen.',
        ];
    }

    return $actions;
}

/**
 * The empty page, and the base everything else is merged into, so every key
 * always exists and index.php never has to check whether something arrived.
 */
function dashboardSkeleton(): array
{
    $hour = (int) date('G');

    return [
        'greeting' => match (true) {
            $hour < 6  => 'Goedenacht',
            $hour < 12 => 'Goedemorgen',
            $hour < 18 => 'Goedemiddag',
            default    => 'Goedenavond',
        },
        'intro' => 'Dit is je stand op dit moment. Verandert er iets, '
            . 'dan verandert het hier mee.',

        /* When the page was built, which is also when its figures were read:
           nothing here comes out of an overnight batch. */
        'updatedAt' => date('H:i'),

        'position' => [
            'rank'   => null,
            'total'  => null,
            'trend'  => null,
            /* What the trend is measured against, as a time. That makes it
               "sinds 13:00" rather than a fixed "sinds gisteren". */
            'trendSince' => null,
            'zone'   => null,
            'margin' => null,
        ],
        'zoneCheck'  => null,
        'assessment' => [],
        'standing'   => [],
        'lessons'    => [],
        'recent'     => [],
        'subjects'   => [],
        'ladder'     => [],
        'attendance' => null,
        'actions'    => [],
    ];
}

$dashboard = dashboardSkeleton();

/* One try around the lot. A database that is down, a schema that has not been
   imported and nobody being logged in all end the same way: the skeleton
   stays and the page shows its empty state. That is what those are for. */
try {
    $student = dashboardStudent($currentUser['id']);

    $position = dashboardPosition($student);

    if ($student !== null && $position['rank'] !== null) {
        $position = [...$position, ...dashboardZone((int) $student['points'])];
        $position = [...$position, ...dashboardTrend((int) $student['id'], $position['rank'])];
    }

    $assessment = dashboardAssessment($student);
    $lessons = dashboardLessons($student);

    $dashboard = [...$dashboard,
        'position'   => $position,
        'zoneCheck'  => dashboardZoneCheck($student, $position['zone'], $position['margin']),
        'assessment' => $assessment,
        'standing'   => dashboardStanding($student),
        'lessons'    => $lessons,
        'ladder'     => dashboardLadder($student),
        'recent'     => dashboardRecent($student),
        'subjects'   => dashboardSubjects($student),
        'attendance' => dashboardAttendance($student),
        'actions'    => dashboardActions($student, $assessment, $lessons),
    ];
} catch (Throwable $exception) {
    if (DEBUG) {
        error_log('dashboard: ' . $exception->getMessage());
    }
}
