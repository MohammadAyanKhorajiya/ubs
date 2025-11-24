<?php
session_start();
include 'backend/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$method = $_POST['payment_method'];

// Fetch all cart items
$cart = $conn->query("
    SELECT c.*, b.price, b.type, b.user_id AS seller_id
    FROM cart c
    JOIN books b ON c.book_id=b.book_id
    WHERE c.user_id=$user_id
");

while ($item = $cart->fetch_assoc()) {

    $book_id = $item['book_id'];
    $seller_id = $item['seller_id'];
    $amount = ($item['type']=="Donated") ? 0 : $item['price'];

    // Insert order
    $conn->query("
        INSERT INTO orders (book_id, buyer_id, seller_id) 
        VALUES ($book_id, $user_id, $seller_id)
    ");

    $order_id = $conn->insert_id;

    // Payment entry
    $conn->query("
        INSERT INTO payments (order_id, amount, method, status)
        VALUES ($order_id, '$amount', '$method', 'Paid')
    ");
}

// Clear cart
$conn->query("DELETE FROM cart WHERE id=$user_id");

// Redirect to Success Page
header("Location: success_checkout.php?order_id=$order_id");
exit();

?>
