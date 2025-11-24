<?php
session_start();
include 'backend/config.php';
include 'backend/stock_manager.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_id'])) {

    $book_id = intval($_POST['book_id']);
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $author = trim($_POST['author']);
    $isbn = trim($_POST['isbn']);
    $condition = $_POST['condition'];
    $category_id = $_POST['category_id'];
    $language = trim($_POST['language']);
    $quantity = (int)$_POST['quantity'];   // <-- NEW QUANTITY IS HERE
    $type = $_POST['type'];
    $price = $type === 'Paid' ? floatval($_POST['price']) : 0.00;

    // ✅ FIX: NOW STOCK UPDATED AFTER WE HAVE $book_id & $quantity
    updateStockOnEdit($book_id, $quantity);

    $stmt = $conn->prepare("
        UPDATE books SET title=?, description=?, author=?, isbn=?, `condition`=?, 
        category_id=?, language=?, quantity=?, `type`=?, price=? 
        WHERE book_id=? AND user_id=?
    ");
    $stmt->bind_param(
        "sssssisdsdii",
        $title, $description, $author, $isbn, $condition,
        $category_id, $language, $quantity, $type, $price, $book_id, $user_id
    );

    if ($stmt->execute()) {

        // Image update
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {

            $targetDir = "uploads/books/";
            if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

            $filename = time() . "_" . basename($_FILES["image"]["name"]);
            $targetFile = $targetDir . $filename;

            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {

                $conn->query("DELETE FROM book_images WHERE book_id = $book_id");

                $img = $conn->prepare("INSERT INTO book_images (book_id, path) VALUES (?, ?)");
                $img->bind_param("is", $book_id, $targetFile);
                $img->execute();
            }
        }

        header("Location: profile.php?updated_book=1");
        exit();
    } else {
        echo "Error updating book.";
    }
}
?>
