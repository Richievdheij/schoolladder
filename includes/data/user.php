<?php

/* Who is logged in. Pure data: no HTML, no output.
 *
 * PLACEHOLDER: there is no login yet. Whoever builds it replaces the array
 * below with the user from $_SESSION. The navbar reads the first four keys,
 * so nothing there has to change. 'student_id' is the row in `students` this
 * user belongs to, for pages (like grades) that query the database for it.
 *
 * Separate from pages.php because it answers a different question: that file
 * says which pages exist, this one says who is looking at them. */

declare(strict_types=1);

$currentUser = [
    'name'       => 'Rick van der Henk',
    'initials'   => 'RH',
    'role'       => 'Leerling',
    'class'      => '4H',
    'student_id' => 1,
];
