<?php

$pageTitle = 'Rooster';

//connect db
require __DIR__ . '/../includes/config.php';

//query to get events of the week
$currentDay = 0;
//from the day get the week start and end
$weekStart = 0;
$weekEnd = 0;
$query = "SELECT * FROM `events` WHERE start_time > $weekStart AND end_time < $weekEnd";
$result = mysqli_query($db, $query);

//get the array for the currentDaySchedule



?>
<!DOCTYPE html>
<html lang="nl">

<?php require __DIR__ . '/../includes/header.php'; ?>
<body>

<main>
    <?=$result?>
    <section id="schedule-box">
        <h1>Rooster</h1>
        <p>uitleg</p>
        <table>
            <tr>
                <th>Ma</th>
                <th>Di</th>
                <th>Wo</th>
                <th>Do</th>
                <th>Vr</th>
            </tr>
            <!--like 10 lesson hour blocks-->
            <!--put the the events in the right place-->
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
        //foreach currentDayschedule as here-under
        ?>
        <div>
            <h3>vaknaam/eventnaam</h3>
            <p>docent - locatie</p>
            <h4>begintijd - eindtijd</h4>
        </div>
    </section>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>

</body>
</html>
