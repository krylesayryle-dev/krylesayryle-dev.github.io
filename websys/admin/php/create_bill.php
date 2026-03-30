<?php
session_start();
include "../dbconn/db.php";

$_SESSION['admin_id'] = 1;

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'] ?? '';
    $property_name = $_POST['property_name'] ?? '';
    $property_address = $_POST['property_address'] ?? '';
    $billing_month = $_POST['billing_month'] ?? '';
    $period_from = $_POST['period_from'] ?? '';
    $period_to = $_POST['period_to'] ?? '';
    $meter_brand = $_POST['meter_brand'] ?? '';
    $meter_serial_no = $_POST['meter_serial_no'] ?? '';
    $multiplier = $_POST['multiplier'] ?? '';
    $previous_reading = $_POST['previous_reading'] ?? '';
    $present_reading = $_POST['present_reading'] ?? '';
    $generation_charge = $_POST['generation_charge'] ?? '';
    $transmission_charge = $_POST['transmission_charge'] ?? '';
    $system_loss_charge = $_POST['system_loss_charge'] ?? '';
    $distribution_charge = $_POST['distribution_charge'] ?? '';
    $supply_charge = $_POST['supply_charge'] ?? '';
    $metering_charge = $_POST['metering_charge'] ?? '';
    $lifeline_subsidy = $_POST['lifeline_subsidy'] ?? '';
    $vat = $_POST['vat'] ?? '';
    $due_date = $_POST['due_date'] ?? '';

    if (!$user_id || !$property_name || !$billing_month || !$period_from || !$period_to || !$due_date) {
        $error = "Please fill all required fields.";
    } elseif ((float)$present_reading < (float)$previous_reading) {
        $error = "Present reading must be greater than or equal to previous reading.";
    } else {
        $stmt = $conn->prepare("
            INSERT INTO electric_bills (
                user_id, property_name, property_address, billing_month, period_from, period_to,
                meter_brand, meter_serial_no, multiplier, previous_reading, present_reading,
                generation_charge, transmission_charge, system_loss_charge, distribution_charge,
                supply_charge, metering_charge, lifeline_subsidy, vat, due_date
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        if ($stmt !== false) {
            $stmt->bind_param(
                str_repeat('s', 20),
                $user_id,
                $property_name,
                $property_address,
                $billing_month,
                $period_from,
                $period_to,
                $meter_brand,
                $meter_serial_no,
                $multiplier,
                $previous_reading,
                $present_reading,
                $generation_charge,
                $transmission_charge,
                $system_loss_charge,
                $distribution_charge,
                $supply_charge,
                $metering_charge,
                $lifeline_subsidy,
                $vat,
                $due_date
            );

            if ($stmt->execute()) {
                $success = "Electric bill successfully created.";
            } else {
                $error = "Database error: " . htmlspecialchars($stmt->error);
            }
        } else {
            $error = "Prepare failed: " . htmlspecialchars($conn->error);
        }
    }
}

$users_result = $conn->query("SELECT user_id, full_name FROM users ORDER BY full_name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Create Electric Bill</title>
    <link rel="stylesheet" href="../css/create_bill.css" />
</head>
<body>
    <form method="post" action="">
        <h2>Create Electric Bill</h2>

        <?php if ($error): ?>
            <div class="message error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="message success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <label for="user_id">User:</label>
        <select name="user_id" id="user_id" required>
            <option value="">-- Select User --</option>
            <?php while ($user = $users_result->fetch_assoc()): ?>
                <option value="<?= htmlspecialchars($user['user_id']) ?>"><?= htmlspecialchars($user['full_name']) ?></option>
            <?php endwhile; ?>
        </select>

        <label for="property_name">Property Name:</label>
        <input type="text" name="property_name" id="property_name" required />

        <label for="property_address">Property Address:</label>
        <textarea name="property_address" id="property_address" required></textarea>

        <label for="billing_month">Billing Month:</label>
        <input type="text" name="billing_month" id="billing_month" placeholder="e.g. January 2026" required />

        <label for="period_from">Period From:</label>
        <input type="date" name="period_from" id="period_from" required />

        <label for="period_to">Period To:</label>
        <input type="date" name="period_to" id="period_to" required />

        <label for="meter_brand">Meter Brand:</label>
        <input type="text" name="meter_brand" id="meter_brand" />

        <label for="meter_serial_no">Meter Serial Number:</label>
        <input type="text" name="meter_serial_no" id="meter_serial_no" />

        <label for="multiplier">Multiplier:</label>
        <input type="number" name="multiplier" id="multiplier" step="0.01" value="1.00" />

        <label for="previous_reading">Previous Reading:</label>
        <input type="number" name="previous_reading" id="previous_reading" step="0.01" required />

        <label for="present_reading">Present Reading:</label>
        <input type="number" name="present_reading" id="present_reading" step="0.01" required />

        <label for="generation_charge">Generation Charge:</label>
        <input type="number" name="generation_charge" id="generation_charge" step="0.01" value="0" />

        <label for="transmission_charge">Transmission Charge:</label>
        <input type="number" name="transmission_charge" id="transmission_charge" step="0.01" value="0" />

        <label for="system_loss_charge">System Loss Charge:</label>
        <input type="number" name="system_loss_charge" id="system_loss_charge" step="0.01" value="0" />

        <label for="distribution_charge">Distribution Charge:</label>
        <input type="number" name="distribution_charge" id="distribution_charge" step="0.01" value="0" />

        <label for="supply_charge">Supply Charge:</label>
        <input type="number" name="supply_charge" id="supply_charge" step="0.01" value="0" />

        <label for="metering_charge">Metering Charge:</label>
        <input type="number" name="metering_charge" id="metering_charge" step="0.01" value="0" />

        <label for="lifeline_subsidy">Lifeline Subsidy:</label>
        <input type="number" name="lifeline_subsidy" id="lifeline_subsidy" step="0.01" value="0" />

        <label for="vat">VAT:</label>
        <input type="number" name="vat" id="vat" step="0.01" value="0" />

        <label for="due_date">Due Date:</label>
        <input type="date" name="due_date" id="due_date" required />

        <button type="submit">Create Bill</button>
        <button type="button" onclick="window.location.href='dashboard.php'">Back to Dashboard</button>
    </form>
</body>
</html>
