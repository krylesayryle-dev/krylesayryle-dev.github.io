<?php
include "../dbconn/db.php";
include "auth.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bill_id = $_POST['bill_id'] ?? '';
    $bill_status = $_POST['bill_status'] ?? '';

    $valid_statuses = ['UNPAID', 'PAID', 'OVERDUE'];

    if ($bill_id && in_array($bill_status, $valid_statuses)) {
        $stmt = $conn->prepare("UPDATE electric_bills SET bill_status = ? WHERE bill_id = ?");
        $stmt->bind_param("si", $bill_status, $bill_id);

        if ($stmt->execute()) {
            header("Location: dashboard.php?msg=Status updated successfully");
            exit;
        } else {
            die("Database error: " . $stmt->error);
        }
    } else {
        die("Invalid input.");
    }
} else {
    header("Location: dashboard.php");
    exit;
}
