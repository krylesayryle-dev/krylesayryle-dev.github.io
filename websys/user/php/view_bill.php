<?php
session_start();
include "../dbconn/db.php";

$bill_id = $_GET['id'];

$stmt = $conn->prepare("
    SELECT eb.*, u.full_name
    FROM electric_bills eb
    JOIN users u ON eb.user_id = u.user_id
    WHERE eb.bill_id = ?
");
$stmt->bind_param("i", $bill_id);
$stmt->execute();
$bill = $stmt->get_result()->fetch_assoc();

if (!$bill) {
    die("Bill not found");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Bill</title>
    <link rel="stylesheet" href="../css/user.css">
    <style>
        .bill {
            max-width: 700px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        .back-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s, transform 0.2s;
        }
        .back-button:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

<div class="bill">
    <h2>Electric Bill Receipt</h2>

    <p><strong>User:</strong> <?= htmlspecialchars($bill['full_name']) ?></p>
    <p><strong>Property:</strong> <?= htmlspecialchars($bill['property_name']) ?></p>
    <p><strong>Address:</strong> <?= htmlspecialchars($bill['property_address']) ?></p>
    <p><strong>Billing Month:</strong> <?= htmlspecialchars($bill['billing_month']) ?></p>
    <p><strong>Period:</strong> <?= htmlspecialchars($bill['period_from']) ?> to <?= htmlspecialchars($bill['period_to']) ?></p>

    <hr>

    <p>Meter Brand: <?= htmlspecialchars($bill['meter_brand']) ?></p>
    <p>Meter Serial No: <?= htmlspecialchars($bill['meter_serial_no']) ?></p>
    <p>Multiplier: <?= htmlspecialchars($bill['multiplier']) ?></p>

    <hr>

    <p>Previous Reading: <?= htmlspecialchars($bill['previous_reading']) ?></p>
    <p>Present Reading: <?= htmlspecialchars($bill['present_reading']) ?></p>
    <p>kWh Used: <?= htmlspecialchars($bill['kwh_used']) ?></p>

    <hr>

    <p>Generation Charge: ₱<?= number_format((float)$bill['generation_charge'], 2) ?></p>
    <p>Transmission Charge: ₱<?= number_format((float)$bill['transmission_charge'], 2) ?></p>
    <p>System Loss Charge: ₱<?= number_format((float)$bill['system_loss_charge'], 2) ?></p>
    <p>Distribution Charge: ₱<?= number_format((float)$bill['distribution_charge'], 2) ?></p>
    <p>Supply Charge: ₱<?= number_format((float)$bill['supply_charge'], 2) ?></p>
    <p>Metering Charge: ₱<?= number_format((float)$bill['metering_charge'], 2) ?></p>
    <p>Lifeline Subsidy: ₱<?= number_format((float)$bill['lifeline_subsidy'], 2) ?></p>
    <p>VAT: ₱<?= number_format((float)$bill['vat'], 2) ?></p>

    <hr>

    <p><strong>Total Amount:</strong> ₱<?= number_format((float)$bill['total_amount'], 2) ?></p>
    <p><strong>Status:</strong> <?= htmlspecialchars($bill['bill_status']) ?></p>
    <p><strong>Due Date:</strong> <?= htmlspecialchars($bill['due_date']) ?></p>

    <a href="dashboard.php" class="back-button">← Back to Dashboard</a>
</div>

</body>
</html>
    