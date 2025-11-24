<?php
include '../backend/config.php';
include "sidebar.php";

// DELETE BOOK
if (isset($_GET['delete'])) {
    $bid = intval($_GET['delete']);

    // delete images first
    $conn->query("DELETE FROM book_images WHERE book_id=$bid");

    // delete book
    $conn->query("DELETE FROM books WHERE book_id=$bid");

    header("Location: manage_books.php");
    exit();
}

// Fetch all categories
$categories = $conn->query("SELECT * FROM categories ORDER BY category_name");

// Fetch all books with category join
$books = $conn->query("
    SELECT b.book_id, b.title, b.condition, b.price, b.type, 
           b.category_id, c.category_name,
           (SELECT path FROM book_images WHERE book_id=b.book_id LIMIT 1) AS image_path
    FROM books b
    LEFT JOIN categories c ON b.category_id = c.category_id
    ORDER BY b.book_id DESC
");

$conditions = ['New', 'Good', 'Fair', 'Old'];
?>

<h2 class="mb-4">Manage Books</h2>

<!-- FILTERS -->
<div class="bg-white p-3 rounded shadow-sm mb-4">
    <div class="row g-3">
        <div class="col-md-4">
            <label class="fw-semibold mb-1">Category</label>
            <select id="filterCategory" class="form-select">
                <option value="">All Categories</option>
                <?php while($cat = $categories->fetch_assoc()): ?>
                    <option value="<?= $cat['category_name'] ?>">
                        <?= htmlspecialchars($cat['category_name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="col-md-4">
            <label class="fw-semibold mb-1">Condition</label>
            <select id="filterCondition" class="form-select">
                <option value="">All Conditions</option>
                <?php foreach($conditions as $con): ?>
                    <option value="<?= $con ?>"><?= $con ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</div>

<!-- BOOKS TABLE -->
<div class="table-responsive bg-white">
    <table class="table table-bordered table-hover align-middle" id="booksTable">
        <thead class="table-danger">
            <tr>
                <th width="60">ID</th>
                <th width="120">Image</th>
                <th>Title</th>
                <th width="160">Category</th>
                <th width="120">Condition</th>
                <th width="120">Price</th>
                <th width="160">Actions</th>
            </tr>
        </thead>

        <tbody>
        <?php while ($row = $books->fetch_assoc()): 
            $img = !empty($row['image_path']) 
                ? "../uploads/books/" . $row['image_path'] 
                : "../assets/default-book.png";
        ?>
            <tr onclick="window.location.href='../bookdetails.php?id=<?= $row['book_id'] ?>'" 
                style="cursor:pointer;">

                <td><?= $row['book_id'] ?></td>

                <td>
                    <img src="<?= $img ?>" 
                        style="width:70px; height:85px; object-fit:cover; border-radius:6px;">
                </td>

                <td><?= htmlspecialchars($row['title']) ?></td>

                <td class="cat"><?= htmlspecialchars($row['category_name'] ?? 'Uncategorized') ?></td>

                <td class="cond"><?= htmlspecialchars($row['condition']) ?></td>

                <td>
                    <?= $row['type']=='Donated' ? 'Donated' : '₹' . $row['price']; ?>
                </td>

                <td onclick="event.stopPropagation();">
                    <a href="manage_books.php?delete=<?= $row['book_id'] ?>"
                    onclick="return confirm('Delete this book permanently?')"
                    class="btn btn-danger btn-sm">Delete</a>
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

<script>
// REAL-TIME FILTERING (Category + Condition)
document.getElementById("filterCategory").addEventListener("change", filterBooks);
document.getElementById("filterCondition").addEventListener("change", filterBooks);

function filterBooks() {
    let cat = document.getElementById("filterCategory").value.toLowerCase();
    let cond = document.getElementById("filterCondition").value.toLowerCase();

    document.querySelectorAll("#booksTable tbody tr").forEach(row => {
        let rowCat = row.querySelector(".cat").innerText.toLowerCase();
        let rowCond = row.querySelector(".cond").innerText.toLowerCase();

        let show = true;

        if (cat !== "" && rowCat !== cat) show = false;
        if (cond !== "" && rowCond !== cond) show = false;

        row.style.display = show ? "" : "none";
    });
}
</script>
