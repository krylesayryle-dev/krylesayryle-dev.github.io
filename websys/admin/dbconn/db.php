<?php
$conn = new mysqli("localhost", "root", "", "electric_bill_portal");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
