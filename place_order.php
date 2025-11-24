<?php
session_start();
include 'backend/config.php';
include 'backend/stock_manager.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$buyer_id  = $_SESSION['user_id'];
$book_id   = intval($_POST['book_id']);   
$seller_id = intval($_POST['seller_id']);
$method    = $_POST['payment_method'];

// ✅ Now it's safe to reduce stock
reduceStock($book_id);

$q = $conn->query("SELECT price, type FROM books WHERE book_id=$book_id");
$b = $q->fetch_assoc();
$amount = ($b['type'] == "Donated") ? 0 : $b['price'];

$conn->query("INSERT INTO orders (book_id, buyer_id, seller_id) VALUES ($book_id, $buyer_id, $seller_id)");
$order_id = $conn->insert_id;

$conn->query("INSERT INTO payments (order_id, amount, method, status) 
              VALUES ($order_id, $amount, '$method', 'Paid')");

header("Location: order_success.php?order_id=".$order_id);
exit();
?>
