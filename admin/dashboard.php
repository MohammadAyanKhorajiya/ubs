<?php
include '../backend/config.php';
include "sidebar.php";
?>

<h2 class="mb-4">Dashboard</h2>

<div class="row">

    <!-- Total Users -->
    <div class="col-md-3 mb-3">
        <div class="p-4 bg-white shadow-sm rounded">
            <h5>Total Users</h5>
            <p class="text-danger fw-bold fs-4">
                <?php 
                    $result = $conn->query("SELECT COUNT(*) FROM users WHERE role='user'");
                    $count = $result->fetch_row()[0];
                    echo $count;
                ?>
            </p>
        </div>
    </div>

    <!-- Total Books -->
    <div class="col-md-3 mb-3">
        <div class="p-4 bg-white shadow-sm rounded">
            <h5>Total Books</h5>
            <p class="text-danger fw-bold fs-4">
                <?php 
                    $result = $conn->query("SELECT COUNT(*) FROM books");
                    $count = $result->fetch_row()[0];
                    echo $count;
                ?>
            </p>
        </div>
    </div>

    <!-- Total Orders -->
    <div class="col-md-3 mb-3">
        <div class="p-4 bg-white shadow-sm rounded">
            <h5>Total Orders</h5>
            <p class="text-danger fw-bold fs-4">
                <?php 
                    $result = $conn->query("SELECT COUNT(*) FROM orders");
                    $count = $result->fetch_row()[0];
                    echo $count;
                ?>
            </p>
        </div>
    </div>

    <!-- Pending Orders -->
    <div class="col-md-3 mb-3">
        <div class="p-4 bg-white shadow-sm rounded">
            <h5>Pending Orders</h5>
            <p class="text-danger fw-bold fs-4">
                <?php 
                    $result = $conn->query("SELECT COUNT(*) FROM orders WHERE status='Pending'");
                    $count = $result->fetch_row()[0];
                    echo $count;
                ?>
            </p>
        </div>
    </div>

</div>


<div class="row">

    <!-- Delivered Orders -->
    <div class="col-md-3 mb-3">
        <div class="p-4 bg-white shadow-sm rounded">
            <h5>Delivered Orders</h5>
            <p class="text-danger fw-bold fs-4">
                <?php 
                    $result = $conn->query("SELECT COUNT(*) FROM orders WHERE status='Completed'");
                    $count = $result->fetch_row()[0];
                    echo $count;
                ?>
            </p>
        </div>
    </div>

    <!-- Cancelled Orders -->
    <div class="col-md-3 mb-3">
        <div class="p-4 bg-white shadow-sm rounded">
            <h5>Cancelled Orders</h5>
            <p class="text-danger fw-bold fs-4">
                <?php 
                    $result = $conn->query("SELECT COUNT(*) FROM orders WHERE status='Canceled'");
                    $count = $result->fetch_row()[0];
                    echo $count;
                ?>
            </p>
        </div>
    </div>

</div>

</div>
</div>
</body>
</html>
