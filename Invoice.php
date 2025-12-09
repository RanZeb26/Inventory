<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    header("Location: login");
    exit;
}
include 'config/db.php';
include 'Get/fetch_sales.php';
include 'Get/fetch_category_item.php';
?>
  <!-- Required for 💰 Sales

        *Record customer sales

        *Invoice generation

        *Track stock deduction upon sale -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inventory</title>
  <link rel="stylesheet" href="vendors/typicons.font/font/typicons.css">
  <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="css/vertical-layout-light/style.css">
  <link rel="stylesheet" href="css/product.css">
  <link rel="shortcut icon" href="images/favicon.png" />
</head>
<body>
  <div class="container-scroller">
    <!--Navbar-->
    <?php include 'Navbar/nav.php'; ?>
    <div class="container-fluid page-body-wrapper">
      <!-- Theme Settings Panel -->
      <div class="theme-setting-wrapper">
        <div id="settings-trigger"><i class="typcn typcn-cog-outline"></i></div>
        <div id="theme-settings" class="settings-panel">
          <i class="settings-close typcn typcn-delete-outline"></i>
          <p class="settings-heading">Sidebar Settings</p>
           <nav>
        <ul class="nav">
          <li class="sidesetings col-12">
            <a class="nav-link hover:text-blue-500 dark:hover:text-blue-300" href="#">
              <!--<i class="typcn typcn-device-desktop menu-icon"></i>-->
              <span data-bs-toggle="modal" data-bs-target="#add_category_Modal">Add Category</span>
            </a>
          </li>
          <li class="sidesetings col-12">
            <a class="nav-link" href="Products">
              <!--<i class="typcn typcn-dropbox menu-icon"></i>-->
              <span class="menu-title">Add Unit</span>
            </a>
          </li>
        </ul>
           </nav>
        </div>
      </div>
      <!--Sidebar-->
      <?php include 'sidebar.php'; ?>
      <!--Main Panel-->
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="col-12 grid-margin">
            <div class="card-body">
              <div class="row">
                <div class="col-lg-12 d-flex grid-margin stretch-card">
                  <div class="card">
                    <div class="card-body">
                      <div id="editResponseMessage"></div>
                      <div class="row">
                      <div class="col-md-4">
            <div class="d-flex justify-content-between mb-3">
                <h3 id="listTitle">Invoices</h3>

                <!-- BUTTONS -->
                <div>
                    <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">+ New Invoice</button>
                </div>
            </div>
            <div id="listContainer"></div>
                      </div>
                              <div class="col-md-7">
            <div id="previewContainer" class="text-center text-muted mt-5">
                <p>Select an item to preview</p>
            </div>
        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- -------------------- MODALS ---------------------- -->

<!-- ADD MODAL -->
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog modal-mb">
    <div class="modal-content">
      
      <form id="addForm">
        <div class="modal-header">
          <h5 class="modal-title">Add New Invoice</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          <div class="row g-3">
            <div class="col-md-6">
              <label>Customer Name</label>
              <input type="text" class="form-control" name="customer">
            </div>

            <div class="col-md-3">
              <label>Date</label>
              <input type="date" class="form-control" name="date">
            </div>

            <div class="col-md-3">
              <label>Amount</label>
              <input type="number" class="form-control" name="amount">
            </div>
          </div>

        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-primary">Save Invoice</button>
        </div>

      </form>

    </div>
  </div>
</div>

<!-- EDIT MODAL -->
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog modal-mb">
    <div class="modal-content">

      <form id="editForm">
        <div class="modal-header">
          <h5 class="modal-title">Edit Invoice</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="id">

          <div class="row g-3">
            <div class="col-md-6">
              <label>Customer Name</label>
              <input type="text" class="form-control" name="customer">
            </div>

            <div class="col-md-3">
              <label>Date</label>
              <input type="date" class="form-control" name="date">
            </div>

            <div class="col-md-3">
              <label>Amount</label>
              <input type="number" class="form-control" name="amount">
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-danger" id="deleteBtn">Delete</button>
          <button class="btn btn-primary">Save Changes</button>
        </div>
      
      </form>

    </div>
  </div>
</div>
          <footer class="footer">
            <div class="d-sm-flex justify-content-center justify-content-sm-between">
              <span class="text-center text-sm-left d-block d-sm-inline-block">Copyright © <a href="#">randolfh.com</a> 2025</span>
            </div>
          </footer>
      </div>
      <!-- End of Main Panel -->
    </div>
  </div>
<!-- DataTables Activation Script -->


  <script src="js/bootstrap.bundle.min.js"></script>
  <script src="vendors/js/vendor.bundle.base.js"></script>
  <script src="js/off-canvas.js"></script>
  <script src="js/hoverable-collapse.js"></script>
  <script src="js/template.js"></script>
  <script src="js/settings.js"></script>
  <script src="js/todolist.js"></script>
  <script src="js/file-upload.js"></script>
  <script>
// TEMP LOCAL DATA (will be replaced by PHP + MySQL)
let data = {
    "Invoices": [
        { id: 1, code: "INV-0001", date: "2025-01-10", customer: "ABC Corp", amount: 15000 },
        { id: 2, code: "INV-0002", date: "2025-01-12", customer: "John Doe", amount: 8950 },
        { id: 3, code: "INV-0003", date: "2025-01-14", customer: "Metro Supplies", amount: 32000 },
    ],
    "Bills": [
        { id: 1, code: "BILL-9001", date: "2025-01-08", vendor: "Water Utility", amount: 2500 },
    ]
};

function loadMenu(menu) {
    document.getElementById("listTitle").innerText = menu;

    let list = data[menu] || [];
    let html = "";

    list.forEach(item => {
        html += `
        <div class="card mb-2 invoice-item" onclick='loadPreview(${JSON.stringify(item)})'>
            <div class="card-body border">
                <strong>${item.code}</strong><br>
                <small>${item.date}</small><br>
                <span>${item.customer || item.vendor}</span>
            </div>
        </div>
        `;
    });

    document.getElementById("listContainer").innerHTML = html;
}

function loadPreview(item) {
    document.getElementById("previewContainer").innerHTML = `
        <div class="preview-box">
            <span class="tag">Draft</span>
            <h3 class="mt-3">INVOICE</h3>
            <p class="mt-2"><strong># ${item.code}</strong></p>

            <p><strong>Date:</strong> ${item.date}</p>
            <p><strong>Name:</strong> ${item.customer || item.vendor}</p>
            <p><strong>Amount Due:</strong> ₱${item.amount.toLocaleString()}</p>

            <button class="btn btn-sm btn-primary mt-3" onclick='editInvoice(${JSON.stringify(item)})'
                    data-bs-toggle="modal" data-bs-target="#editModal">
                Edit Invoice
            </button>
        </div>
    `;
}

function editInvoice(item) {
    document.querySelector("#editForm [name=id]").value = item.id;
    document.querySelector("#editForm [name=customer]").value = item.customer;
    document.querySelector("#editForm [name=date]").value = item.date;
    document.querySelector("#editForm [name=amount]").value = item.amount;
}

// Default load
loadMenu("Invoices");
</script>
</body>
</html>
