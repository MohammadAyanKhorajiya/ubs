<?php
include '../backend/config.php';
include "sidebar.php";

// DELETE USER (with their books)
if (isset($_GET['delete'])) {
    $uid = intval($_GET['delete']);

    // Delete user's books first
    $conn->query("DELETE FROM books WHERE user_id=$uid");

    // Then delete user
    $conn->query("DELETE FROM users WHERE id=$uid");

    header("Location: manage_users.php");
    exit();
}

// BLOCK / UNBLOCK USER
if (isset($_GET['block'])) {
    $uid = intval($_GET['block']);
    $conn->query("UPDATE users SET is_blocked = 1 WHERE id=$uid");
    header("Location: manage_users.php");
    exit();
}

if (isset($_GET['unblock'])) {
    $uid = intval($_GET['unblock']);
    $conn->query("UPDATE users SET is_blocked = 0 WHERE id=$uid");
    header("Location: manage_users.php");
    exit();
}

// FETCH ALL USERS (only normal users)
$users = $conn->query("SELECT id, name, email, phone, state, city, pincode, house_no, created_at, is_blocked 
                       FROM users 
                       WHERE role='user'
                       ORDER BY id DESC");
?>

<h2 class="mb-4">Manage Users</h2>

<div class="table-responsive bg-white">
    <table class="table table-bordered table-hover align-middle">
        <thead class="table-danger">
            <tr>
                <th width="50">ID</th>
                <th>Name</th>
                <th>Email</th>
                <th width="100">Phone</th>
                <th width="120">State</th>
                <th width="120">City</th>
                <th width="70">Pincode</th>
                <th width="180">House No.</th>
                <th width="170">Joined</th>
                <th width="70">Status</th>
                <th width="200">Actions</th>
            </tr>
        </thead>

        <tbody>
        <?php while ($row = $users->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['phone']) ?></td>
                <td><?= htmlspecialchars($row['state']) ?></td>
                <td><?= htmlspecialchars($row['city']) ?></td>
                <td><?= htmlspecialchars($row['pincode']) ?></td>
                <td><?= htmlspecialchars($row['house_no']) ?></td>
                <td><?= date("d M Y", strtotime($row['created_at'])) ?></td>
                <td>
                    <?php if ($row['is_blocked'] == 1): ?>
                        <span class="badge text-dark">Blocked</span>
                    <?php else: ?>
                        <span class="badge text-dark">Active</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($row['is_blocked'] == 1): ?>
                        <a href="manage_users.php?unblock=<?= $row['id'] ?>" 
                           class="btn btn-success btn-sm">Unblock</a>
                    <?php else: ?>
                        <a href="manage_users.php?block=<?= $row['id'] ?>" 
                           class="btn btn-danger btn-sm">Block</a>
                    <?php endif; ?>

                    <a href="manage_users.php?delete=<?= $row['id'] ?>"
                       onclick="return confirm('Delete this user and all their books?')"
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
