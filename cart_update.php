<?php
include 'backend/config.php';
session_start();

$cart_id = intval($_POST['cart_id']);
$action = $_POST['action'];

$q = $conn->query("SELECT quantity FROM cart WHERE cart_id=$cart_id");
$row = $q->fetch_assoc();
$qty = $row['quantity'];

if ($action === "plus") $qty++;
if ($action === "minus" && $qty > 1) $qty--;

$conn->query("UPDATE cart SET quantity=$qty WHERE cart_id=$cart_id");
echo "ok";
