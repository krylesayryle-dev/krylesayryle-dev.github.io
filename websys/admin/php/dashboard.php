<?php
session_start();
include "../dbconn/db.php";

$_SESSION['admin_id'] = 1;

$total_users = $conn->query("SELECT COUNT(*) AS count FROM users")->fetch_assoc()['count'];
$total_bills = $conn->query("SELECT COUNT(*) AS count FROM electric_bills")->fetch_assoc()['count'];
$unpaid_bills = $conn->query("SELECT COUNT(*) AS count FROM electric_bills WHERE bill_status='UNPAID'")->fetch_assoc()['count'];
$overdue_bills = $conn->query("SELECT COUNT(*) AS count FROM electric_bills WHERE bill_status='OVERDUE'")->fetch_assoc()['count'];

$latest_users = $conn->query("SELECT user_id, full_name, username, created_at FROM users ORDER BY created_at DESC LIMIT 5");
$latest_bills = $conn->query("
    SELECT eb.bill_id, u.full_name, eb.property_name, eb.billing_month, eb.total_amount, eb.bill_status, eb.generated_date
    FROM electric_bills eb
    JOIN users u ON eb.user_id = u.user_id
    ORDER BY eb.generated_date DESC
    LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Admin Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="../css/admin.css" />
<style>
.status-form select {
    padding: 4px 8px;
    font-size: 1rem;
    border-radius: 4px;
    border: 1px solid #ccc;
    cursor: pointer;
}
.message.success {
    background-color: #d4edda;
    color: #155724;
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 5px;
}
.bills-table-container {
    flex: 1 1 100%;
    max-width: 100%;
    overflow-x: auto;
    min-width: 320px;
}
.bills-table-container table {
    min-width: 850px;
    width: 100%;
}
.logout-footer-btn {
    background-color: #dc3545;
    color: white;
    padding: 8px 16px;
    border-radius: 5px;
    text-decoration: none;
    font-weight: 600;
    transition: 0.3s;
}

.logout-footer-btn:hover {
    background-color: #b02a37;
}
</style>
</head>
<body>
<header class="header">
    <div class="container">
        <h1 class="logo">Zamsureco 10 Ultra 5g Pro Max </h1>
        <nav class="nav-bar">
    <div class="nav-left">
        <a href="dashboard.php" class="nav-link active">Dashboard</a>
        <a href="create_bill.php" class="nav-link">Create Bill</a>
    </div>


</nav>
    </div>
</header>

<main class="container">
    <?php if (isset($_GET['msg'])): ?>
        <div class="message success"><?= htmlspecialchars($_GET['msg']) ?></div>
    <?php endif; ?>

    <section class="stats-grid">
        <div class="stat-card">
            <h3>Total Users</h3>
            <p><?= $total_users ?></p>
        </div>
        <div class="stat-card">
            <h3>Total Bills</h3>
            <p><?= $total_bills ?></p>
        </div>
        <div class="stat-card warning">
            <h3>Unpaid Bills</h3>
            <p><?= $unpaid_bills ?></p>
        </div>
        <div class="stat-card danger">
            <h3>Overdue Bills</h3>
            <p><?= $overdue_bills ?></p>
        </div>
    </section>

    <section class="tables-section">
        <div class="table-container">
            <h2>Latest 5 Users</h2>
            <table>
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Full Name</th>
                        <th>Username</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = $latest_users->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['user_id']) ?></td>
                        <td><?= htmlspecialchars($user['full_name']) ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['created_at']) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="table-container bills-table-container">
            <h2>Latest 5 Bills</h2>
            <table>
                <thead>
                    <tr>
                        <th>Bill ID</th>
                        <th>User</th>
                        <th>Property</th>
                        <th>Billing Month</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Change Status</th>
                        <th>Generated Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($bill = $latest_bills->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($bill['bill_id']) ?></td>
                        <td><?= htmlspecialchars($bill['full_name']) ?></td>
                        <td><?= htmlspecialchars($bill['property_name']) ?></td>
                        <td><?= htmlspecialchars($bill['billing_month']) ?></td>
                        <td><?= number_format($bill['total_amount'], 2) ?></td>
                        <td><span class="status <?= strtolower($bill['bill_status']) ?>"><?= htmlspecialchars($bill['bill_status']) ?></span></td>
                        <td>
                            <form method="post" action="update_bill_status.php" class="status-form">
                                <input type="hidden" name="bill_id" value="<?= $bill['bill_id'] ?>">
                                <select name="bill_status" onchange="this.form.submit()">
                                    <?php 
                                        $statuses = ['UNPAID', 'PAID', 'OVERDUE'];
                                        foreach ($statuses as $status): 
                                            $selected = ($bill['bill_status'] === $status) ? 'selected' : '';
                                    ?>
                                        <option value="<?= $status ?>" <?= $selected ?>><?= $status ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                        </td>
                        <td><?= htmlspecialchars($bill['generated_date']) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<footer class="footer">
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <span>&copy; <?= date("Y") ?> Zamsureco 10 Ultra 5g Pro Max. All rights reserved.</span>
            
            <a href="logout.php" class="logout-footer-btn">Logout</a>
        </div>
    </div>
</footer>
</body>
</html>
