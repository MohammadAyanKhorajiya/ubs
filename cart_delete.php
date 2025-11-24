<?php
include 'backend/config.php';
session_start();

$cart_id = intval($_POST['cart_id']);
$conn->query("DELETE FROM cart WHERE cart_id=$cart_id");
echo "deleted";
