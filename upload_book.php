<?php
session_start();
include 'backend/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $author = trim($_POST['author']);
    $isbn = trim($_POST['isbn']);
    $condition = $_POST['condition'];
    $category_id = $_POST['category_id'];
    $language = trim($_POST['language']);
    $quantity = (int)$_POST['quantity'];
    $type = $_POST['type'];
    $price = $type === 'Paid' ? floatval($_POST['price']) : 0.00;

    $stmt = $conn->prepare("INSERT INTO books (user_id, title, description, author, isbn, `condition`, category_id, language, quantity, `type`, price) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssisdss", $user_id, $title, $description, $author, $isbn, $condition, $category_id, $language, $quantity, $type, $price);
    if ($stmt->execute()) {
        $book_id = $stmt->insert_id;

        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $targetDir = "uploads/books/";
            if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

            $filename = time() . "_" . basename($_FILES["image"]["name"]);
            $targetFile = $targetDir . $filename;

            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                $img = $conn->prepare("INSERT INTO book_images (book_id, path) VALUES (?, ?)");
                $img->bind_param("is", $book_id, $targetFile);
                $img->execute();
            }
        }

        header("Location: profile.php");
        exit();
    } else {
        echo "Error inserting book.";
    }
}
?>
