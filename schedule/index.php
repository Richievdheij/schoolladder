<?php
//page basis
$pageTitle = 'Rooster';


//all the date stuff to write directly to the schedule table
$weekOffset = -0;
$currentWeekNumber = date('W', strtotime("$weekOffset week"));
$monthShort = date('M', strtotime("$weekOffset week"));

//based of your day you get a different result for the day based of the week
function weekFixer(): array
{
    switch (date('l')) {
        case 'Monday':
            return ['', '', '', '', ''];
            break;
        case 'Tuesday':
            return ['previous', '', '', '', ''];
            break;
        case 'Wednesday':
            return ['previous', 'previous', '', '', ''];
            break;
        case 'Thursday':
            return ['previous', 'previous', 'previous', '', ''];
            break;
        case 'Friday':
            return ['previous', 'previous', 'previous', 'previous', ''];
            break;
        default:
            return ['', '', '', '', '', '', ''];
    }
}

$weekFix = weekFixer();
$weekNumbers = [
        'ma' => date('j', strtotime("$weekFix[0] monday $weekOffset week")),
        'di' => date('j', strtotime("$weekFix[1] tuesday $weekOffset week")),
        'wo' => date('j', strtotime("$weekFix[2] wednesday $weekOffset week")),
        'do' => date('j', strtotime("$weekFix[3] thursday $weekOffset week")),
        'vr' => date('j', strtotime("$weekFix[4] friday $weekOffset week")),
];


//these are used to fill the schedule table
//main array to write to
$lessonTimes = [
        '1' => [
                'time' => '8:30',
                'ma' => '',
                'di' => '',
                'wo' => '',
                'do' => '',
                'vr' => ''
        ],
        '2' => [
                'time' => '9:30',
                'ma' => '',
                'di' => '',
                'wo' => '',
                'do' => '',
                'vr' => ''
        ],
        'kleine pauze' => [
                'time' => '10:30',
                'ma' => '',
                'di' => '',
                'wo' => '',
                'do' => '',
                'vr' => ''
        ],
        '3' => [
                'time' => '10:45',
                'ma' => '',
                'di' => '',
                'wo' => '',
                'do' => '',
                'vr' => ''
        ],
        'grote Pauze' => [
                'time' => '11:45',
                'ma' => '',
                'di' => '',
                'wo' => '',
                'do' => '',
                'vr' => ''
        ],
        '4' => [
                'time' => '12:15',
                'ma' => '',
                'di' => '',
                'wo' => '',
                'do' => '',
                'vr' => ''
        ],
        '5' => [
                'time' => '13:15',
                'ma' => '',
                'di' => '',
                'wo' => '',
                'do' => '',
                'vr' => ''
        ],
];
//the parts to put in the loops to fill the table horizontal(time) and verticle(day) parts
$dayParts = [
        date('Y-m-d', strtotime("$weekFix[0] monday $weekOffset week")),
        date('Y-m-d', strtotime("$weekFix[1] tuesday $weekOffset week")),
        date('Y-m-d', strtotime("$weekFix[2] wednesday $weekOffset week")),
        date('Y-m-d', strtotime("$weekFix[3] thursday $weekOffset week")),
        date('Y-m-d', strtotime("$weekFix[4] friday $weekOffset week"))
];
$timeParts = [
        '08:30:10',
        '09:30:10',
        '10:45:10',
        '12:15:10',
        '13:15:10'
];
$lessonTimesDayKeys = ['ma', 'di', 'wo', 'do', 'vr',];

function getWeekEvents($weekOffset): array
{
    $classId = 1;

    //connect db
    require_once __DIR__ . '/../includes/config.php';    //gives a db as variable (not $db)

    //query to get events of the week
    //need to join the teacher name and the subject name
    $query = "SELECT * FROM `events` WHERE class_id=$classId";
    $result = mysqli_query(db, $query);
    $weekEvents = mysqli_fetch_all($result, MYSQLI_ASSOC);

    return $weekEvents;
}

$weekEvents = getWeekEvents($currentWeekNumber);

foreach ($weekEvents as $event) {
    //checking if the pointer is between the start and endtime to write to the table
    $tablePointerTime = strtotime("$dayParts[0] $timeParts[0]");
    $eventStartTime = strtotime($event['start_time']);
    $eventEndTime = strtotime($event['end_time']);

    for ($x = 0; $x < 5; $x++) {
        for ($y = 0; $y < 5; $y++) {
            $tablePointerTime = strtotime("$dayParts[$x] $timeParts[$y]");
            if ($eventStartTime < $tablePointerTime && $tablePointerTime < $eventEndTime) {
                $lessonTimes[$y + 1][$lessonTimesDayKeys[$x]] = $event['name'];
            }
        }
    }
}

//don't touch the make events, unless there is time left to do the teacher side
function makeEvent()
{
    $startTime = '2026-09-23 09:17:51';
    $endTime = '2026-09-23 12:17:51';
    $name = 'test';
    $classId = 1;
    $teacherId = 1;
    $subjectId = 1;
    $location = 'hier';

    $query = "INSERT INTO `events`(`start_time`,, `end_time`, `name`, `class_id`, `teacher_id`, `subject_id`, `location`)
VALUES ('$startTime','$endTime','$name','$classId','$teacherId','$subjectId','$location')";
    $result = mysqli_query(db, $query);

}


//close the db
mysqli_close(db);

?>
<!DOCTYPE html>
<html lang="nl">
<link rel="stylesheet" href="../css/pages/rooster.css">

<?php require __DIR__ . '/../includes/header.php'; ?>
<body>
<?php require __DIR__ . '/../includes/navbar.php'; ?>
<main>
    <section id="schedule-box">
        <h1>Rooster</h1>
        <p>Bekijk je lessen, komende toetsen en waar je extra aandacht nodig hebt.</p>
        <div>
            <?php for ($i = 0; $i < 5; $i++) { ?>
                <button>W<?= $currentWeekNumber + $i ?></button>
            <?php } ?>
        </div>
        <table>
            <tr>
                <td>W<?= $currentWeekNumber ?></td>
                <td><?= $monthShort ?></td>
                <th>Ma <?= $weekNumbers['ma'] ?></th>
                <th>Di <?= $weekNumbers['di'] ?></th>
                <th>Wo <?= $weekNumbers['wo'] ?></th>
                <th>Do <?= $weekNumbers['do'] ?></th>
                <th>Vr <?= $weekNumbers['vr'] ?></th>
            </tr>
            <!--like 10 lesson hour blocks-->
            <!--put the events in the right place-->
            <?php
            foreach ($lessonTimes as $id => $lessonTime) { ?>
                <tr>
                    <th><?= $id ?></th>
                    <th><?= $lessonTime['time'] ?></th>

                    <td>
                        <!--subjectAbbreviation-->
                        <?= $lessonTime['ma'] ?>
                    </td>
                    <td><?= $lessonTime['di'] ?></td>
                    <td><?= $lessonTime['wo'] ?></td>
                    <td><?= $lessonTime['do'] ?></td>
                    <td><?= $lessonTime['vr'] ?></td>
                </tr>
            <?php } ?>

        </table>
    </section>
    <section id="day-schedule-list">
        <div class="day-event">
            <h3>Naam - Vak</h3>
            <p>leraar - locatie</p>
            <h4>start - eind</h4>
        </div>
        <div class="day-event">
            <h3>Naam - Vak</h3>
            <p>leraar - locatie</p>
            <h4>start - eind</h4>
        </div>
        <!--the amount based of lessons based of today-->
        <?php
        //foreach currentDaySchedule as here-under
        $beginDay = strtotime("this day 0:00");
        $endDay = strtotime("this day 23:00");

        foreach ($weekEvents as $weekEvent) {
            if ($weekEvent['start_time'] > $beginDay && $weekEvent['end_time'] < $endDay) {
                //make the day schedule box

                ?>
                <div class="day-event">
                    <h3><?= $weekEvent['name'] ?> - <?= $weekEvent['subject'] ?></h3>
                    <p><?= $weekEvent['teacher_name'] ?> - <?= $weekEvent['location'] ?></p>
                    <h4><?= $weekEvent['start_time'] ?> - <?= $weekEvent['end_time'] ?></h4>
                </div>
                <?php
            }
        }
        ?>
    </section>
</main>

</body>
</html>
