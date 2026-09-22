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
$weekOffset = -1;                //weirdly the offset starts from -1, so 0 is next week, -2 is previous week, etc

function getWeekEvents($weekOffset)
{
    $weekStart = strtotime("monday $weekOffset week 0:00");
    $weekEnd = strtotime("sunday $weekOffset week 23:00");

    //connect db
    require __DIR__ . '/../includes/config.php';    //gives a db as variable (not $db)

    //query to get events of the week
    //need to join the teacher name and the subject name and only from the right class
    $query = "SELECT * FROM `events` WHERE start_time>$weekStart AND start_time<$weekEnd";

    $result = mysqli_query(db, $query);
    $weekEvents = mysqli_fetch_all($result, MYSQLI_ASSOC);

    //close the db
    mysqli_close(db);

    return $weekEvents;
}

function makeEvent(){
//    $startTime;
//    $endTime;
//    $name;
//    $classId;
//    $teacherId;
//    $subjectId;
//    $location;

    require __DIR__ . '/../includes/config.php';
//    $query = ""
}
$weekEvents = getWeekEvents($weekOffset);


//get the array for the currentDaySchedule


?>
<!DOCTYPE html>
<html lang="nl">

<?php require __DIR__ . '/../includes/header.php'; ?>
<body>

<main>
    <section id="schedule-box">
        <h1>Rooster</h1>
        <p>uitleg</p>
        <table>
            <tr>
                <td>W<?= $currentWeekNumber ?></td>
                <th>Ma</th>
                <th>Di</th>
                <th>Wo</th>
                <th>Do</th>
                <th>Vr</th>
            </tr>
            <!--like 10 lesson hour blocks-->
            <!--put the events in the right place-->
            <?php
            //if the timestamp is in the array put in the info inside
            //else empty
            ?>
            <tr>
                <th>8:00</th>
                <td>
                    <!--subjectAbbreviation-->
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </table>
    </section>
    <section id="day-schedule list">
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

<?php require __DIR__ . '/../includes/footer.php'; ?>

</body>
</html>
