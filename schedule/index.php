<?php
//page basis
$pageTitle = 'Rooster';

//fetching from the db: the week from the 1st and last day of the week

//how does datetime work again???
/*
print_r(time());                            // returns the time now as a number
print_r(date("Y-m-d H:i:s", time()));       // returns the time as a readable format
print_r(mktime(10,0,0));                    //hour,minute,second,month,day,year returns:the raw ass time
print_r(date('l'));                         //returns the day as the name of that day
print_r(date('W'));                         //returns the nth week of the year
pint_r(strtotime(monday));                          //returns the datetime of the string that is written
*/

$currentWeekNumber = date('W');
$currentDayName = date('l');
$currentYear = date('Y');
//$weekOffset = -2;

//$thisMonday = date('j ', strtotime("monday -2 week"));
//$thisSunday = date('j ', strtotime("sunday -1 week"));



function getWeekEvents($weekOffset):array
{
//    $weekStart = strtotime("monday $weekOffset week 0:00");
//    $weekEnd = strtotime("sunday $weekOffset week 23:00");
//    $formattedWeekStart = date("Y-m-d H:i:s", $weekStart);
//    $formattedWeekEnd = date("Y-m-d H:i:s", $weekEnd);
//    print_r($formattedWeekStart);
//    print_r($formattedWeekEnd);
    $classId = 1;

    //connect db
    require_once __DIR__ . '/../includes/config.php';    //gives a db as variable (not $db)

    //query to get events of the week
    //need to join the teacher name and the subject name and only from the right class
//    $query = "SELECT * FROM `events` WHERE start_time>'$formattedWeekStart' AND start_time<'$formattedWeekEnd'";
    $query = "SELECT * FROM `events` WHERE class_id=$classId";
    $result = mysqli_query(db, $query);
    $weekEvents = mysqli_fetch_all($result, MYSQLI_ASSOC);

    return $weekEvents;
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

//the schedule arrays
$weekEvents = getWeekEvents($currentWeekNumber);

//the array to print out the schedule => each:[timeIndicator, time, ma,di,wo,do,vr]
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

//$mondayThisWeekPart = date('Y-m-d', strtotime('Monday this week'));
//$tuesdayThisWeekPart = date('Y-m-d', strtotime('Tuesday this week'));
//$wednesdayThisWeekPart = date('Y-m-d', strtotime('Wednesday this week'));
//$thursdayThisWeekPart = date('Y-m-d', strtotime('Thursday this week'));
//$fridayThisWeekPart = date('Y-m-d', strtotime('Friday this week'));
$weekParts = [
        date('Y-m-d', strtotime("Monday week $currentWeekNumber")),
        date('Y-m-d', strtotime("Tuesday week $currentWeekNumber")),
        date('Y-m-d', strtotime("Wednesday week $currentWeekNumber")),
        date('Y-m-d', strtotime("Thursday week $currentWeekNumber")),
        date('Y-m-d', strtotime("Friday week $currentWeekNumber")),
];
$timeParts = [
        '08:30:10',
        '09:30:10',
        '10:45:10',
        '12:15:10',
        '13:15:10'
];
$shortDays = [
        'ma',
        'di',
        'wo',
        'do',
        'vr'
];

$test = 0;

foreach ($weekEvents as $key => $weekEvent) {
    // ma 8:30

    $startTime = strtotime($weekEvent['start_time']);
    $endTime = strtotime($weekEvent['end_time']);
    $checkTime = strtotime("$weekParts[0] $timeParts[0]");

//    //with all the arrays bc I have to use numbers
//    for ($i=0;$i<5;$i++) {
//        for ($j = 0; $j < 5; $j++) {
//            $checkTime = strtotime("$weekParts[$i] $timeParts[$j]");
//
//            if ($startTime <= $checkTime && $checkTime <= $endTime) {
//                print_r($shortDays[$i]);
//                print_r(' ');
//                print_r($timeParts[$j]);
//                print_r(' ');
//                print_r($weekEvent);
//                print_r('  ||  ');
//                $lessonTimes[$j+1][$shortDays[$i]]=$weekEvent['name'];
//
//            }
//        }
//    }
}
//print_r($lessonTimes);
//print_r("$weekParts[0] $timeParts[0]");
//print_r($weekEvents[0]);

//close the db
mysqli_close(db);


//get the array for each time


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
                <td></td>
                <th>Ma</th>
                <th>Di</th>
                <th>Wo</th>
                <th>Do</th>
                <th>Vr</th>
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
