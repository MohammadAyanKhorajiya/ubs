<?php
include 'backend/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch cart items
$query = "
SELECT c.cart_id, c.quantity, 
       b.book_id, b.title, b.price, b.type, b.condition,
       (SELECT path FROM book_images WHERE book_id=b.book_id LIMIT 1) AS image_path
FROM cart c
JOIN books b ON c.book_id = b.book_id
WHERE c.user_id = $user_id
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="css/style.css">
</head>
<style>
.cart-item {
    border: 1px solid #eee;
    padding: 15px;
    border-radius: 8px;
    background: #fff;
}
.cart-item img {
    width: 110px;
    height: 130px;
    object-fit: cover;
    border-radius: 6px;
}
.qty-btn {
    width: 30px;
    height: 30px;
    border: none;
    background: #dc3545;
    color: #fff;
    border-radius: 4px;
}
.qty-display {
    width: 40px;
    text-align: center;
}
.summary-box {
    border: 1px solid #ddd;
    padding: 20px;
    border-radius: 8px;
    background: #fff;
}
</style>
<body>
    <?php include 'navbar.php'; ?>

<div class="container my-5">

<h3 class="mb-4">My Cart</h3>

<?php
$total = 0;

if ($result->num_rows === 0) {
    echo "<p class='text-muted'>Your cart is empty.</p>";
} else {
    while ($row = $result->fetch_assoc()) {
        $img = $row['image_path'] ?: "assets/default-book.png";
        $price = ($row['type'] === "Donated") ? 0 : $row['price'];
        $line_total = $price * $row['quantity'];
        $total += $line_total;
?>
    
    <!-- ⭐⭐ CARD CLICKABLE ADDED ⭐⭐ -->
    <a href="bookdetails.php?id=<?= $row['book_id'] ?>" style="text-decoration:none; color:inherit;">
    <div class="cart-item mb-3 d-flex align-items-center justify-content-between">

        <!-- Image -->
        <div>
            <img src="<?= $img ?>">
        </div>

        <!-- Book Details -->
        <div style="flex:1; margin-left:15px;">
            <h5 class="mb-1"><?= htmlspecialchars($row['title']) ?></h5>
            <p class="text-muted small mb-1">Condition: <?= htmlspecialchars($row['condition']) ?></p>
            <p class="text-danger fw-bold">
                <?= $row['type']==="Donated" ? "Donated" : "₹".$price ?>
            </p>
        </div>

        <!-- Quantity -->
        <div class="d-flex align-items-center" onclick="event.preventDefault(); event.stopPropagation();">
            <button class="qty-btn" onclick="updateQty(<?= $row['cart_id'] ?>,'minus')">-</button>
            <input class="qty-display mx-2" value="<?= $row['quantity'] ?>" readonly>
            <button class="qty-btn" onclick="updateQty(<?= $row['cart_id'] ?>,'plus')">+</button>
        </div>

        <!-- Line Total -->
        <div class="mx-4 fw-bold">
            <?= $row['type']==="Donated" ? "Free" : "₹".$line_total ?>
        </div>

        <!-- Delete -->
        <div onclick="event.preventDefault(); event.stopPropagation();">
            <button class="btn btn-outline-danger" onclick="removeItem(<?= $row['cart_id'] ?>)">
                <i class="fa-solid fa-trash"></i>
            </button>
        </div>

    </div>
    </a>
    <!-- ⭐⭐ CLICKABLE END ⭐⭐ -->

<?php
    }
}
?>

<!-- Summary -->
<?php if ($result->num_rows > 0): ?>
<div class="summary-box mt-4" style="width:40%; margin-left:auto;">
    <h5 class="mb-3">Order Summary</h5>

    <p class="d-flex justify-content-between">
        <strong>Total:</strong>
        <span class="text-danger fw-bold">₹<?= $total ?></span>
    </p> 

    <!-- ⭐ CHECKOUT REDIRECT ADDED ⭐ -->
    <button class="btn btn-danger w-100 mt-3" onclick="window.location.href='cart_checkout.php'">
        Checkout
    </button>
</div>
<?php endif; ?>


</div>

<script>
function updateQty(cart_id, action) {
    fetch("cart_update.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "cart_id=" + cart_id + "&action=" + action
    })
    .then(r => r.text())
    .then(() => location.reload());
}

function removeItem(cart_id) {
    if (confirm("Remove this item?")) {
        fetch("cart_delete.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "cart_id=" + cart_id
        })
        .then(r => r.text())
        .then(() => location.reload());
    }
}
</script>

<?php include 'footer.php'; ?>
</body>
</html>
