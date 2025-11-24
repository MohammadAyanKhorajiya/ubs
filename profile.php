<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'backend/config.php';

// Fetch user data
$user_id = $_SESSION['user_id'];
$query = $conn->prepare("SELECT name, email, phone, state, city, pincode, house_no, password FROM users WHERE id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();

// Handle update with POST → Redirect → GET
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $state    = trim($_POST['state']);
    $city     = trim($_POST['city']);
    $pincode  = trim($_POST['pincode']);
    $house_no = trim($_POST['house_no']);

    $update = $conn->prepare("UPDATE users SET name=?, phone=?, state=?, city=?, pincode=?, house_no=? WHERE id=?");
    $update->bind_param("ssssssi", $name, $phone, $state, $city, $pincode, $house_no, $user_id);

    if ($update->execute()) {
        header("Location: profile.php?updated=1");
        exit();
    }
}

// Handle password update
$password_msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current = trim($_POST['current_password']);
    $new = trim($_POST['new_password']);

    $query = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $query->bind_param("i", $user_id);
    $query->execute();
    $res = $query->get_result();
    $row = $res->fetch_assoc();

    if (password_verify($current, $row['password'])) {
        $hashed_new = password_hash($new, PASSWORD_DEFAULT);
        $update_pass = $conn->prepare("UPDATE users SET password=? WHERE id=?");
        $update_pass->bind_param("si", $hashed_new, $user_id);
        if ($update_pass->execute()) {
            $password_msg = "<div class='alert alert-success mt-3'>Password updated successfully!</div>";
        }
    } else {
        $password_msg = "<div class='alert alert-danger mt-3'>Current password is incorrect!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Profile</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
<link rel="stylesheet" href="css/style.css">

<style>
/* PROFILE PAGE STYLE - Compact Version */
.profile-wrapper {
  margin-top: 50px;
  margin-bottom: 50px;
  display: flex;
  flex-wrap: wrap;
  background: white;
  overflow: hidden;
 }

/* Sidebar */
.sidebar {
  flex: 0 0 220px;
  border-right: 1px solid #ddd;
  padding: 20px 15px;
}
.sidebar h5 {
  font-weight: 700;
  font-size: 1.05rem;
  color: #111;
  margin-bottom: 15px;
}
.sidebar ul {
  list-style: none;
  padding: 0;
  margin: 0;
}
.sidebar ul li {
  padding: 10px 12px;
  cursor: pointer;
  border-radius: 6px;
  margin-bottom: 6px;
  color: #111;
  font-weight: 500;
  transition: 0.3s;
  font-size: 0.95rem;
}
.sidebar ul li.active,
.sidebar ul li:hover {
  background-color: #e63946;
  color: #fff;
}

/* Main content */
.content-area {
  flex: 1;
  padding: 30px 25px;
}
.content-area h4 {
  color: #e63946;
  font-weight: 600;
  margin-bottom: 25px;
}
.btn-theme {
  background-color: #e63946;
  border: none;
  color: #fff;
}
.btn-theme:hover {
  background-color: #c72e3b;
}

/* Responsive */
@media (max-width: 768px) {
  .profile-wrapper {
    flex-direction: column;
  }
  .sidebar {
    flex: 1 1 100%;
    border-right: none;
    border-bottom: 1px solid #ddd;
  }
}
</style>
</head>
<body>
<?php include 'navbar.php'; ?> 

<div class="profile-wrapper">
  <!-- Sidebar -->
  <div class="sidebar">
    <h5><i class="fa-solid fa-user me-2"></i>Account</h5>
    <ul id="profileTabs">
      <li class="active" data-section="profile">My Profile</li>
      <li data-section="posts">My Posts</li>
      <li data-section="orders">My Orders</li>
      <li data-section="received_orders">Received Orders</li>
      <li data-section="wishlist">Wishlist</li>
    </ul>
  </div>

  <!-- Main Content -->
  <div class="content-area">
    <?php if (isset($_GET['updated'])): ?>
      <div class="alert alert-success alert-dismissible fade show auto-hide-alert" role="alert">
        Profile updated successfully!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <!-- My Profile Section -->
    <div id="profile-section">
      <h4>My Profile</h4>
      <form method="POST">
        <div class="mb-3 row">
          <label class="col-sm-3 col-form-label">Name</label>
          <div class="col-sm-9">
            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
          </div>
        </div>

        <div class="mb-3 row">
          <label class="col-sm-3 col-form-label">Email</label>
          <div class="col-sm-9">
            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
          </div>
        </div>

        <!-- Password Change Button -->
        <div class="mb-3 row">
          <label class="col-sm-3 col-form-label">Password</label>
          <div class="col-sm-9 d-flex align-items-center">
            <input type="password" class="form-control me-3" value="********" disabled>
            <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2" data-bs-toggle="modal" data-bs-target="#changePasswordModal">Change Password</button>
          </div>
        </div>

        <div class="mb-3 row">
          <label class="col-sm-3 col-form-label">Phone</label>
          <div class="col-sm-9">
            <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone']); ?>">
          </div>
        </div>

        <div class="mb-4 row">
          <label class="col-sm-3 col-form-label">Address</label>
          <div class="col-sm-9">

            <div class="mb-2">
              <select class="form-select" id="state" name="state" required>
                <option value="">Select State</option>
                <option value="Gujarat"     <?= ($user['state']=="Gujarat" ? "selected" : "") ?>>Gujarat</option>
                <option value="Maharashtra" <?= ($user['state']=="Maharashtra" ? "selected" : "") ?>>Maharashtra</option>
                <option value="Rajasthan"   <?= ($user['state']=="Rajasthan" ? "selected" : "") ?>>Rajasthan</option>
                <option value="Madhya Pradesh" <?= ($user['state']=="Madhya Pradesh" ? "selected" : "") ?>>Madhya Pradesh</option>
                <option value="Delhi"       <?= ($user['state']=="Delhi" ? "selected" : "") ?>>Delhi</option>
              </select>
              <span class="error" id="stateError"></span>
            </div>

            <div class="mb-2">
              <input type="text" class="form-control" id="city" name="city" 
                    placeholder="Enter City" 
                    value="<?= htmlspecialchars($user['city']); ?>">
              <span class="error" id="cityError"></span>
            </div>

            <div class="mb-2">
              <input type="text" class="form-control" id="pincode" name="pincode"
                    placeholder="Enter Pincode" maxlength="6"
                    value="<?= htmlspecialchars($user['pincode']); ?>">
              <span class="error" id="pincodeError"></span>
            </div>

            <div class="mb-2">
              <input type="text" class="form-control" id="house" name="house_no"
                    placeholder="House No / Building Name"
                    value="<?= htmlspecialchars($user['house_no']); ?>">
              <span class="error" id="houseError"></span>
            </div>

          </div>
        </div>


        <div class="text-end">
          <button type="submit" name="update_profile" class="btn btn-danger px-4">Update</button>
        </div>
      </form>
    </div>

<!-- PAAAAAAAAAAAAAAAAAAAAAAAAAAA-->


    <div id="posts-section" class="d-none">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>My Posts</h4>
        <button class="btn btn-danger" id="toggleNewPost">
        <i class="fa-solid fa-plus me-1"></i> New Post
        </button>
    </div>

    <!-- Inline New/Edit Form -->
    <div id="newPostFormContainer" class="border rounded p-3 mb-4" style="display:none; background:#fff;">
        <h5 id="formTitle" class="text-danger mb-3">Post a New Book</h5>

        <form method="POST" enctype="multipart/form-data" id="bookForm">
        <input type="hidden" name="book_id" id="book_id">
        <div class="row g-3">
            <div class="col-md-6">
            <label class="form-label">Title</label>
            <input type="text" name="title" id="title" class="form-control" required>
            <span class="error" id="titleError"></span>
            </div>
            <div class="col-md-6">
            <label class="form-label">Author</label>
            <input type="text" name="author" id="author" class="form-control" required>
            <span class="error" id="authorError"></span>
            </div>
            <div class="col-md-6">
            <label class="form-label">ISBN</label>
            <input type="text" name="isbn" id="isbn" class="form-control" required>
            <span class="error" id="isbnError"></span>
            </div>
            <div class="col-md-6">
            <label class="form-label">Category</label>
            <select name="category_id" id="category_id" class="form-select" required>
                <option value="">Select category</option>
                <?php
                $cat = $conn->query("SELECT * FROM categories ORDER BY category_name");
                while ($row = $cat->fetch_assoc()) {
                    echo "<option value='{$row['category_id']}'>{$row['category_name']}</option>";
                }
                ?>
            </select>
            <span class="error" id="categoryError"></span>
            </div>
            <div class="col-md-6">
            <label class="form-label">Condition</label>
            <select name="condition" id="condition" class="form-select" required>
                <option value="New">New</option>
                <option value="Good">Good</option>
                <option value="Fair">Fair</option>
                <option value="Old">Old</option>
            </select>
            </div>
            <div class="col-md-6">
            <label class="form-label">Language</label>
            <input type="text" name="language" id="language" class="form-control" required>
            <span class="error" id="languageError"></span>
            </div>
            <div class="col-md-6">
            <label class="form-label">Quantity</label>
            <input type="number" name="quantity" id="quantity" min="1" value="1" class="form-control" required>
            </div>
            <div class="col-md-6">
            <label class="form-label">Type</label>
            <select name="type" id="type" class="form-select" required>
                <option value="Paid">Paid</option>
                <option value="Donated">Donated</option>
            </select>
            </div>
            <div class="col-md-6">
            <label class="form-label">Price (₹)</label>
            <input type="number" step="0.01" name="price" id="price" class="form-control" value="0.00">
            </div>
            <div class="col-md-6">
            <label class="form-label">Image</label>
            <input type="file" name="image" id="image" class="form-control" accept="image/*">
            </div>
            <div class="col-12">
            <label class="form-label">Description</label>
            <textarea name="description" id="description" rows="3" class="form-control" required></textarea>
            </div>
        </div>

        <div class="text-end mt-3">
            <button type="submit" id="formSubmitBtn" class="btn btn-danger px-4">Post</button>
        </div>
        </form>

        <div id="formMessage" class="mt-3"></div>
    </div>

    <?php
    $stmt = $conn->prepare("
        SELECT b.*, c.category_name, (SELECT path FROM book_images WHERE book_id = b.book_id LIMIT 1) AS image_path 
        FROM books b 
        LEFT JOIN categories c ON b.category_id = c.category_id 
        WHERE b.user_id = ? ORDER BY b.posted_at DESC
    ");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $books = $stmt->get_result();

    if ($books->num_rows === 0): ?>
        <div class="text-center text-muted py-5 fs-5">No any post yet.</div>
    <?php else: ?>
        <div class="row">
        <?php while ($book = $books->fetch_assoc()): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <img src="<?php echo $book['image_path'] ? htmlspecialchars($book['image_path']) : 'images/no-book.png'; ?>" 
                        class="card-img-top" alt="Book Image" style="height:230px; object-fit:cover;">
                    <div class="card-body">
                        <h5 class="card-title text-danger fw-bold"><?php echo htmlspecialchars($book['title']); ?></h5>
                        <p class="mb-1"><strong>Author:</strong> <?php echo htmlspecialchars($book['author']); ?></p>
                        <p class="mb-1"><strong>ISBN:</strong> <?php echo htmlspecialchars($book['isbn']); ?></p>
                        <p class="mb-1"><strong>Category:</strong> <?php echo htmlspecialchars($book['category_name']); ?></p>
                        <p class="mb-1"><strong>Condition:</strong> <?php echo htmlspecialchars($book['condition']); ?></p>
                        <p class="mb-1"><strong>Language:</strong> <?php echo htmlspecialchars($book['language']); ?></p>
                        <p class="mb-1 d-flex align-items-center">
                          <strong>Quantity:</strong> 
                          <span class="ms-1"><?php echo htmlspecialchars($book['quantity']); ?></span>

                          <?php if ($book['quantity'] <= 0): ?>
                              <span class="badge bg-danger ms-2">Out of Stock</span>
                          <?php endif; ?>
                      </p>
                        <p class="mb-1"><strong>Type:</strong> <?php echo htmlspecialchars($book['type']); ?></p>
                        <?php if ($book['type'] === 'Paid'): ?>
                            <p class="mb-1"><strong>Price:</strong> ₹<?php echo htmlspecialchars($book['price']); ?></p>
                        <?php endif; ?>
                        <p class="text-truncate"><?php echo htmlspecialchars(substr($book['description'], 0, 80)) . '...'; ?></p>
                        <div class="d-flex justify-content-between mt-2">
                            <button class="btn btn-outline-secondary btn-sm editBookBtn"
                                    data-book='<?php echo json_encode($book); ?>'>
                                <i class="fa-solid fa-pen"></i> Edit
                            </button>
                            <a href="delete_book.php?id=<?php echo $book['book_id']; ?>" 
                                class="btn btn-outline-danger btn-sm"
                                onclick="return confirm('Are you sure you want to delete this book?');">
                                <i class="fa-solid fa-trash"></i> Delete
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
        </div>
    <?php endif; ?>
    </div>

    <script>
    const formContainer = document.getElementById('newPostFormContainer');
    const toggleBtn = document.getElementById('toggleNewPost');
    const formTitle = document.getElementById('formTitle');
    const submitBtn = document.getElementById('formSubmitBtn');
    const bookForm = document.getElementById('bookForm');
    const formMessage = document.getElementById('formMessage');

    toggleBtn.addEventListener('click', () => {
    resetForm();
    formContainer.style.display = formContainer.style.display === 'none' ? 'block' : 'none';
    formTitle.innerText = 'Post a New Book';
    submitBtn.innerText = 'Post';
    bookForm.action = 'upload_book.php';
    formMessage.innerHTML = '';
    window.scrollTo({ top: formContainer.offsetTop - 100, behavior: 'smooth' });
    });

    // Prefill edit form
    document.querySelectorAll('.editBookBtn').forEach(btn => {
    btn.addEventListener('click', () => {
        const data = JSON.parse(btn.dataset.book);
        formContainer.style.display = 'block';
        window.scrollTo({ top: formContainer.offsetTop - 100, behavior: 'smooth' });
        formTitle.innerText = 'Edit Book';
        submitBtn.innerText = 'Update';
        bookForm.action = 'update_book.php';

        document.getElementById('book_id').value = data.book_id;
        document.getElementById('title').value = data.title;
        document.getElementById('author').value = data.author;
        document.getElementById('isbn').value = data.isbn;
        document.getElementById('category_id').value = data.category_id;
        document.getElementById('condition').value = data.condition;
        document.getElementById('language').value = data.language;
        document.getElementById('quantity').value = data.quantity;
        document.getElementById('type').value = data.type;
        document.getElementById('price').value = data.price;
        document.getElementById('description').value = data.description;
        formMessage.innerHTML = '';
    });
    });

    function resetForm() {
    bookForm.reset();
    document.getElementById('book_id').value = '';
    }

    // Optional: success message handler
    if (window.location.search.includes('updated_book=1')) {
    formMessage.innerHTML = "<div class='alert alert-success mt-3'>Book updated successfully!</div>";
    setTimeout(() => window.location.href = 'profile.php', 2000);
    }
    </script>
    



<!-- PAAAAAAAAAAAAAAAAAAAAAAAAAAA-->

    <div id="orders-section" class="d-none">
        <h4 class="mb-3">My Orders</h4>

        <?php
        $orders = $conn->query("
            SELECT o.order_id, o.order_date, o.status, 
                  b.title, 
                  (SELECT path FROM book_images WHERE book_id=b.book_id LIMIT 1) AS image_path 
            FROM orders o
            JOIN books b ON o.book_id = b.book_id
            WHERE o.buyer_id = $user_id
            ORDER BY o.order_date DESC
        ");

        if ($orders->num_rows == 0) {
            echo "<p class='text-muted'>No orders placed yet.</p>";
        } else {
            echo "<div class='row g-3'>";
            while ($o = $orders->fetch_assoc()) {
                $img = $o['image_path'] ?: 'assets/default-book.png';
    ?>
                <!-- FULL CARD IS CLICKABLE -->
                <div class="col-12">
                    <a href="order_details.php?id=<?= $o['order_id'] ?>" class="text-decoration-none text-dark">
                        <div class="d-flex p-3 border rounded shadow-sm align-items-center" style="background:#fff;">
                            
                            <img src="<?= $img ?>" 
                                style="width:80px;height:80px;object-fit:cover;border-radius:6px;">

                            <div class="ms-3 flex-grow-1">
                                <h6 class="mb-1 fw-semibold"><?= htmlspecialchars($o['title']) ?></h6>
                                <p class="mb-1 text-muted" style="font-size:0.9rem;">
                                    Order Date: <?= date("d M Y", strtotime($o['order_date'])) ?>
                                </p>

                                <span class="badge 
                                    <?php if($o['status']=='Completed') echo 'bg-success';
                                          elseif($o['status']=='Pending') echo 'bg-warning text-dark';
                                          else echo 'bg-danger'; ?>">
                                    <?= $o['status'] ?>
                                </span>
                            </div>

                        </div>
                    </a>
                </div>
    <?php
            }
            echo "</div>";
        }
        ?>
    </div>

<!-- dddddddddddd -->
    <!-- RECEIVED ORDERS SECTION -->
    <div id="received_orders-section" class="d-none">
        <h4 class="mb-3">Received Orders</h4>

        <?php
        $received = $conn->query("
            SELECT 
                o.order_id, 
                o.order_date, 
                o.status,
                b.title,
                b.price,
                b.type,
                (SELECT path FROM book_images WHERE book_id=b.book_id LIMIT 1) AS image_path,

                -- Buyer Details
                u1.name  AS buyer_name,
                u1.email AS buyer_email,
                u1.house_no AS buyer_house,
                u1.city AS buyer_city,
                u1.state AS buyer_state,
                u1.pincode AS buyer_pincode

            FROM orders o
            JOIN books b ON o.book_id = b.book_id
            JOIN users u1 ON o.buyer_id = u1.id
            WHERE o.seller_id = $user_id
            ORDER BY o.order_date DESC
        ");

        if ($received->num_rows == 0) {
            echo "<p class='text-muted'>No received orders yet.</p>";
        } else {
        ?>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-danger">
                    <tr>
                        <th width="70">Order ID</th>
                        <th width="100">Image</th>
                        <th>Book Title</th>
                        <th width="200">Buyer</th>
                        <th width="180">Order Date</th>
                        <th width="230">Address</th>
                        <th width="110">Amount</th>
                        <th width="120">Status</th>
                    </tr>
                </thead>

                <tbody>
                <?php while($o = $received->fetch_assoc()):
                    $img = $o['image_path'] ? "uploads/books/".$o['image_path'] : "assets/default-book.png";

                    $amount = ($o['type'] === "Donated") ? "Donated" : "₹".$o['price'];

                    $address = $o['buyer_house'].", ".$o['buyer_city'].", ".$o['buyer_state']." - ".$o['buyer_pincode'];
                ?>
                    <tr>
                        <td><?= $o['order_id'] ?></td>

                        <td>
                            <img src="<?= $img ?>" 
                                style="width:70px;height:80px;object-fit:cover;border-radius:6px;">
                        </td>

                        <td><?= htmlspecialchars($o['title']) ?></td>

                        <td>
                            <b><?= htmlspecialchars($o['buyer_name']) ?></b><br>
                            <span class="text-muted small"><?= htmlspecialchars($o['buyer_email']) ?></span>
                        </td>

                        <td><?= date("d M Y | h:i A", strtotime($o['order_date'])) ?></td>

                        <td><?= htmlspecialchars($address) ?></td>

                        <td><?= $amount ?></td>

                        <td>
                            <?php if ($o['status']=="Completed"): ?>
                                <span class="badge bg-success">Delivered</span>
                            <?php elseif ($o['status']=="Pending"): ?>
                                <span class="badge bg-warning text-dark">Pending</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Canceled</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <?php } ?>
    </div>





    <div id="wishlist-section" class="d-none">
        <h4 class="mb-3">Wishlist</h4>
        <div class="row g-4">
            <?php
            $user_id = $_SESSION['user_id'];
            $query = "SELECT b.*, 
                        (SELECT path FROM book_images WHERE book_id=b.book_id LIMIT 1) AS image_path,
                        c.category_name
                    FROM wishlist w
                    JOIN books b ON w.book_id = b.book_id
                    LEFT JOIN categories c ON b.category_id = c.category_id
                    WHERE w.id=$user_id";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) > 0) {
                while ($book = mysqli_fetch_assoc($result)) {
                    $image = $book['image_path'] ? $book['image_path'] : 'assets/default-book.png';
            ?>
                <div class="col-6 col-md-4 col-lg-3">

                    <!-- ⭐ ONLY CHANGE: Entire card wrapped in anchor -->
                    <a href="bookdetails.php?id=<?= $book['book_id'] ?>" style="text-decoration:none; color:inherit;">

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
                    <!-- ⭐ ONLY CHANGE END -->

                </div>
            <?php
                }
            } else {
                echo "<p class='text-muted'>No books in wishlist yet.</p>";
            }
            ?>
        </div>
    </div>




  </div>
</div>

<!-- Password Change Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="POST">
        <input type="hidden" name="change_password" value="1">
        <div class="modal-header">
          <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Current Password</label>
            <input type="password" name="current_password" class="form-control" placeholder="Enter current password" required>
          </div>
          <div class="mb-3">
            <label class="form-label">New Password</label>
            <input type="password" name="new_password" class="form-control" placeholder="Enter new password" required>
          </div>
          <a href="forgot_password.php" class="text-danger" style="font-size: 0.9rem;">Forgot Password?</a>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Update Password</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>

<script>
// Sidebar tab switching
document.addEventListener('DOMContentLoaded', () => {
  const tabs = document.querySelectorAll('#profileTabs li');
  const sections = {
    profile: document.getElementById('profile-section'),
    posts: document.getElementById('posts-section'),
    orders: document.getElementById('orders-section'),
    received_orders: document.getElementById('received_orders-section'),
    wishlist: document.getElementById('wishlist-section')
    
  };

  // Function to switch to a specific section
  function activateSection(sectionName) {
    tabs.forEach(t => t.classList.remove('active'));
    Object.values(sections).forEach(s => s.classList.add('d-none'));

    const activeTab = document.querySelector(`#profileTabs li[data-section="${sectionName}"]`);
    if (activeTab) {
      activeTab.classList.add('active');
    }

    if (sections[sectionName]) {
      sections[sectionName].classList.remove('d-none');
      window.scrollTo({ top: document.querySelector('.content-area').offsetTop - 30, behavior: 'smooth' });
    }
  }

  // Default click-based switching
  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      const section = tab.dataset.section;
      activateSection(section);
      // Update the URL (optional, for browser back/forward support)
      const newUrl = `${window.location.pathname}?section=${section}`;
      window.history.replaceState({}, '', newUrl);
    });
  });

  // --- Open specific section via URL parameter (e.g. ?section=posts) ---
  const urlParams = new URLSearchParams(window.location.search);
  const sectionFromURL = urlParams.get('section');

  if (sectionFromURL && sections[sectionFromURL]) {
    activateSection(sectionFromURL);
  } else {
    activateSection('profile'); // default
  }
});

// Auto-hide alert after 5 seconds
document.addEventListener('DOMContentLoaded', () => {
  const alertBox = document.querySelector('.auto-hide-alert');
  if (alertBox) {
    setTimeout(() => {
      alertBox.classList.remove('show');
      alertBox.classList.add('fade');
      setTimeout(() => alertBox.remove(), 500);
    }, 5000);
  }
});

document.getElementById('type').addEventListener('change', function () {
    const price = document.getElementById('price');

    if (this.value === 'Donated') {
        price.value = "0.00";
        price.readOnly = true;
    } else {
        price.readOnly = false;
    }
});

let state = document.getElementById("state").value.trim();
if(state === ""){
    document.getElementById("stateError").innerText = "Please select your state.";
    valid = false;
}

let city = document.getElementById("city").value.trim();
if(city === ""){
    document.getElementById("cityError").innerText = "Please enter your city.";
    valid = false;
} else if (!/^[A-Za-z\s]+$/.test(city)) {
    document.getElementById("cityError").innerText = "City cannot contain numbers.";
    valid = false;
}

let pincode = document.getElementById("pincode").value.trim();
if(pincode === ""){
    document.getElementById("pincodeError").innerText = "Please enter pincode.";
    valid = false;
} else if(!/^[0-9]{6}$/.test(pincode)){
    document.getElementById("pincodeError").innerText = "Enter valid 6-digit pincode.";
    valid = false;
}

let house = document.getElementById("house").value.trim();
if(house === ""){
    document.getElementById("houseError").innerText = "Please enter house/building name.";
    valid = false;
}

document.getElementById("city").addEventListener("input", function(){
    this.value = this.value.replace(/[^A-Za-z\s]/g, '');
    document.getElementById("cityError").innerText="";
});

document.getElementById("pincode").addEventListener("input", function(){
    this.value = this.value.replace(/[^0-9]/g, '');
    document.getElementById("pincodeError").innerText="";
});


</script>
</body>
</html>
