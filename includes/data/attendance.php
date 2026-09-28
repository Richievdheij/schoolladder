<?php

/* What the attendance page shows, read from the database. Pure data: no HTML.
 *
 * attendance/index.php reads these keys and nothing else, so this is the only
 * file that knows about tables. Two rules hold throughout:
 *
 *   null means "not known yet", an empty array means "nothing to show".
 *   The template has to tell "no records yet" from "the data did not load". */

declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/user.php';

/** @var array{id: ?int, name: ?string, initials: ?string, role: ?string, class: ?string} $currentUser */

/**
 * One attendance record as a card. The class matches attendance.css: empty for
 * present, afwezig for absent, te-laat for late, so the border and icon colour
 * follow the status without the template repeating the match.
 */
function attendanceCard(array $row): array
{
    $start = new DateTimeImmutable($row['start_time']);
    $end = new DateTimeImmutable($row['end_time']);

    return [
        'class'   => match ((string) $row['status']) {
            'late'   => 'te-laat',
            'absent' => 'afwezig',
            default  => '',
        },
        'subject' => (string) ($row['subject'] ?? ''),
        'kind'    => $row['test'] ? 'Toets' : 'Les',
        'label'   => match ((string) $row['status']) {
            'present' => 'Op tijd',
            'late'    => 'Te laat',
            'absent'  => 'Afwezig',
            default   => ucfirst((string) $row['status']),
        },
        'time' => $start->format('H:i') . '-' . $end->format('H:i'),
        'when' => relativeDay($row['start_time']),
    ];
}

/** The student row for the logged-in user: null for a teacher, or for nobody. */
function attendanceStudent(?int $userId): ?array
{
    if ($userId === null) {
        return null;
    }

    $statement = db()->prepare(
        'SELECT s.id
           FROM students s
          WHERE s.user_id = ?'
    );
    $statement->execute([$userId]);

    return $statement->fetch() ?: null;
}

/**
 * The records for the lessons of today, oldest lesson first. Only lessons that
 * have a record show up: a lesson that has not been marked yet is not a
 * record, so it stays out of the list.
 */
function attendanceToday(?array $student): array
{
    if ($student === null) {
        return [];
    }

    $statement = db()->prepare(
        'SELECT a.status,
                e.start_time, e.end_time, e.name,
                subject.name AS subject,
                t.id AS test
           FROM attendance_records a
           JOIN events e ON e.id = a.event_id
           LEFT JOIN subjects subject ON subject.id = e.subject_id
           LEFT JOIN tests t ON t.event_id = e.id
           WHERE a.student_id = ? AND DATE(e.start_time) = CURDATE()
           ORDER BY e.start_time'
    );
    $statement->execute([$student['id']]);

    $rows = [];

    foreach ($statement as $row) {
        $rows[] = attendanceCard($row);
    }

    return $rows;
}

/**
 * The records of the last seven days, newest lesson first. The date is
 * relative, so a lesson from yesterday reads "gisteren" and one from the start
 * of the week reads "5 dagen geleden".
 */
function attendanceLast7Days(?array $student): array
{
    if ($student === null) {
        return [];
    }

    $statement = db()->prepare(
        'SELECT a.status,
                e.start_time, e.end_time, e.name,
                subject.name AS subject,
                t.id AS test
           FROM attendance_records a
           JOIN events e ON e.id = a.event_id
           LEFT JOIN subjects subject ON subject.id = e.subject_id
           LEFT JOIN tests t ON t.event_id = e.id
           WHERE a.student_id = ? AND DATE(e.start_time) >= CURDATE() - INTERVAL 6 DAY
           ORDER BY e.start_time DESC, e.id DESC'
    );
    $statement->execute([$student['id']]);

    $rows = [];

    foreach ($statement as $row) {
        $rows[] = attendanceCard($row);
    }

    return $rows;
}

/** The empty page, so every key always exists and index.php never has to check. */
function attendanceSkeleton(): array
{
    return [
        'today'     => [],
        'last7Days' => [],
    ];
}

$attendance = attendanceSkeleton();

/* One try around the lot. A database that is down, a schema that has not been
   imported and nobody being logged in all end the same way: the skeleton stays
   and the page shows its empty state. That is what those are for. */
try {
    $student = attendanceStudent($currentUser['id']);

    $attendance = [...$attendance,
        'today'     => attendanceToday($student),
        'last7Days' => attendanceLast7Days($student),
    ];
} catch (Throwable $exception) {
    if (DEBUG) {
        error_log('attendance: ' . $exception->getMessage());
    }
}
