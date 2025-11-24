<?php
include 'backend/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "login_required";
    exit;
}

$user_id = intval($_SESSION['user_id']);
$book_id = intval($_POST['book_id']);

$check = mysqli_query($conn, "SELECT * FROM wishlist WHERE id=$user_id AND book_id=$book_id");

if (mysqli_num_rows($check) > 0) {

    mysqli_query($conn, "DELETE FROM wishlist WHERE id=$user_id AND book_id=$book_id");
    echo "removed";

} else {

    mysqli_query($conn, "INSERT INTO wishlist (id, book_id) VALUES ($user_id, $book_id)");
    echo "added";

}
