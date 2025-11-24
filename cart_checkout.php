<?php
session_start();
include 'backend/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch cart items
$cart = $conn->query("
    SELECT c.*, b.title, b.author, b.price, b.type, b.condition,
           (SELECT path FROM book_images WHERE book_id=b.book_id LIMIT 1) AS image_path,
           b.user_id AS seller_id
    FROM cart c
    JOIN books b ON c.book_id=b.book_id
    WHERE c.user_id=$user_id
");

// Fetch user address
$user = $conn->query("
    SELECT name, phone, state, city, pincode, house_no 
    FROM users WHERE id=$user_id
")->fetch_assoc();

$address = $user['house_no'].", ".$user['city'].", ".$user['state']." - ".$user['pincode'];

$item_count = 0;
$total_amount = 0;

$check = $conn->query("
SELECT c.quantity, b.price, b.type
FROM cart c
JOIN books b ON c.book_id = b.book_id
WHERE c.user_id = $user_id
");

while ($r = $check->fetch_assoc()) {
    $item_count += $r['quantity'];
    $price = ($r['type'] === "Donated") ? 0 : $r['price'];
    $total_amount += ($price * $r['quantity']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Checkout</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
.checkout-box {
    width: 40%;
    min-width: 360px;
    margin: 40px auto;
    background: #fff;
    border-radius: 10px;
    padding: 25px 30px;
}
.item-box {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 18px;
    border-bottom: 1px solid #eee;
    padding-bottom: 12px;
}
.item-box img {
    width: 80px;
    height: 95px;
    object-fit: cover;
    border-radius: 5px;
}
.section-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #e63946;
}
</style>
</head>

<body>
<?php include 'navbar.php'; ?>

<div class="checkout-box">
    
    <h5 class="text-danger fs-4 mb-3">Items in Your Cart</h5>


    <?php if ($cart->num_rows == 0): ?>
        <p class="text-muted">Your cart is empty.</p>
    <?php else: ?>

        <?php while ($item = $cart->fetch_assoc()): ?>
        <div class="item-box">
            <img src="<?= $item['image_path'] ?>" />
            <div>
                <h6 class="mb-1"><?= htmlspecialchars($item['title']) ?></h6>
                <div class="text-muted small"><?= htmlspecialchars($item['condition']) ?></div>
                <strong class="text-danger">
                    <?= $item['type']=="Donated" ? "Free" : "₹".$item['price'] ?>
                </strong>
            </div>
        </div>
        <?php endwhile; ?>

    <?php endif; ?>

    <!-- ADDRESS -->
    <div class="mt-4">
        <h5 class="text-danger fs-5 mb-3">Delivery Address</h5>
        <div class="border rounded p-3 bg-light">
            <?= htmlspecialchars($address) ?><br>
            <strong>Phone:</strong> <?= htmlspecialchars($user['phone']) ?>
        </div>

        <a href="profile.php?section=profile" class="text-danger small d-block mt-2">
            Edit Address
        </a>
    </div>

    <div class="summary-box mt-4">
        <h5 class="mb-3">Order Summary</h5>

        <p class="d-flex justify-content-between mb-1">
            <span>Total Items:</span>
            <strong><?= $item_count ?></strong>
        </p>

        <p class="d-flex justify-content-between">
            <span>Total Amount:</span>
            <strong class="text-danger">₹<?= $total_amount ?></strong>
        </p>
    </div>


    <!-- PAYMENT -->
    <div class="mt-4">
        <h5 class="text-danger fs-5 mb-3">Payment Method</h5>

        <form action="place_multi_order.php" method="POST">
            <select name="payment_method" class="form-select" required>
                <option value="">Select Method</option>
                <option value="Cash">Cash</option>
                <option value="UPI">UPI</option>
                <option value="Card">Card</option>
                <option value="Bank Transfer">Bank Transfer</option>
            </select>

            <button type="submit" class="btn btn-danger w-100 py-2 mt-3">
                Place Order
            </button>
        </form>

    </div>

</div>

<?php include 'footer.php'; ?>
</body>
</html>
