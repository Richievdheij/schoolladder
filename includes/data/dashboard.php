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

/** @var array{id: ?int, name: ?string, initials: ?string, role: ?string, class: ?string} $currentUser */

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

    $ladder = [];

    foreach (array_slice($rows, max(0, $index - 2), 5, true) as $offset => $row) {
        $isMe = (int) $row['id'] === (int) $student['id'];

        $ladder[] = [
            'rank'  => $places[$offset],
            /* Your own name comes from data/user.php, not from here. */
            'name'  => $isMe ? '' : (string) $row['name'],
            'class' => (string) ($row['class'] ?? ''),
            'move'  => null,
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

    $dashboard = [...$dashboard,
        'position' => dashboardPosition($student),
        'ladder'   => dashboardLadder($student),
        'lessons'  => dashboardLessons($student),
        'recent'   => dashboardRecent($student),
        'subjects' => dashboardSubjects($student),
    ];
} catch (Throwable $exception) {
    if (DEBUG) {
        error_log('dashboard: ' . $exception->getMessage());
    }
}
