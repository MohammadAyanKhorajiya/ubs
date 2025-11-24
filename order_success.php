<?php
session_start();
include 'backend/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['order_id'])) {
    die("Invalid Request");
}

$order_id = intval($_GET['order_id']);
$user_id = $_SESSION['user_id'];

// FETCH ORDER + BOOK + PAYMENT DETAILS
$query = $conn->query("
    SELECT o.*, 
           b.title, 
           (SELECT path FROM book_images WHERE book_id=b.book_id LIMIT 1) AS image_path,
           p.method, 
           p.amount, 
           p.paid_at
    FROM orders o
    JOIN books b ON o.book_id = b.book_id
    LEFT JOIN payments p ON p.order_id = o.order_id
    WHERE o.order_id=$order_id AND o.buyer_id=$user_id
");

$order = $query->fetch_assoc();
if (!$order) {
    die("Order not found.");
}

$img = $order['image_path'] ? $order['image_path'] : "assets/default-book.png";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Order Success</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
.success-container {
    width: 40%;
    min-width: 350px;
    margin: 40px auto;
    background: #fff;
    padding: 25px 30px;
    border-radius: 10px;
    box-shadow: 0 0 8px rgba(0,0,0,0.1);
    text-align: center;
}
.success-icon {
    font-size: 70px;
    color: #28a745; /* GREEN ✔ */
}
.success-title {
    font-size: 1.7rem;
    font-weight: 700;
    color: #28a745; /* GREEN */
}
.book-img {
    width: 120px;
    height: 160px;
    object-fit: cover;
    border-radius: 6px;
    margin-top: 10px;
}
.info-box {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 6px;
    text-align: left;
    margin-top: 20px;
}
.success-container {
    box-shadow: none !important;
}

</style>
</head>

<body>
<?php include 'navbar.php'; ?>

<div class="success-container">

    <i class="fa-solid fa-circle-check success-icon"></i>

    <h3 class="success-title mt-3">Thank You! Your Order is Confirmed</h3>

    <img src="<?= $img ?>" class="book-img">

    <h5 class="mt-3"><?= htmlspecialchars($order['title']) ?></h5>

    <div class="info-box mt-4">
        <p><strong>Order Date:</strong> <?= date("d M Y, h:i A", strtotime($order['order_date'])) ?></p>
        <p><strong>Payment Method:</strong> <?= $order['method'] ?></p>
        <p><strong>Amount:</strong> 
            <?= $order['amount'] == 0 ? "Free (Donated)" : "₹".$order['amount'] ?>
        </p>
    </div>

    <a href="http://localhost/ubs/profile.php?section=orders?id=<?= $order_id ?>" class="btn btn-danger w-100 mt-4">View Order</a>

    <a href="books.php" class="btn btn-outline-success w-100 mt-3">Continue Shopping</a>  
    <!-- GREEN BUTTON (as requested) -->
</div>

<?php include 'footer.php'; ?>
</body>
</html>
