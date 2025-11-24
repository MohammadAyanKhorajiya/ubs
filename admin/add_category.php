<?php
include '../backend/config.php';
include "sidebar.php";

// ADD CATEGORY
if (isset($_POST['add_category'])) {
    $cat = trim($_POST['category_name']);
    if ($cat !== "") {
        $stmt = $conn->prepare("INSERT INTO categories (category_name) VALUES (?)");
        $stmt->bind_param("s", $cat);
        $stmt->execute();
        header("Location: add_category.php");
        exit();
    }
}

// DELETE CATEGORY
if (isset($_GET['delete'])) {
    $cid = intval($_GET['delete']);
    $conn->query("DELETE FROM categories WHERE category_id=$cid");
    header("Location: add_category.php");
    exit();
}

// FETCH ALL CATEGORIES
$categories = $conn->query("SELECT * FROM categories ORDER BY category_id DESC");
?>

<h2 class="mb-4">Manage Categories</h2>

<!-- ADD CATEGORY BOX -->
<div class=" mb-4">
    <form method="POST" class="row g-3">
        <div class="col-md-6">
            <label class="form-label fw-semibold">Category Name</label>
            <input type="text" name="category_name" class="form-control" placeholder="Enter category name" required>
        </div>

        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" name="add_category" class="btn btn-danger w-100">Add Category</button>
        </div>
    </form>
</div>

<!-- CATEGORY TABLE -->
<div class="table-responsive bg-white">
    <table class="table table-bordered table-hover align-middle">
        <thead class="table-danger">
            <tr>
                <th width="60">ID</th>
                <th>Category Name</th>
                <th width="150">Actions</th>
            </tr>
        </thead>

        <tbody>
            <?php while ($row = $categories->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['category_id'] ?></td>
                    <td><?= htmlspecialchars($row['category_name']) ?></td>

                    <td>
                        <a href="add_category.php?delete=<?= $row['category_id'] ?>"
                           onclick="return confirm('Delete this category?')"
                           class="btn btn-danger btn-sm">
                           Delete
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</div>
</div>
</body>
</html>
