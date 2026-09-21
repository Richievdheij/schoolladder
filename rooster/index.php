<?php

$pageTitle = 'Rooster';
//get the events of the week
//get the events of the day


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
                <th>Ma</th>
                <th>Di</th>
                <th>Wo</th>
                <th>Do</th>
                <th>Vr</th>
            </tr>
            <!--like 10 lesson hour blocks-->
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
