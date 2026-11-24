<?php
$conn = new mysqli("localhost", "root", "", "clinic_queue");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>