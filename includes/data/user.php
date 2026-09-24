<?php

/* Who is logged in. Pure data: no HTML, no output.
 *
 * The portal puts the whole users row in $_SESSION['user']. With nobody
 * logged in every value below is null and the pages fall back to their empty
 * state; there is no redirect to the login screen yet. */

declare(strict_types=1);

require_once __DIR__ . '/../config.php';

/* The portal starts the session itself. Everywhere else it has to happen
   here, or $_SESSION is empty while someone is in fact logged in. */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * First letter of the first name and of the last. One word gives one letter.
 * mb_* so a name with an accent is not cut through a character.
 */
function initialsOf(?string $name): ?string
{
    $name = trim((string) $name);

    if ($name === '') {
        return null;
    }

    $parts = preg_split('/\s+/', $name) ?: [$name];
    $initials = mb_substr($parts[0], 0, 1);

    if (count($parts) > 1) {
        $initials .= mb_substr((string) end($parts), 0, 1);
    }

    return mb_strtoupper($initials);
}

$sessionUser = $_SESSION['user'] ?? null;
$userId = isset($sessionUser['id']) ? (int) $sessionUser['id'] : null;

/* Deliberately not kept in the session: a class can change while someone is
   logged in. If the lookup fails the rest of the page still stands. */
$userClass = null;
$studentId = null;

if ($userId !== null) {
    try {
        $statement = db()->prepare(
            'SELECT s.id, c.name
               FROM students s
               JOIN classes c ON c.id = s.class_id
              WHERE s.user_id = ?'
        );
        $statement->execute([$userId]);
        $studentRow = $statement->fetch();

        if ($studentRow) {
            $studentId = (int) $studentRow['id'];
            $userClass = $studentRow['name'];
        }
    } catch (Throwable $exception) {
        $userClass = null;
        $studentId = null;
    }
}

$currentUser = [
    'id'         => $userId,
    'name'       => $sessionUser['name'] ?? null,
    'initials'   => initialsOf($sessionUser['name'] ?? null),
    'role'       => match ($sessionUser['role'] ?? null) {
        'student' => 'Leerling',
        'teacher' => 'Docent',
        'admin'   => 'Beheerder',
        default   => null,
    },
    'class'      => $userClass,
    // Null for anyone who is not a student: teachers, admins, nobody logged in.
    'student_id' => $studentId,
];
