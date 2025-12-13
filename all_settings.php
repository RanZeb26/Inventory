<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    header("Location: login");
    exit;
}
include 'config/db.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required for 🚚 Delivery

        *Dispatch or shipment tracking

        *Link with sales orders

        *Delivery status updates -->
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="vendors/typicons.font/font/typicons.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="css/vertical-layout-light/style.css">
    <link rel="stylesheet" href="css/product.css">
    <link rel="shortcut icon" href="images/favicon.png" />

    <title>Inventory</title>
    <style>
        body {
            background: #f8f9fa;
        }

        #sidebar {
            height: 100vh;
            width: 260px;
            position: fixed;
            left: 0;
            top: 0;
            background: #1b1b29;
            color: white;
            padding-top: 100px;
        }

        #sidebar a {
            color: white;
            padding: 15px 20px;
            display: block;
            font-size: 16px;
            text-decoration: none;
        }

        #sidebar a:hover {
            background: #1f2937;
        }

        #content {
            margin-left: 260px;
            padding: 25px;
            padding-top: 100px;
        }
    </style>
</head>

<body>
    <div class="container-scroller">
        <?php include 'Navbar/settings_nav.php'; ?>
        <!-- Sidebar -->
        <div id="sidebar">
            <a href="Dashboard"><i class="typcn typcn-chevron-left-outline"></i>Back to Dashboard</a>
            <hr style="border-top: 1px solid #374151;">
            <a href="#" onclick="loadPage('settings1')">Profile</a>
            <a href="#" onclick="loadPage('sales_receipts')">Sales Receipts</a>
            <a href="#" onclick="loadPage('items')">Items</a>
            <a href="#" onclick="loadPage('customers')">Customers</a>
            <a href="#" onclick="loadPage('add_products')">Add Products</a>
            <a href="#" onclick="loadPage('product_adjustment')">Product Adjustment</a>
            <a href="#" onclick="loadPage('adjustment_history')">Adjustment History</a>
            <a href="#" onclick="loadPage('reports')">Reports</a>
        </div>

        <!-- Main Content -->
        <div id="content">
            <h2>Welcome</h2>
            <p>Select a menu item to load content.</p>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="vendors/js/vendor.bundle.base.js"></script>
    <script src="js/off-canvas.js"></script>
    <script src="js/hoverable-collapse.js"></script>
    <script src="js/template.js"></script>
    <script src="js/settings.js"></script>
    <script src="js/todolist.js"></script>
    <script src="js/file-upload.js"></script>

    <script>
        function loadPage(page) {
            fetch(`pages/${page}.html`)
                .then(res => res.text())
                .then(html => {
                    document.getElementById("content").innerHTML = html;
                })
                .catch(() => {
                    document.getElementById("content").innerHTML = "<h3>Page not found.</h3>";
                });
        }
    </script>

</body>

</html>