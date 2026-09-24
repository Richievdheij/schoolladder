<?php

/* Which icon belongs to which subject. Pure data: no HTML, no output.
 *
 * The key is the subject's abbreviation from the `subjects` table. A subject
 * that isn't in this list gets the default icon (book-open). The icon names
 * are file names from images/icons, without .svg. */

declare(strict_types=1);

const SUBJECT_ICONS = [
    'WI'  => 'calculator',
    'NE'  => 'languages',
    'EN'  => 'languages',
    'DU'  => 'languages',
    'FA'  => 'languages',
    'GS'  => 'landmark',
    'AK'  => 'globe',
    'BI'  => 'flask-conical',
    'NA'  => 'atom',
    'SK'  => 'flask-conical',
    'LO'  => 'dumbbell',
    'MU'  => 'music',
    'TE'  => 'palette',
    'INF' => 'code',
];

// Icon name for a subject, or book-open if it has no icon of its own.
function subjectIcon(string $abbreviation): string
{
    return SUBJECT_ICONS[$abbreviation] ?? 'book-open';
}
