<!DOCTYPE html>
<html lang="en">
<head>
    <script src="./script.js"></script>
    <meta charset="UTF-8">
    <title>Calendar</title>
</head>
<body>

<?php

$month = $_GET['month'] ?? date('m');
$year = $_GET['year'] ?? date('Y');

$month = (int)$month;
$year = (int)$year;

function generateCalendar(int $month, int $year): void {
    $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

    $formattedMonth = str_pad((string)$month, 2, "0", STR_PAD_LEFT);
    $firstDayOfMonth = strtotime("$year-$formattedMonth-01");
    $daysInMonth = date('t', $firstDayOfMonth);
    $startDay = (date('N', $firstDayOfMonth) - 1);
    $monthName = date('F', $firstDayOfMonth);

    echo "<h2 style='text-align: center;'>$monthName $year</h2>";
    echo "<table border='1' cellspacing='0' cellpadding='0' style='width: 100%; text-align: center; table-layout: fixed;'>";

    echo "<tr><th>Week number</th>";
    foreach ($daysOfWeek as $day) {
        echo "<th style='width: calc(100% / 8); height: 50px; background-color: #fff3bd;'>$day</th>";
    }
    echo "</tr><tr>";

    $currentDate = "$year-$formattedMonth-01";
    $isoWeekNumber = getISOWeekNumber($currentDate);
    echo "<td style='width: calc(100% / 8); height: 50px;'>$isoWeekNumber</td>";

    for ($i = 0; $i < $startDay; $i++) {
        echo "<td style='width: calc(100% / 8); height: 50px;'></td>";
    }

    for ($day = 1; $day <= $daysInMonth; $day++) {
        $currentDate = "$year-$formattedMonth-" . str_pad((string)$day, 2, "0", STR_PAD_LEFT);

        if (($startDay + $day - 1) % 7 == 0 && $day != 1) {
            echo "</tr><tr>";
            $isoWeekNumber = getISOWeekNumber($currentDate);
            echo "<td style='width: calc(100% / 8); height: 50px;'>$isoWeekNumber</td>";
        }

        echo "<td style='width: calc(100% / 8); height: 50px; cursor: pointer;'
                class='calendar-cell'
                data-date='$currentDate'
                onclick='showAppointments(\"$currentDate\")'>$day</td>";
    }

    $remainingDays = (7 - ($startDay + $daysInMonth) % 7) % 7;
    for ($i = 0; $i < $remainingDays; $i++) {
        echo "<td style='width: calc(100% / 8); height: 50px;'></td>";
    }

    echo "</tr>";
    echo "</table>";
}

function getISOWeekNumber($date) {
    $dateTime = new DateTime($date);
    return $dateTime->format("W");
}

echo '<form method="GET" style="margin-bottom: 20px; text-align: center;">';

echo '<select name="month" onchange="this.form.submit()" style="margin-right: 10px;">';
for ($m = 1; $m <= 12; $m++) {
    $formattedMonth = str_pad((string)$m, 2, "0", STR_PAD_LEFT);
    $monthName = date('F', strtotime("2023-$formattedMonth-01"));
    $selected = ($m == $month) ? 'selected' : '';
    echo "<option value=\"$m\" $selected>$monthName</option>";
}
echo '</select>';

echo '<select name="year" onchange="this.form.submit()">';
for ($y = 2024; $y <= 2035; $y++) {
    $selected = ($y == $year) ? 'selected' : '';
    echo "<option value=\"$y\" $selected>$y</option>";
}
echo '</select>';

echo '</form>';

generateCalendar($month, $year);
?>

<div id="appointmentModal" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 300px; padding: 20px; background: white; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2); z-index: 1000;">
    <h3 id="modalDate"></h3>
    <p id="modalContent">No appointments for this day.</p>
    <button onclick="addAppointment()">Add Appointment</button>
    <button onclick="closeModal()">Close</button>
</div>

<div id="addAppointmentModal" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 300px; padding: 20px; background: white; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2); z-index: 1001;">
    <h3>Add Appointment</h3>
    <form id="addAppointmentForm">
        <label for="CRM">CRM:</label><br>
        <input type="text" id="CRM" name="CRM" required><br><br>

        <label for="date">Date:</label><br>
        <input type="date" id="date" name="date" required><br><br>

        <label for="startTime">Start Time:</label><br>
        <input type="time" id="startTime" name="startTime" required><br><br>

        <label for="endTime">End Time:</label><br>
        <input type="time" id="endTime" name="endTime" required><br><br>

        <label for="workerName">Worker Name:</label><br>
        <input type="text" id="workerName" name="workerName" required><br><br>

        <label for="notes">Notes:</label><br>
        <textarea id="notes" name="notes" rows="4" style="width: 100%;"></textarea><br><br>

        <input type="hidden" id="appointmentDate" name="appointmentDate">
        <button type="button" onclick="saveAppointment()">Save</button>
        <button type="button" onclick="closeAddAppointmentModal()">Cancel</button>
    </form>
</div>

<script>
   function showAppointments(date) {
    document.getElementById('modalDate').innerText = `Appointments for ${date}`;
    document.getElementById('modalContent').innerText = "Loading...";

    fetch(`save_event.php?date=${date}`)
        .then(response => response.json())
        .then(data => {
            let content = data.length > 0 
                ? data.map(event => 
                    `CRM: ${event.CRM}<br>
                     Time: ${event.startTime} - ${event.endTime}<br>
                     Worker: ${event.workerName}<br>
                     Notes: ${event.notes}<br><hr>`).join("")
                : "No appointments for this day.";

            document.getElementById('modalContent').innerHTML = content;
        })
        .catch(error => {
            console.error("Error fetching appointments:", error);
            document.getElementById('modalContent').innerText = "Error loading appointments.";
        });

    document.getElementById('appointmentModal').style.display = 'block';
    document.getElementById('appointmentDate').value = date;
}

function saveAppointment() {
    let form = document.getElementById('addAppointmentForm');
    let formData = new FormData(form);

    fetch("save_event.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(result => {
        if (result.trim() === "Success") {
            alert("Appointment saved!");
            
            form.reset();  

            closeAddAppointmentModal(); 
        } else {
            alert("Error saving appointment: " + result);
        }
    })
    .catch(error => console.error("Error:", error));
}

document.addEventListener("DOMContentLoaded", function () {
    loadEvents();
});

function loadEvents() {
    fetch("save_event.php") // Fetch all events
        .then(response => response.json())
        .then(data => {
            document.querySelectorAll(".calendar-cell").forEach(cell => {
                const date = cell.dataset.date;
                const eventList = data.filter(event => event.date === date);
                
                cell.querySelector(".event-container")?.remove();

                if (eventList.length > 0) {
                    let eventHTML = eventList.map(event => 
                        `<div style="font-size: 12px; background: #ffdd57; padding: 2px; margin: 2px; border-radius: 3px;">
                            ${event.startTime} - ${event.workerName}
                        </div>`
                    ).join("");

                    // ✅ Add events inside the cell
                    cell.innerHTML += `<div class="event-container">${eventHTML}</div>`;
                }
            });
        })
        .catch(error => console.error("Error loading events:", error));
}

document.addEventListener("DOMContentLoaded", function () {
    loadEvents();
});

function closeModal() {
    document.getElementById('appointmentModal').style.display = 'none';
}

function addAppointment() {
    let selectedDate = document.getElementById('appointmentDate').value;

    if (!selectedDate) {
        alert("Please select a date first.");
        return;
    }

    document.getElementById('date').value = selectedDate;
    document.getElementById('addAppointmentModal').style.display = 'block';
    closeModal();
}

function closeAddAppointmentModal() {
    document.getElementById('addAppointmentModal').style.display = 'none';
}

</script>

</body>
</html>
