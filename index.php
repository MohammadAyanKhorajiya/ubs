<?php
include 'backend/config.php';

$best = $conn->query("
    SELECT 
        b.book_id, 
        b.title, 
        b.price, 
        b.type,
        c.category_name,
        (SELECT path FROM book_images WHERE book_id=b.book_id LIMIT 1) AS image_path,
        COUNT(o.order_id) AS total_orders
    FROM orders o
    JOIN books b ON o.book_id = b.book_id
    LEFT JOIN categories c ON b.category_id = c.category_id
    GROUP BY o.book_id
    ORDER BY total_orders DESC
    LIMIT 4
");
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
    <style>
        .books-row {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            justify-content: center; 
        }

        .book-card {
            flex: 0 0 calc(20% - 1.2rem);
            max-width: calc(20% - 1.2rem);
        }

        .book-card img.fixed-img {
            height: 220px;
            object-fit: cover;
        }

        @media (max-width: 992px) {
            .book-card { flex: 0 0 calc(33.333% - 1rem); max-width: calc(33.333% - 1rem); }
        }
        @media (max-width: 768px) {
            .book-card { flex: 0 0 calc(50% - 1rem); max-width: calc(50% - 1rem); }
        }
        @media (max-width: 576px) {
            .book-card { flex: 0 0 100%; max-width: 100%; }
        }
        @media (max-width: 576px) {
            .banner-buttons {
                display: flex;
                flex-direction: column;
                gap: 12px; 
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="banner-container">
        <img src="assets/banner33.png" alt="banner" class="banner-img">
        <div class="banner-text">
            <h2>Buy and sell your old book in simple steps</h2>
            <div class="banner-buttons">
                <a href="#" class="btn btn-danger me-2">
                    <i class="fa-solid fa-cart-shopping"></i> Buy Book
                </a>
                <a href="profile.php?section=posts&book_added=1" class="btn btn-light text-danger border border-danger">
                    <i class="fa-solid fa-handshake"></i> Sell Book
                </a>
            </div>
        </div>
    </div>

    <section class="best-selling-books py-2">
        <div class="container">
            <h2 class="text-danger mt-5 mb-4 text-center">Best Selling Books</h2>

            <div class="books-row">
            <?php 
            if ($best->num_rows > 0) {
                while($book = $best->fetch_assoc()) { 
                    $image = $book['image_path'] ? $book['image_path'] : 'assets/default-book.png';
            ?>
                
                <div class="book-card">
                    <a href="bookdetails.php?id=<?= $book['book_id'] ?>" class="text-decoration-none text-dark">
                        <div class="card h-100 shadow-sm">

                            <img src="<?= htmlspecialchars($image) ?>" class="card-img-top fixed-img" alt="Book Cover">

                            <div class="card-body p-3">
                                <h5 class="mb-1"><?= htmlspecialchars($book['title']) ?></h5>
                                <p class="text-muted small mb-2"><?= htmlspecialchars($book['category_name'] ?? 'Uncategorized') ?></p>
                                <p class="text-danger mb-2">
                                    <?= $book['type'] === 'Donated' ? 'Donated' : '₹' . htmlspecialchars($book['price']) ?>
                                </p>
                            </div>

                        </div>
                    </a>
                </div>

            <?php 
                }
            } else {
                echo "<p class='text-muted'>No best-selling books found.</p>";
            }
            ?>
            </div>
        </div>
    </section>



    <div class="text-center mt-5 mb-4">
        <a href="books.php" class="btn btn-danger px-4 py-2">Explore All Books</a>
    </div>

    <section class="post-ad-section" style="background-image: url('assets/banner33.png');">
        <div class="overlay"></div>

        <div class="post-ad-content container">
            <div class="post-ad-text d-flex align-items-start">
                <div>
                    <h2 class="mb-2">Post Your Book For Sell in Some Steps</h2>
                    <p>You have so lucky visit our website you have some steps to sell your book and buy books.</p>
                </div>
            </div>
            <div class="post-ad-btn">
                <a href="http://localhost/ubs/profile.php?section=posts" class="btn btn-danger px-4 py-2">Post Book</a>
            </div>
        </div>
    </section>

    <section class="how-to-sell py-5">
        <div class="container text-center">
            <h2 class="mb-5 section-title text-danger">How to Sell Your Book</h2>
            <div class="row justify-content-center gx-4 gy-3">
                <div class="col-12 col-md-4">
                    <div class="step-box p-4">
                        <div class="step-icon mb-3">
                            <i class="fa-solid fa-camera"></i>
                        </div>
                        <h3 class="step-title mb-2">Post an Ad</h3>
                        <p class="step-desc mb-0">
                            Describe and post your book for sell.
                        </p>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="step-box p-4">
                        <div class="step-icon mb-3">
                            <i class="fa-solid fa-tag"></i>
                        </div>
                        <h3 class="step-title mb-2">Set Price</h3>
                        <p class="step-desc mb-0">
                            Choose a price to attract buyers quickly.
                        </p>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="step-box p-4">
                        <div class="step-icon mb-3">
                            <i class="fa-solid fa-money-bill-wave"></i>
                        </div>
                        <h3 class="step-title mb-2">Get Paid</h3>
                        <p class="step-desc mb-0">
                            Receive payment directly once your book is sold.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="how-to-buy py-5">
        <div class="container text-center">
            <h2 class="mb-5 section-title text-danger">How to Buy a Book</h2>
            <div class="row justify-content-center gx-4 gy-3">
                <div class="col-12 col-md-4">
                    <div class="step-box p-4">
                        <div class="step-icon mb-3">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </div>
                        <h3 class="step-title mb-2">Search Item</h3>
                        <p class="step-desc mb-0">Select the used books you want.</p>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="step-box p-4">
                        <div class="step-icon mb-3">
                            <i class="fa-solid fa-cart-plus"></i>
                        </div>
                        <h3 class="step-title mb-2">Place Order</h3>
                        <p class="step-desc mb-0">Make a payment and place order.</p>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="step-box p-4">
                        <div class="step-icon mb-3">
                            <i class="fa-solid fa-truck"></i>
                        </div>
                        <h3 class="step-title mb-2">Books Delivered</h3>
                        <p class="step-desc mb-0">The books will be delivered to your address.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>

</body>
</html>