<?php
session_start();
include 'backend/config.php';
include 'backend/stock_manager.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$order_id = intval($_GET['id']);

$order = $conn->query("
    SELECT o.*, 
           b.title, b.book_id,
           (SELECT path FROM book_images WHERE book_id=b.book_id LIMIT 1) AS image_path,
           p.method AS payment_method
    FROM orders o
    JOIN books b ON o.book_id = b.book_id
    LEFT JOIN payments p ON p.order_id = o.order_id
    WHERE o.order_id = $order_id
")->fetch_assoc();

$img = $order['image_path'] ?: "assets/default-book.png";

// ---- CANCEL ORDER ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order'])) {

    $reason = trim($_POST['cancel_reason']);
    $order_id = intval($_POST['order_id']);

    $conn->query("UPDATE orders SET status='Canceled' WHERE order_id=$order_id");

    $get = $conn->query("SELECT book_id FROM orders WHERE order_id=$order_id");
    $row = $get->fetch_assoc();
    $book_id = $row['book_id'];

    // Restore Quantity
    $getQty = $conn->query("SELECT quantity FROM books WHERE book_id=$book_id");
    $old_qty = $getQty->fetch_assoc()['quantity'];

    $new_qty = $old_qty + 1;

    $conn->query("UPDATE books SET quantity=$new_qty, status='Available' WHERE book_id=$book_id");
    


        
        $reasonSafe = $conn->real_escape_string($reason);
        $conn->query("INSERT INTO order_cancel_reasons (order_id, reason) VALUES ($order_id, '$reasonSafe')");

        header("Location: order_details.php?id=".$order_id."&cancelled=1");
        exit();
    }

$reviewCheck = $conn->query("SELECT review_id FROM book_reviews 
    WHERE book_id={$order['book_id']} AND user_id={$_SESSION['user_id']}");

$alreadyReviewed = ($reviewCheck->num_rows > 0);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<?php include 'navbar.php'; ?>

<div class="container" style="max-width:600px; margin-top:40px;margin-bottom:40px;">
    
    <div class="p-1 bg-white">

        <h4 class="text-danger mb-4">Order Details</h4>

        <img src="<?= $img ?>" class="w-100 rounded mb-3" style="height:260px;object-fit:cover;">

        <h5 class="fw-semibold mb-2"><?= htmlspecialchars($order['title']); ?></h5>

        <p><strong>Order Date:</strong> <?= date("d M Y", strtotime($order['order_date'])) ?></p>

        <p><strong>Status:</strong> 
            <span class="badge 
                <?php if($order['status']=='Completed') echo 'bg-success';
                      elseif($order['status']=='Pending') echo 'bg-warning text-dark';
                      else echo 'bg-danger'; ?>">
                <?= $order['status'] ?>
            </span>
        </p>

        <p><strong>Payment Type:</strong> <?= $order['payment_method'] ?></p>

        <?php if ($order['status'] == "Pending"): ?>
        <div class="mt-4 p-3 border rounded bg-light">
            <h6 class="fw-bold mb-2 text-danger">Cancel Order</h6>

            <!-- Cancel Button -->
            <button id="cancelBtn" class="btn btn-danger btn-sm">Cancel Order</button>

            <!-- Radio Box (Initially Hidden) -->
            <form id="cancelForm" method="POST" class="mt-3 d-none">
                <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                <input type="hidden" name="cancel_order" value="1">

                <p class="fw-semibold mb-1">Select Reason:</p>

                <div class="form-check mb-1">
                    <input class="form-check-input" type="radio" name="cancel_reason" value="Ordered by mistake" required>
                    <label class="form-check-label">Ordered by mistake</label>
                </div>

                <div class="form-check mb-1">
                    <input class="form-check-input" type="radio" name="cancel_reason" value="Found cheaper elsewhere">
                    <label class="form-check-label">Found cheaper elsewhere</label>
                </div>

                <div class="form-check mb-1">
                    <input class="form-check-input" type="radio" name="cancel_reason" value="Order taking too long">
                    <label class="form-check-label">Order taking too long</label>
                </div>

                <div class="form-check mb-1">
                    <input class="form-check-input" type="radio" name="cancel_reason" value="Don't need it anymore">
                    <label class="form-check-label">Don’t need it anymore</label>
                </div>

                <button type="submit" class="btn btn-danger mt-3 w-100">Confirm Cancel</button>
            </form>
        </div>
    <?php endif; ?>
    <?php if ($order['status'] == "Completed" && !$alreadyReviewed): ?>
    <div class="mt-4 p-3 border rounded bg-light">

        <h6 class="fw-bold mb-2 text-success">Write a Review</h6>

        <form method="POST">
            <input type="hidden" name="submit_review" value="1">
            <input type="hidden" name="book_id" value="<?= $order['book_id'] ?>">

            <label class="form-label">Rating (1–5)</label>
            <select name="rating" class="form-select mb-2" required>
                <option value="">Select Rating</option>
                <option value="1">1 ★</option>
                <option value="2">2 ★★</option>
                <option value="3">3 ★★★</option>
                <option value="4">4 ★★★★</option>
                <option value="5">5 ★★★★★</option>
            </select>

            <label class="form-label">Comment</label>
            <textarea name="comment" class="form-control mb-2" rows="3"></textarea>
        
            <button class="btn btn-success w-100">Submit Review</button>
        </form>

    </div>
    <?php endif; ?>
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {

        $book_id = intval($_POST['book_id']);
        $rating  = intval($_POST['rating']);
        $comment = $conn->real_escape_string(trim($_POST['comment']));

        $uid = $_SESSION['user_id'];

        $conn->query("
            INSERT INTO book_reviews (book_id, user_id, rating, comment)
            VALUES ($book_id, $uid, $rating, '$comment')
        ");

        header("Location: order_details.php?id=".$order_id."&review_added=1");
        exit();
    }
    ?>

    </div>

</div>

<?php include 'footer.php'; ?>
<script>
document.getElementById("cancelBtn")?.addEventListener("click", function() {
    document.getElementById("cancelForm").classList.remove("d-none");
});
</script>

</body>
</html>
