<?php
session_start();
include 'backend/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $book_id = intval($_GET['id']);

    // Ensure user owns the book
    $check = $conn->prepare("SELECT book_id FROM books WHERE book_id = ? AND user_id = ?");
    $check->bind_param("ii", $book_id, $user_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        // Delete book (book_images cascades automatically)
        $del = $conn->prepare("DELETE FROM books WHERE book_id = ?");
        $del->bind_param("i", $book_id);
        $del->execute();
    }
}

header("Location: profile.php?deleted=1");
exit();
?>
