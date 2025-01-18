<?php
function generateCalendar($month, $year) {
    $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

    $formattedMonth = str_pad($month, 2, "0", STR_PAD_LEFT);
    $firstDayOfMonth = strtotime("$year-$formattedMonth-01");
    $daysInMonth = date('t', $firstDayOfMonth);

    $startDay = (date('N', $firstDayOfMonth) - 1);

    $monthName = date('F', $firstDayOfMonth);
    echo "<h2>$monthName $year</h2>";

    echo "<table border='1' cellspacing='0' cellpadding='5'>";

    echo "<tr><th>Week number</th>";
    foreach ($daysOfWeek as $day) {
        echo "<th>$day</th>";
    }
    echo "</tr>";

    echo "<tr>";

    $currentDate = "$year-$formattedMonth-01";
    $isoWeekNumber = getISOWeekNumber($currentDate);
    echo "<td>$year-$formattedMonth-$isoWeekNumber</td>";

    for ($i = 0; $i < $startDay; $i++) {
        echo "<td></td>";
    }

    for ($day = 1; $day <= $daysInMonth; $day++) {
        $currentDate = "$year-$formattedMonth-" . str_pad($day, 2, "0", STR_PAD_LEFT);

        if (($startDay + $day - 1) % 7 == 0 && $day != 1) {
            echo "</tr><tr>";
            $isoWeekNumber = getISOWeekNumber($currentDate);
            echo "<td>$year-$formattedMonth-$isoWeekNumber</td>";
        }

        echo "<td>$day</td>";
    }

    $remainingDays = (7 - ($startDay + $daysInMonth) % 7) % 7;
    for ($i = 0; $i < $remainingDays; $i++) {
        echo "<td></td>";
    }

    echo "</tr>";
    echo "</table>";
}

function getISOWeekNumber($date) {
    $dateTime = new DateTime($date);
    return $dateTime->format("W");
}

generateCalendar(10, 2025);
?>