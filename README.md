# Calendar Appointment System

### Overview

This is a simple and interactive Calendar Appointment System that allows users to schedule, view, and manage appointments directly from a web-based calendar. 
Users can:

- Click on a calendar cell to view existing appointments.

- Add new appointments with details like date, time, worker name, and notes.

- Automatically save appointments to a MySQL database.

- View saved appointments directly inside the calendar cells.

### How to Use

1️. Setup Database

You need to create a MySQL database and import the required table structure.

Create Database and Table

 CREATE DATABASE calendar;
 USE calendar;
 CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    CRM VARCHAR(50) NOT NULL,
    date DATE NOT NULL,
    startTime TIME NOT NULL,
    endTime TIME NOT NULL,
    workerName VARCHAR(100) NOT NULL,
    notes TEXT
 );

2️. Configure Database Connection

Edit save_event.php and update the database credentials:

$servername = "your_server";
$username = "your_username";
$password = "your_password";
$dbname = "your_dbname"; 

3️. Run the Application

Place all files in your local server (e.g., XAMPP/Apache).

Open the project in your browser (http://localhost/calendar.php).

Start adding and managing your appointments!

### 📌 Features

Saves appointments to MySQL database 
Displays events directly inside calendar cells

🛠️ Technologies Used

PHP (Backend & Database interactions)

JavaScript (Dynamic UI updates)

MySQL (Data storage)

HTML & CSS (Frontend design)

### ❓ Troubleshooting

If the calendar doesn’t display events, ensure:

1. The database connection details are correct.

2. The events table exists and contains data.

If the calendar doesn’t save appointments, check:

1. save_event.php handles POST requests correctly.

2. Your database allows write operations.