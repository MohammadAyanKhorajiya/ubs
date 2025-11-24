<?php
include 'backend/config.php';

$categoryFilter = isset($_GET['category']) ? $_GET['category'] : '';
$conditionFilter = isset($_GET['condition']) ? $_GET['condition'] : '';

$query = "SELECT b.*, c.category_name, 
          (SELECT path FROM book_images WHERE book_id = b.book_id LIMIT 1) AS image_path
          FROM books b
          LEFT JOIN categories c ON b.category_id = c.category_id
          WHERE b.status='Available' AND b.quantity > 0";

if ($categoryFilter != '') {
    $query .= " AND b.category_id = '" . mysqli_real_escape_string($conn, $categoryFilter) . "'";
}

if ($conditionFilter != '') {
    $query .= " AND b.`condition` = '" . mysqli_real_escape_string($conn, $conditionFilter) . "'";
}

$query .= " ORDER BY b.posted_at DESC";

$result = mysqli_query($conn, $query);


// Get dropdown data
$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY category_name");
$conditions = ['New', 'Good', 'Fair', 'Old'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Books</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .filter-box {
            background: #fff;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
            border-radius: 8px;
            padding: 15px 20px;
            margin-bottom: 30px;
        }
        .book-card img.fixed-img {
            height: 220px;
            object-fit: cover;
        }
        .book-card h5 {
            font-size: 1rem;
            font-weight: 600;
        }
        .text-small {
            font-size: 0.9rem;
        }
        /* ✅ EXACTLY 5 CARDS PER ROW */
        .book-row {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            justify-content: start;
        }
        .book-card {
            flex: 0 0 calc(20% - 1.2rem); /* 5 cards per row */
            max-width: calc(20% - 1.2rem);
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
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container my-5">
        <!-- Filter Box -->
        <div class="filter-box">
            <form method="GET" class="row g-3 align-items-center">
                <div class="col-md-5">
                    <label class="form-label mb-1 fw-semibold">Book Category</label>
                    <select name="category" class="form-select">
                        <option value="">All Categories</option>
                        <?php while($cat = mysqli_fetch_assoc($categories)) { ?>
                            <option value="<?= $cat['category_id'] ?>" <?= ($categoryFilter == $cat['category_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['category_name']) ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label mb-1 fw-semibold">Book Condition</label>
                    <select name="condition" class="form-select">
                        <option value="">All Conditions</option>
                        <?php foreach($conditions as $cond) { ?>
                            <option value="<?= $cond ?>" <?= ($conditionFilter == $cond) ? 'selected' : '' ?>>
                                <?= $cond ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-2 text-center">
                    <button type="submit" class="btn btn-danger w-100 mt-4">Filter</button>
                </div>
            </form>
        </div>

        <!-- Books Grid -->
        <div class="book-row">
            <?php if(mysqli_num_rows($result) > 0) { ?>
                <?php while($book = mysqli_fetch_assoc($result)) { 
                    $image = $book['image_path'] ? $book['image_path'] : 'assets/default-book.png';
                ?>
                    <div class="book-card">
                        <a href="bookdetails.php?id=<?= $book['book_id'] ?>" class="text-decoration-none text-dark">
                            <div class="card h-100 shadow-sm">
                                <img src="<?= htmlspecialchars($image) ?>" class="card-img-top fixed-img" alt="Book Cover">
                                <div class="card-body p-3">
                                    <h5 class="mb-1"><?= htmlspecialchars($book['title']) ?></h5>
                                    <p class="text-muted text-small mb-1"><?= htmlspecialchars($book['category_name'] ?? 'Uncategorized') ?></p>
                                    <p class="text-muted text-small mb-1">Condition: <?= htmlspecialchars($book['condition']) ?></p>
                                    <p class="text-danger mb-0">
                                        <?= $book['type'] === 'Donated' ? 'Donated' : '₹' . htmlspecialchars($book['price']) ?>
                                    </p>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <p class="text-center text-muted mt-4">No books found.</p>
            <?php } ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
