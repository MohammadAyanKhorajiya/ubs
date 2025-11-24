<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Panel</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<style>
body {
    background: #f7f7f7;
}

.admin-wrapper {
    display: flex;
}

/* Sidebar */
.admin-sidebar {
    width: 220px;
    background: #fff;
    min-height: 100vh;
    border-right: 1px solid #ddd;
    padding: 20px 15px;
}

.admin-sidebar ul {
    list-style: none;
    padding: 0;
}

.admin-sidebar ul li {
    padding: 10px 12px;
    border-radius: 6px;
    margin-bottom: 8px;
    font-weight: 500;
    cursor: pointer;
    transition: 0.3s;
}

.admin-sidebar ul li a {
    color: #111;
    text-decoration: none;
    display: block;
}

.admin-sidebar ul li:hover,
.admin-sidebar ul li.active {
    background: #e63946;
}

.admin-sidebar ul li:hover a,
.admin-sidebar ul li.active a {
    color: #fff;
}

.admin-content {
    flex: 1;
    padding: 25px 30px;
}

.admin-topbar {
    width: 100%;
    background: #fff;
    padding: 12px 25px;
    border-bottom: 1px solid #ddd;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.admin-topbar h4 {
    margin: 0;
    font-weight: 600;
    color: #e63946;
}

.logout-btn {
    color: #e63946;
    font-weight: 600;
    text-decoration: none;
}

.logout-btn:hover {
    text-decoration: underline;
}
</style>

</head>
<body>

<div class="admin-topbar">
    <div class="d-flex align-items-center gap-2 fs-3 text-danger">
        <i class="fa-solid fa-book"></i>
        <h2 class="text-decoration-none text-danger fw-bold m-0">Rebooks</h2></>
    </div>
    <a href="../logout.php" class="logout-btn">Logout</a>
</div>

<div class="admin-wrapper">

    <!-- SIDEBAR -->
    <div class="admin-sidebar">

        <ul>
            <li class="<?= basename($_SERVER['PHP_SELF'])=='dashboard.php'?'active':'' ?>">
                <a href="dashboard.php">Dashboard</a>
            </li>

            <li class="<?= basename($_SERVER['PHP_SELF'])=='manage_users.php'?'active':'' ?>">
                <a href="manage_users.php">Manage Users</a>
            </li>

            <li class="<?= basename($_SERVER['PHP_SELF'])=='manage_books.php'?'active':'' ?>">
                <a href="manage_books.php">Manage Books</a>
            </li>

            <li class="<?= basename($_SERVER['PHP_SELF'])=='manage_orders.php'?'active':'' ?>">
                <a href="manage_orders.php">Manage Orders</a>
            </li>

            <li class="<?= basename($_SERVER['PHP_SELF'])=='add_category.php'?'active':'' ?>">
                <a href="add_category.php">Add Category</a>
            </li>
        </ul>
    </div>

    <!-- CONTENT WRAPPER START -->
    <div class="admin-content">
