<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
  header("Location: login");
  exit;
}
include 'config/db.php';
include 'Get/fetch_sales.php';
include 'Get/fetch_list_customer.php';
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
                          <div class="left-list bg-white border-end" id="listContainer" style="position: sticky; overflow-y: auto; height: 80vh;"></div>
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
          <div class="modal-dialog modal-lg">
            <div class="modal-content">

              <form id="addForm">
                <div class="modal-header">
                  <h5 class="modal-title">Add New Invoice</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                  <div class="row g-3">
                    <!-- INVOICE NUMBER -->
                    <div class="col-md-4">
                      <label>Invoice #</label>
                      <input type="text" class="form-control" name="invoice_number" required>
                    </div>
                    <!-- ORDER NUMBER -->
                    <div class="col-md-4">
                      <label>Order #</label>
                      <input type="text" class="form-control" name="order_number" required>
                    </div>
                    <!-- INVOICE DATE -->
                    <div class="col-md-4">
                      <label>Invoice Date</label>
                      <input type="date" class="form-control" name="date">
                    </div>
                    <!-- CUSTOMER SELECT -->
                    <div class="col-md-4">
                      <label>Customer Name</label>
                      <select name="customer_id" id="customerSelect" class="form-control" required>
                        <option value="" disabled selected>Select Customer</option>
                        <?php foreach ($category as $categories): ?>
                          <option
                            value="<?= $categories['customer_id'] ?>"
                            data-customername="<?= htmlspecialchars($categories['customer_name']) ?>"
                            data-companyname="<?= $categories['company_name'] ?>">
                            <?= htmlspecialchars($categories['customer_name']) ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <!-- HIDDEN NAME -->
                    <input type="hidden" name="name" id="customerName">
                    <!-- Company SELECT -->
                    <div class="col-md-4">
                      <label>Company Name</label>
                      <input type="text" class="form-control" name="company_name" id="companyName" readonly>
                    </div>
                    <!-- Due DATE -->
                    <div class="col-md-4">
                      <label>Due Date</label>
                      <input type="date" class="form-control" name="due_date">
                    </div>
                    <!-- SUBJECT -->
                    <div class="col-md-8">
                      <label>Subject</label>
                      <input type="text" class="form-control" name="subject" required>
                    </div>

                    <div class="card p-3">
                      <h5>Item Table</h5>

                      <table class="table table-bordered align-middle">
                        <thead>
                          <tr>
                            <th style="width: 30%">Item Details</th>
                            <th style="width: 10%">Qty</th>
                            <th style="width: 15%">Price</th>
                            <th style="width: 10%">Discount (%)</th>
                            <th style="width: 15%">Tax</th>
                            <th style="width: 15%">Amount</th>
                            <th style="width: 5%"></th>
                          </tr>
                        </thead>

                        <tbody id="itemRows">
                          <tr>
                            <td>
                              <div class="item-dropdown-wrapper" style="position: relative;">
                                <input type="text" class="form-control item-input" placeholder="Type or click to select an item">

                                <div class="dropdown-menu item-dropdown w-100"></div>
                              </div>
                            </td>

                            <td><input type="number" class="form-control qty" value="1"></td>
                            <td><input type="number" class="form-control rate" value="0"></td>
                            <td><input type="number" class="form-control discount" value="0"></td>

                            <td>
                              <select class="form-control tax">
                                <option value="0">None</option>
                                <option value="5">5%</option>
                                <option value="12">12%</option>
                              </select>
                            </td>

                            <td><input type="text" class="form-control amount" value="0" readonly></td>

                            <td>
                              <button class="btn btn-danger btn-sm removeRow">&times;</button>
                            </td>
                          </tr>
                        </tbody>
                      </table>

                      <button id="addRow" class="btn btn-info btn-sm">+ Add New Item</button>
                      <div class="row mt-4">
                        <div class="col-md-6"></div>

                        <div class="col-md-6">
                          <div class="border rounded p-3 bg-light">

                            <h6 class="fw-bold">Sub Total</h6>

                            <div class="d-flex justify-content-between mb-2">
                              <span>Item Total</span>
                              <span id="subtotal">0.00</span>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                              <span>Shipping Charges</span>
                              <input type="number" id="shipping" class="form-control form-control-sm w-50" value="0">
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                              <span>VAT (7.5%)</span>
                              <span id="vat">0.00</span>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                              <span>Adjustment</span>
                              <input type="number" id="adjustment" class="form-control form-control-sm w-50" value="0">
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between mb-2 fw-bold">
                              <span>Total Amount</span>
                              <span id="grand_total">0.00</span>
                            </div>

                          </div>
                        </div>
                      </div>

                    </div>

                  </div>

                </div>

                <div class="modal-footer">
                  <button class="btn btn-info">Save Invoice</button>
                  <button class="btn btn-danger" data-bs-dismiss="modal">Close</button>
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
    document.getElementById('customerSelect').addEventListener('change', function() {
      let customername = this.options[this.selectedIndex].getAttribute('data-customername');
      let companyname = this.options[this.selectedIndex].getAttribute('data-companyname');

      document.getElementById('customerName').value = customername;
      document.getElementById('companyName').value = companyname;
    });
    let items = [];

    // Load items from database
    function loadItems() {
      fetch("fetch_product_price.php")
        .then(res => res.json())
        .then(data => {
          items = data; // Save globally
          console.log("Items loaded:", items);
        })
        .catch(err => console.error(err));
    }

    loadItems(); // Call on page load
    document.addEventListener("input", function(e) {
      if (e.target.classList.contains("item-input")) {

        const wrapper = e.target.closest(".item-dropdown-wrapper");
        const dropdown = wrapper.querySelector(".item-dropdown");
        const search = e.target.value.toLowerCase();

        // Filter items
        const filtered = items.filter(item =>
          item.name.toLowerCase().includes(search)
        );

        // Create dropdown list
        dropdown.innerHTML = filtered.map(i => `
      <button class="dropdown-item select-item" data-name="${i.name}" data-rate="${i.selling_price}">
        ${i.name} <span class="text-muted float-end">₱${i.selling_price}</span>
      </button>
    `).join("");

        dropdown.classList.add("show");
      }
    });
    document.addEventListener("click", function(e) {
      if (e.target.classList.contains("select-item")) {

        const name = e.target.dataset.name;
        const rate = e.target.dataset.rate;

        const wrapper = e.target.closest(".item-dropdown-wrapper");
        wrapper.querySelector(".item-input").value = name;

        // Insert rate into the "rate" column
        const row = wrapper.closest("tr");
        row.querySelector(".rate").value = rate;

        // Recompute totals
        computeRow(row);

        // Hide dropdown
        wrapper.querySelector(".item-dropdown").classList.remove("show");
      }
    });
    // Recompute row on input change
    document.addEventListener("input", function(e) {
      if (e.target.classList.contains("qty") ||
        e.target.classList.contains("rate") ||
        e.target.classList.contains("discount") ||
        e.target.classList.contains("tax")) {
        const row = e.target.closest("tr");
        computeRow(row);
      }
    });

    // Compute Amount per row
    function computeRow(row) {
      let qty = parseFloat(row.querySelector(".qty").value) || 0;
      let rate = parseFloat(row.querySelector(".rate").value) || 0;
      let discount = parseFloat(row.querySelector(".discount").value) || 0;
      let taxPercent = parseFloat(row.querySelector(".tax").value) || 0;

      let base = qty * rate;
      let lessDiscount = base - (base * (discount / 100));
      let taxAmount = lessDiscount * (taxPercent / 100);
      let total = lessDiscount + taxAmount;

      // If amount is an input field
      if (row.querySelector(".amount").tagName === "INPUT") {
        row.querySelector(".amount").value = total.toFixed(2);
      } else {
        row.querySelector(".amount").textContent = total.toFixed(2);
      }

      computeTotals(); // update totals
    }

    // Compute all totals
    function computeTotals() {
      let subtotal = 0;

      document.querySelectorAll(".amount").forEach(a => {
        let val = (a.tagName === "INPUT") ? a.value : a.textContent;
        subtotal += parseFloat(val) || 0;
      });

      let shipping = parseFloat(document.getElementById("shipping").value) || 0;
      let adjustment = parseFloat(document.getElementById("adjustment").value) || 0;
      let vat = subtotal * 0.075; // 7.5%

      document.getElementById("subtotal").textContent = subtotal.toFixed(2);
      document.getElementById("vat").textContent = vat.toFixed(2);

      let grand = subtotal + vat + shipping + adjustment;
      document.getElementById("grand_total").textContent = grand.toFixed(2);
    }

    // Trigger recalculation when shipping or adjustment changes
    document.getElementById("shipping").addEventListener("input", computeTotals);
    document.getElementById("adjustment").addEventListener("input", computeTotals);


    // Add Row
    document.getElementById("addRow").onclick = function() {
      let row = document.querySelector("tbody tr").cloneNode(true);
      row.querySelectorAll("input").forEach(i => i.value = 0);
      document.getElementById("itemRows").appendChild(row);
    };

    // Remove Row
    document.addEventListener("click", function(e) {
      if (e.target.classList.contains("removeRow")) {
        e.target.closest("tr").remove();
      }
    });

    // TEMP LOCAL DATA (will be replaced by PHP + MySQL)
    let data = {
      "Invoices": [{
          id: 1,
          code: "INV-0001",
          date: "2025-01-10",
          customer: "ABC Corp",
          amount: 15000
        },
        {
          id: 2,
          code: "INV-0002",
          date: "2025-01-12",
          customer: "John Doe",
          amount: 8950
        },
        {
          id: 3,
          code: "INV-0003",
          date: "2025-01-14",
          customer: "Metro Supplies",
          amount: 32000
        },
      ],
      "Bills": [{
        id: 1,
        code: "BILL-9001",
        date: "2025-01-08",
        vendor: "Water Utility",
        amount: 2500
      }, ]
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