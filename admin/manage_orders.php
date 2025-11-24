<?php
include '../backend/config.php';
include "sidebar.php";

// UPDATE STATUS (AJAX)
if (isset($_POST['update_status'])) {
    $oid = intval($_POST['order_id']);
    $conn->query("UPDATE orders SET status='Completed' WHERE order_id=$oid");
    $get = $conn->query("SELECT book_id FROM orders WHERE order_id=$oid");
    $row = $get->fetch_assoc();
    $book_id = $row['book_id'];

    $getQty = $conn->query("SELECT quantity FROM books WHERE book_id=$book_id");
    $b = $getQty->fetch_assoc();
    $old_qty = $b['quantity'];

    $new_qty = $old_qty - 1;

    if ($new_qty <= 0) {
        $conn->query("UPDATE books SET quantity=0, status='Hidden' WHERE book_id=$book_id");
    } else {
        $conn->query("UPDATE books SET quantity=$new_qty WHERE book_id=$book_id");
    }

    echo "OK";
    exit();
}

// FETCH ORDERS
$orders = $conn->query("
    SELECT 
        o.order_id,
        o.order_date,
        o.status,

        b.title,
        b.price,
        b.type,

        bi.path AS image_path,

        u1.name  AS buyer_name,
        u1.email AS buyer_email,
        u1.house_no AS buyer_house,
        u1.city     AS buyer_city,
        u1.state    AS buyer_state,
        u1.pincode  AS buyer_pincode,

        u2.name  AS seller_name,
        u2.email AS seller_email,

        p.method AS payment_method,
        p.status AS payment_status

    FROM orders o
    JOIN books b        ON o.book_id  = b.book_id
    LEFT JOIN book_images bi ON bi.book_id = b.book_id
    JOIN users u1       ON o.buyer_id = u1.id
    JOIN users u2       ON o.seller_id = u2.id
    LEFT JOIN payments p     ON o.order_id = p.order_id

    ORDER BY o.order_id DESC
");

?>

<h2 class="mb-4">Manage Orders</h2>

<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
        <thead class="table-danger">
            <tr>
                <th width="70">Order ID</th>
                <th width="100">Image</th>
                <th>Book Title</th>
                <th width="200">Buyer</th>
                <th width="200">Seller</th>
                <th width="170">Order Date</th>
                <th width="220">Address</th>

                <!-- ✅ Added Summary Columns -->
                <th width="90">Items</th>
                <th width="110">Amount</th>
                <th width="120">Pay Status</th>

                <th width="130">Payment</th>
                <th width="120">Status</th>
                <th width="120">Action</th>
            </tr>
        </thead>

        <tbody>
        <?php while($row = $orders->fetch_assoc()): 
            $img = !empty($row['image_path']) 
                ? "../uploads/books/" . $row['image_path']
                : "../assets/default-book.png";

            $address = $row['buyer_house'] . ", " . $row['buyer_city'] . ", " .
                       $row['buyer_state'] . " - " . $row['buyer_pincode'];

            $amount = ($row['type'] === "Donated") ? 0 : $row['price'];
        ?>
            <tr>

                <td><?= $row['order_id'] ?></td>

                <td>
                    <img src="<?= $img ?>" 
                         style="width:70px; height:85px; object-fit:cover; border-radius:6px;">
                </td>

                <td><?= htmlspecialchars($row['title']) ?></td>

                <td>
                    <b><?= htmlspecialchars($row['buyer_name']) ?></b><br>
                    <span class="text-muted small"><?= htmlspecialchars($row['buyer_email']) ?></span>
                </td>

                <td>
                    <b><?= htmlspecialchars($row['seller_name']) ?></b><br>
                    <span class="text-muted small"><?= htmlspecialchars($row['seller_email']) ?></span>
                </td>

                <td><?= date("d M Y | h:i A", strtotime($row['order_date'])) ?></td>

                <td><?= htmlspecialchars($address) ?></td>

                <!-- ✅ Items (always 1 because single-book orders) -->
                <td>1</td>

                <!-- ✅ Amount -->
                <td><?= $amount == 0 ? "Donated" : "₹".$amount ?></td>

                <!-- ✅ Payment Status -->
                <td>
                    <?php
                    $ps = $row['payment_status'] ?: "Pending";

                    if ($ps == "Paid") {
                        echo "<span class='px-2 py-1 border border-success text-success rounded'>Paid</span>";
                    }
                    else if ($ps == "Failed") {
                        echo "<span class='px-2 py-1 border border-danger text-danger rounded'>Failed</span>";
                    }
                    else {
                        echo "<span class='px-2 py-1 border border-warning text-warning rounded'>Pending</span>";
                    }
                    ?>
                </td>

                <td><?= htmlspecialchars($row['payment_method'] ?? "N/A") ?></td>

                <td>
                    <?php 
                    if ($row['status'] == "Pending") {
                        echo "<span class='px-2 py-1 border border-warning text-warning rounded'>Pending</span>";
                    }
                    elseif ($row['status'] == 'Completed') {
                        echo "<span class='px-2 py-1 border border-success text-success rounded'>Delivered</span>";
                    }
                    else {
                        echo "<span class='px-2 py-1 border border-danger text-danger rounded'>Canceled</span>";
                    }
                    ?>

                </td>

                <td>
                    <?php if ($row['status'] == "Pending"): ?>
                        <button class="btn btn-success btn-sm"
                                onclick="markDelivered(<?= $row['order_id'] ?>)">
                            Deliver
                        </button>
                    <?php endif; ?>
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
function markDelivered(order_id) {
    if (!confirm("Mark this order as Delivered?")) return;

    fetch("manage_orders.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "update_status=1&order_id=" + order_id
    })
    .then(res => res.text())
    .then(text => {
        if (text.trim() === "OK") location.reload();
    });
}
</script>
