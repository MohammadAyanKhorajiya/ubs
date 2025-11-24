<?php
session_start();
include 'backend/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Order ID from URL
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

// Fetch order + book details
$query = $conn->query("
    SELECT o.order_id, o.order_date, 
           b.title, 
           (SELECT path FROM book_images WHERE book_id=b.book_id LIMIT 1) AS image_path
    FROM orders o
    JOIN books b ON o.book_id = b.book_id
    WHERE o.order_id = $order_id
");

$order = $query->fetch_assoc();

// book image fallback
$image = $order['image_path'] ? $order['image_path'] : "assets/default-book.png";
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Order Successful</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
.success-container {
    width: 40%;
    min-width: 350px;
    margin: 50px auto;
    text-align: center;
    padding: 25px 30px;
    background: #ffffff;
    border-radius: 12px;
}
.success-icon {
    font-size: 65px;
    color: #28a745;   /* GREEN ICON (as you wanted) */
}
.book-img {
    width: 160px;
    height: 200px;
    object-fit: cover;
    border-radius: 8px;
}
</style>
</head>

<body>
<?php include 'navbar.php'; ?>

<div class="success-container shadow-none"> <!-- shadow removed -->

    <i class="fa-solid fa-circle-check success-icon"></i>

    <h3 class="mt-3" style="color:#28a745;">Thank You!</h3>
    <p>Your order has been placed successfully.</p>

    <!-- Book Preview -->
    <img src="<?= $image ?>" class="book-img mt-3">

    <h5 class="mt-3"><?= htmlspecialchars($order['title']) ?></h5>

    <p class="text-muted mt-2">
        <strong>Order Date:</strong> <?= date("d M Y, h:i A", strtotime($order['order_date'])) ?>
    </p>

    <!-- Buttons -->
    <a href="profile.php?section=orders" class="btn btn-danger w-100 mt-3">View My Orders</a>

    <a href="books.php" class="btn btn-outline-secondary w-100 mt-3">
        Continue Shopping
    </a>

</div>

<?php include 'footer.php'; ?>
</body>
</html>
