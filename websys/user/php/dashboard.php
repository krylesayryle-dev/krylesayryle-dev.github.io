<?php
session_start();
include "../dbconn/db.php";

$_SESSION['user_id'] = 1;
$user_id = $_SESSION['user_id'];

// Fetch user full name
$user_query = $conn->prepare("SELECT full_name FROM users WHERE user_id = ?");
$user_query->bind_param("i", $user_id);
$user_query->execute();
$user_result = $user_query->get_result();
$user = $user_result->fetch_assoc();

// Fetch user's bills
$bills = $conn->prepare("
    SELECT bill_id, property_name, billing_month, total_amount, bill_status, due_date
    FROM electric_bills
    WHERE user_id = ?
    ORDER BY generated_date DESC
");
$bills->bind_param("i", $user_id);
$bills->execute();
$result = $bills->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Dashboard</title>

<link rel="stylesheet" href="../css/style.css">

<style>

/* HEADER DESIGN */
header{
display:flex;
justify-content:space-between;
align-items:center;
background:#2563eb;
color:white;
padding:20px 40px;
border-radius:8px;
box-shadow:0 4px 6px rgba(0,0,0,0.1);
}

/* LOGOUT BUTTON */
.logout-btn{
background:#ef4444;
color:white;
padding:10px 20px;
border-radius:6px;
text-decoration:none;
font-weight:600;
transition:0.3s;
}

.logout-btn:hover{
background:#b91c1c;
transform:translateY(-2px);
}

</style>

</head>

<body>

<header>
<h1>Welcome, <?= htmlspecialchars($user['full_name']) ?></h1>

<a href="logout.php" class="logout-btn">Logout</a>

</header>

<div class="container">

<table>

<thead>
<tr>
<th>Property</th>
<th>Billing Month</th>
<th>Total Amount</th>
<th>Status</th>
<th>Due Date</th>
<th>Action</th>
</tr>
</thead>

<tbody>

<?php while ($bill = $result->fetch_assoc()): ?>

<tr>

<td><?= htmlspecialchars($bill['property_name']) ?></td>

<td><?= htmlspecialchars($bill['billing_month']) ?></td>

<td>₱<?= number_format((float)$bill['total_amount'], 2) ?></td>

<td>
<span class="status <?= htmlspecialchars($bill['bill_status']) ?>">
<?= htmlspecialchars($bill['bill_status']) ?>
</span>
</td>

<td><?= htmlspecialchars($bill['due_date']) ?></td>

<td>
<a href="view_bill.php?id=<?= $bill['bill_id'] ?>">View</a>
</td>

</tr>

<?php endwhile; ?>

</tbody>
</table>

</div>

</body>
</html>