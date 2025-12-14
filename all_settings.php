<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    http_response_code(401);
    exit;
}
include 'config/db.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>Inventory Settings</title>

    <!-- Vendor CSS -->
    <link rel="stylesheet" href="vendors/typicons.font/font/typicons.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="css/vertical-layout-light/style.css">
    <link rel="stylesheet" href="css/product.css">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="shortcut icon" href="images/favicon.png" />

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
            color: #fff;
            padding-top: 100px;
            z-index: 1000;
            transition: all .3s ease;
        }

        #sidebar a {
            color: #fff;
            padding: 15px 20px;
            display: block;
            font-size: 16px;
            text-decoration: none;
        }

        #sidebar a:hover,
        #sidebar a.active {
            background: #374151;
        }

        #sidebar.collapsed {
            width: 80px;
        }

        #content {
            margin-left: 260px;
            padding: 25px;
            padding-top: 100px;
            transition: all .3s ease;
        }

        #content.expanded {
            margin-left: 80px;
        }

        /* Mobile */
        @media (max-width: 768px) {
            #sidebar {
                left: -260px;
            }

            #sidebar.show {
                left: 0;
            }

            .mobile-toggle {
                margin-top: 0px;
            }

            #content,
            #content.expanded {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>

    <div class="container-scroller">

        <?php include 'Navbar/settings_nav.php'; ?>

        <!-- Sidebar -->
        <div id="sidebar">

            <!-- Desktop collapse 
            <div class="d-none d-md-block p-2">
                <button class="btn btn-sm btn-outline-light" onclick="collapseSidebar()">
                    <i class="typcn typcn-th-menu"></i>
                </button>
            </div>-->


            <!-- Mobile toggle -->
            <div class="d-md-none text-end p-3 mobile-toggle">
                <button class="btn btn-sm" onclick="toggleSidebar()">
                    <i style="color: #fff;" class="typcn typcn-chevron-left-outline"></i>
                </button>
            </div>

            <a href="Dashboard">
                <i class="typcn typcn-chevron-left-outline"></i> Back to Dashboard
            </a>

            <hr style="border-top:1px solid #374151">

            <a href="#" onclick="loadPage('settings_company', this)"><i class="typcn typcn-group"></i> Company Information</a>
            <a href="#" onclick="loadPage('items', this)"><i class="typcn typcn-shopping-bag"></i> Inventory Settings</a>
            <a href="#" onclick="loadPage('customers', this)"><i class="typcn typcn-document-text"></i> Documents Settings</a>
            <a href="#" onclick="loadPage('add_products', this)"><i class="typcn typcn-user"></i> User & Role</a>
            <a href="#" onclick="loadPage('product_adjustment', this)"><i class="typcn typcn-tag"></i> Tax</a>
            <a href="#" onclick="loadPage('adjustment_history', this)"><i class="typcn typcn-calculator"></i> Accounting</a>
            <a href="#" onclick="loadPage('reports', this)"><i class="typcn typcn-chart-pie"></i> Reports</a>
        </div>

        <!-- Main Content -->
        <div class="d-md-none text-end p-3">
            <button style="padding-top:100px;" class="btn btn-sm" onclick="toggleSidebar()">
                <i class="typcn typcn-chevron-right-outline"></i>
            </button>
        </div>
        <div id="content">

            <h2>⚙️ System Settings – Overview</h2>
            <p>
                The Settings module allows administrators and authorized users to configure
                how the inventory system operates.
            </p>

            <h4>🎯 Purpose of System Settings</h4>
            <ul>
                <li>Customize system behavior</li>
                <li>Ensure data accuracy</li>
                <li>Control access & permissions</li>
                <li>Integrate inventory, sales, and accounting</li>
            </ul>

            <h4>🧩 What You Can Configure</h4>

            <h5>🏢 Company & System Information</h5>
            <ul>
                <li>Company details</li>
                <li>Logo for documents</li>
                <li>Timezone & localization</li>
            </ul>

            <h5>📦 Inventory Configuration</h5>
            <ul>
                <li>Stock units & categories</li>
                <li>Low stock alerts</li>
                <li>Stock adjustment rules</li>
            </ul>

            <h5>🧾 Document Settings</h5>
            <ul>
                <li>Invoice & receipt numbering</li>
                <li>Currency & tax rules</li>
                <li>PDF layout defaults</li>
            </ul>

            <h5>👥 User & Role Management</h5>
            <ul>
                <li>User accounts</li>
                <li>Permission control</li>
                <li>Audit logs</li>
            </ul>

            <h5>📊 Reports & Notifications</h5>
            <ul>
                <li>Inventory movement reports</li>
                <li>Stock alerts</li>
                <li>Adjustment history</li>
            </ul>

        </div>
    </div>

    <!-- JS -->
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="vendors/js/vendor.bundle.base.js"></script>
    <script src="js/off-canvas.js"></script>
    <script src="js/hoverable-collapse.js"></script>
    <script src="js/template.js"></script>

    <script>
        const sidebar = document.getElementById("sidebar");
        const content = document.getElementById("content");

        let collapsed = false;

        const allowedPages = [
            "settings_company",
            "items",
            "customers",
            "add_products",
            "product_adjustment",
            "adjustment_history",
            "reports"
        ];

        function loadPage(page, el) {
            if (!allowedPages.includes(page)) return;

            localStorage.setItem("lastSettingsPage", page);

            document.querySelectorAll("#sidebar a").forEach(a => a.classList.remove("active"));
            if (el) el.classList.add("active");

            fetch(`Settings/${page}.php`)
                .then(res => {
                    if (res.status === 401) {
                        window.location.href = "login";
                        return;
                    }
                    return res.text();
                })
                .then(html => {
                    if (html) content.innerHTML = html;
                });
        }

        function collapseSidebar() {
            collapsed = !collapsed;
            localStorage.setItem("sidebarCollapsed", collapsed);
            const chevron = document.querySelector("#sidebar .typcn-chevron-left-outline");
            if (chevron) {
                chevron.classList.toggle("typcn-chevron-right-outline");

            }
            sidebar.classList.toggle("collapsed");
            content.classList.toggle("expanded");
        }

        function toggleSidebar() {
            sidebar.classList.toggle("show");
        }

        window.onload = () => {
            if (localStorage.getItem("sidebarCollapsed") === "true") {
                sidebar.classList.add("collapsed");
                content.classList.add("expanded");
                collapsed = true;
            }

            const lastPage = localStorage.getItem("lastSettingsPage");
            if (lastPage) loadPage(lastPage);
        };
    </script>

</body>

</html>