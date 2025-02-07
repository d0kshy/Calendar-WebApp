<?php

$servername = "";
$username = "";
$password = "";
$dbname = "";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $crm = $_POST['CRM'];
    $date = $_POST['date'];
    $startTime = $_POST['startTime'];
    $endTime = $_POST['endTime'];
    $workerName = $_POST['workerName'];
    $notes = $_POST['notes'];

    $stmt = $conn->prepare("INSERT INTO events (CRM, date, startTime, endTime, workerName, notes) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $crm, $date, $startTime, $endTime, $workerName, $notes);
    
    if ($stmt->execute()) {
        echo "Success";
    } else {
        echo "Error: " . $stmt->error;
    }
    
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $events = [];

    if (isset($_GET['date'])) {
        $date = $_GET['date'];
        $stmt = $conn->prepare("SELECT * FROM events WHERE date = ?");
        $stmt->bind_param("s", $date);
    } else {
        $stmt = $conn->prepare("SELECT * FROM events"); // Get all events
    }

    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $events[] = $row;
        }

        echo json_encode($events);
    } else {
        echo json_encode(["error" => "Database error: " . $conn->error]);
    }

    $stmt->close();
}

$conn->close();
?>