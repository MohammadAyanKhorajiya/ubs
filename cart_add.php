<?php
include 'backend/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "login_required";
    exit;
}

$user_id = intval($_SESSION['user_id']);
$book_id = intval($_POST['book_id']);

$check = $conn->query("SELECT * FROM cart WHERE user_id=$user_id AND book_id=$book_id");

if ($check->num_rows > 0) {
    echo "exists";
    exit;
}

$conn->query("INSERT INTO cart (user_id, book_id, quantity) VALUES ($user_id, $book_id, 1)");
echo "added";
