<?php

/* Who is logged in. Pure data: no HTML, no output.
 *
 * PLACEHOLDER: there is no login yet. Whoever builds it replaces the array
 * below with the user from $_SESSION. The navbar only reads these four keys,
 * so nothing else has to change.
 *
 * Separate from pages.php because it answers a different question: that file
 * says which pages exist, this one says who is looking at them. */

declare(strict_types=1);

$currentUser = [
    'name'     => 'Rick van der Henk',
    'initials' => 'RH',
    'role'     => 'Leerling',
    'class'    => '4H',
];
