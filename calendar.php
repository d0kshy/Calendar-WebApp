<?php
function generateCalendar($month, $year) {

    $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

    $firstDayOfMonth = strtotime("$year-$month-01");
    $daysInMonth = date('t', $firstDayOfMonth);

    $startDay = (date('N', $firstDayOfMonth) - 1);

    $monthName = date('F', $firstDayOfMonth);
    echo "<h2>$monthName $year</h2>";

    echo "<table border='1' cellspacing='0' cellpadding='5'>";

    echo "<tr><th>ISO Week</th>";
    foreach ($daysOfWeek as $day) {
        echo "<th>$day</th>";
    }
    echo "</tr>";

    echo "<tr>";

    $firstThursday = strtotime("Thursday this week", $firstDayOfMonth);
    $currentWeek = date('W', $firstThursday);

    echo "<td>$currentWeek</td>";

    for ($i = 0; $i < $startDay; $i++) {
        echo "<td></td>";
    }

    for ($day = 1; $day <= $daysInMonth; $day++) {
        $currentDate = strtotime("$year-$month-$day");

        if (($startDay + $day - 1) % 7 == 0 && $day != 1) {
            echo "</tr><tr>";
            $currentWeek = date('W', strtotime("Thursday this week", $currentDate));
            echo "<td>$currentWeek</td>";
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

generateCalendar(1, 2025);
?>
