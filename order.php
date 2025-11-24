<?php
session_start();
include 'backend/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// --- BOOK ID FROM BUY BUTTON ---
$book_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch book details
$book = $conn->query("
    SELECT b.*, 
           (SELECT path FROM book_images WHERE book_id=b.book_id LIMIT 1) AS image_path
    FROM books b
    WHERE b.book_id=$book_id
")->fetch_assoc();


// ⭐ ADD THIS — Fetch seller_id of the posted book
$q = $conn->query("SELECT user_id FROM books WHERE book_id=$book_id");
$seller = $q->fetch_assoc();
$seller_id = $seller['user_id'];


// Fetch user address
$user = $conn->query("
    SELECT name, phone, state, city, pincode, house_no 
    FROM users WHERE id=$user_id
")->fetch_assoc();

// Merge address
$full_address = $user['house_no'] . ', ' . $user['city'] . ', ' . $user['state'] . ' - ' . $user['pincode'];
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Order Summary</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
.order-container {
    width: 40%;
    min-width: 350px;
    margin: 40px auto;
    background: #fff;
    padding: 25px 30px;
}
.book-img {
    width: 100%;
    height: 230px;
    object-fit: cover;
    border-radius: 6px;
}
</style>
</head>

<body>
<?php include 'navbar.php'; ?>

<div class="order-container">

    <!-- BOOK DETAILS -->
    <div class="mb-4">
        <h5 class="text-danger fs-4 mb-3">Book Details</h5>
        <img src="<?= $book['image_path'] ?>" class="book-img mb-3">

        <p><strong>Title:</strong> <?= htmlspecialchars($book['title']) ?></p>
        <p><strong>Author:</strong> <?= htmlspecialchars($book['author']) ?></p>
        <p><strong>Condition:</strong> <?= htmlspecialchars($book['condition']) ?></p>

        <h5 class="text-danger mt-2">
            <?= $book['type']=="Donated" ? "Free (Donated)" : "₹".$book['price']; ?>
        </h5>
    </div>

    <!-- ADDRESS SECTION -->
    <div class="mb-4">
        <h5 class="text-danger fs-5 mb-3">Delivery Address</h5>

        <div class="border rounded p-3 bg-light">
            <?= htmlspecialchars($full_address) ?><br>
            <strong>Phone:</strong> <?= htmlspecialchars($user['phone']) ?>
        </div>

        <a href="profile.php?section=profile" class="text-danger mt-2 d-block" style="font-size:0.9rem;">
            Edit Address
        </a>
    </div>

    <!-- PAYMENT -->
    <div class="mb-4">
        <h5 class="text-danger fs-5 mb-3">Payment Method</h5>

        <form action="place_order.php" method="POST">
            <input type="hidden" name="book_id" value="<?= $book_id ?>">
            <input type="hidden" name="seller_id" value="<?= $book['user_id'] ?>">

            <select name="payment_method" class="form-select" required>
                <option value="">Select Payment Method</option>
                <option value="Cash">Cash</option>
                <option value="UPI">UPI</option>
                <option value="Card">Card</option>
                <option value="Bank Transfer">Bank Transfer</option>
            </select>
    </div>

    <!-- FINAL BUTTON -->
    <button type="submit" class="btn btn-danger w-100 py-2">
        Confirm Order
    </button>

    </form>

</div>

<?php include 'footer.php'; ?>
</body>
</html>
