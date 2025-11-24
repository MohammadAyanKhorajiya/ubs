<?php
include 'backend/config.php';
session_start();

// Correct login check
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

$book_id = intval($_GET['id']);
$user_id = isLoggedIn() ? intval($_SESSION['user_id']) : 0;

// Fetch book details
$query = "SELECT b.*, c.category_name,
         (SELECT path FROM book_images WHERE book_id=b.book_id LIMIT 1) AS image_path
         FROM books b LEFT JOIN categories c ON b.category_id=c.category_id
         WHERE b.book_id=$book_id LIMIT 1";
$result = mysqli_query($conn, $query);
$book = mysqli_fetch_assoc($result);
$image = $book['image_path'] ? $book['image_path'] : 'assets/default-book.png';

// Check wishlist
$isLiked = false;
if ($user_id > 0) {
    $check = mysqli_query($conn, "SELECT * FROM wishlist WHERE id=$user_id AND book_id=$book_id");
    if (mysqli_num_rows($check) > 0) $isLiked = true;
}
$reviews = $conn->query("
    SELECT r.*, u.name 
    FROM book_reviews r
    JOIN users u ON r.user_id=u.id
    WHERE r.book_id=$book_id
    ORDER BY r.created_at DESC
");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($book['title']) ?> - Book Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="css/style.css">
    <style>
        .book-image {
            width: 100%;
            height: 280px;
            object-fit: cover;
            border: none;
            box-shadow: none;
            border-radius: 6px;
        }
        .book-title {
            font-size: 1.6rem;
            font-weight: 600;
        }
        .book-price {
            font-size: 1.2rem;
            font-weight: 500;
            color: #dc3545;
            margin-bottom: 15px;
        }
        .wishlist-btn {
            background: none;
            border: none;
            font-size: 1.8rem;
            color: <?= $isLiked ? "#dc3545" : "#aaa" ?>;
            cursor: pointer;
            transition: 0.3s;
        }
        .wishlist-btn:hover {
            color: #dc3545;
        }
        .detail-item {
            margin-bottom: 6px;
        }
        .detail-item strong {
            width: 150px;
            display: inline-block;
        }
        .centered-container {
            max-width: 1200px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container my-5 centered-container">
        <div class="row g-4 align-items-start">
            <div class="col-md-4">
                <img src="<?= htmlspecialchars($image) ?>" alt="Book Cover" class="book-image">
            </div>

            <div class="col-md-8">
                <h3 class="book-title mb-2"><?= htmlspecialchars($book['title']) ?></h3>
                <h5 class="book-price">
                    <?= $book['type'] === 'Donated' ? 'Donated' : '₹' . htmlspecialchars($book['price']) ?>
                </h5>

                <div class="d-flex gap-3 mb-2">
                    <button class="btn btn-danger px-4 py-2" id="add-to-cart-btn">Add to Cart</button>
                    <a href="order.php?id=<?= $book_id ?>" class="btn btn-danger px-4 py-2">Buy Book</a>

                </div>

                <div id="cart-alert" class="alert alert-success alert-dismissible fade show d-none" role="alert">
                    <span id="cart-alert-msg"></span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>


                <!-- ❤️ Wishlist / Like Button -->
                <div>
                    <button id="wishlist-btn" class="wishlist-btn">
                        <i class="fa-solid fa-heart"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="mt-5">
            <h5 class="fw-bold mb-3">Book Details</h5>
            <p class="detail-item"><strong>Title:</strong> <?= htmlspecialchars($book['title']) ?></p>
            <p class="detail-item"><strong>Description:</strong> <?= nl2br(htmlspecialchars($book['description'])) ?></p>
            <p class="detail-item"><strong>ISBN:</strong> <?= htmlspecialchars($book['isbn'] ?: 'N/A') ?></p>
            <p class="detail-item"><strong>Condition:</strong> <?= htmlspecialchars($book['condition']) ?></p>
            <p class="detail-item"><strong>Category:</strong> <?= htmlspecialchars($book['category_name'] ?? 'Uncategorized') ?></p>
            <p class="detail-item"><strong>Language:</strong> <?= htmlspecialchars($book['language'] ?: 'N/A') ?></p>
            <p class="detail-item"><strong>Author:</strong> <?= htmlspecialchars($book['author'] ?: 'Unknown') ?></p>
        </div>
        
        <div class="mt-5">
            <h5 class="fw-bold mb-3">Reviews</h5>

            <?php if ($reviews->num_rows == 0): ?>
                <p class="text-muted">No reviews yet.</p>
            <?php else: ?>
                <?php while($r = $reviews->fetch_assoc()): ?>
                    <div class="p-3 border rounded mb-3">
                        <strong><?= htmlspecialchars($r['name']) ?></strong>
                        <span class="ms-2 text-warning">
                            <?= str_repeat("★", $r['rating']) ?>
                            <?= str_repeat("☆", 5 - $r['rating']) ?>
                        </span>
                        <p class="mt-2 mb-1"><?= nl2br(htmlspecialchars($r['comment'])) ?></p>
                        <small class="text-muted"><?= date("d M Y", strtotime($r['created_at'])) ?></small>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>


    <?php include 'footer.php'; ?>

<script>
const wishlistBtn = document.getElementById('wishlist-btn');

wishlistBtn.addEventListener('click', function() {

    <?php if (!$user_id): ?>  
        window.location.href = 'login.php';  
    <?php else: ?>  
        fetch('wishlist_toggle.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'book_id=<?= $book_id ?>'
        })
        .then(response => response.text())
        .then(data => {
            if (data === 'added') wishlistBtn.style.color = '#dc3545';
            if (data === 'removed') wishlistBtn.style.color = '#aaa';
        });
    <?php endif; ?>

});

//bootstrap alert mate 
document.getElementById('add-to-cart-btn').addEventListener('click', function() {
    <?php if (!$user_id): ?>
        window.location.href = 'login.php';
    <?php else: ?>
        fetch('cart_add.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'book_id=<?= $book_id ?>'
        })
        .then(res => res.text())
        .then(data => {

            let alertBox = document.getElementById('cart-alert');
            let alertMsg = document.getElementById('cart-alert-msg');

            if (data === 'added') {
                alertMsg.innerText = "Book added to cart.";
                alertBox.classList.remove('d-none');
                alertBox.classList.add('show');
            } 
            else if (data === 'exists') {
                alertMsg.innerText = "Already added in cart.";
                alertBox.classList.remove('d-none');
                alertBox.classList.add('show');
            }

            setTimeout(() => {
                alertBox.classList.add('d-none');
            }, 3000);
        });
    <?php endif; ?>
});

</script>

</body>
</html>
