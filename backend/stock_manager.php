<?php
// stock_manager.php
include 'config.php';

// -------------------------------
// Reduce Stock on Order Confirm
// -------------------------------
function reduceStock($book_id) {
    global $conn;

    $q = $conn->query("SELECT quantity FROM books WHERE book_id=$book_id");
    if (!$q || $q->num_rows == 0) return;

    $row = $q->fetch_assoc();
    $old_qty = (int)$row['quantity'];
    $new_qty = $old_qty - 1;

    if ($new_qty <= 0) {
        $conn->query("UPDATE books SET quantity=0, status='Hidden' WHERE book_id=$book_id");
    } else {
        $conn->query("UPDATE books SET quantity=$new_qty, status='Available' WHERE book_id=$book_id");
    }
}

// -------------------------------
// Increase Stock on Order Cancel
// -------------------------------
function increaseStock($book_id) {
    global $conn;

    $q = $conn->query("SELECT quantity FROM books WHERE book_id=$book_id");
    if (!$q || $q->num_rows == 0) return;

    $row = $q->fetch_assoc();
    $qty = (int)$row['quantity'] + 1;

    $conn->query("UPDATE books SET quantity=$qty, status='Available' WHERE book_id=$book_id");
}

// -------------------------------
// Update Stock If Seller Edits Book Quantity
// -------------------------------
function updateStockOnEdit($book_id, $new_qty) {
    global $conn;

    if ($new_qty <= 0) {
        $conn->query("UPDATE books SET quantity=0, status='Hidden' WHERE book_id=$book_id");
    } else {
        $conn->query("UPDATE books SET quantity=$new_qty, status='Available' WHERE book_id=$book_id");
    }
}
?>
