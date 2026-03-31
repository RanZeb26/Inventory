<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
  header("Location: login");
  exit;
}
include 'config/db.php';
include 'Get/fetch_list_customer.php';
include 'Get/fetch_coa.php';
include 'Get/fetch_payment_option.php';
?>
<!-- Required for 💰 Sales

        *Record customer sales

        *Invoice generation

        *Track stock deduction upon sale -->
<!DOCTYPE html>
<html lang="en">
<style>
  .modal-header .btn-close {
    font-size: 1.5rem;
    font-weight: bold;
    color: #fff;
    border: none;
    border-radius: 10%;
    width: 30px;
    height: 30px;
    line-height: 30px;
    text-align: center;
    cursor: pointer;
  }

</style>
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
                  <span data-bs-toggle="modal" data-bs-target="#add_payment_mode_Modal">Add Payment Mode</span>
                </a>
              </li>
              <!--
              <li class="sidesetings col-12">
                <a class="nav-link" href="Products">
                  <i class="typcn typcn-dropbox menu-icon"></i>
                  <span class="menu-title">Add Unit</span>
                </a>
              </li>-->
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
                      <!-- Add Payment Mode Modal -->
                      <div class="modal fade" id="add_payment_mode_Modal" tabindex="-1" aria-labelledby="add_payment_mode_ModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-md">
                          <div class="modal-content">
                            <form id="itemForm" action="add_payment_mode" method="POST">
                              <!-- <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>"> -->
                              <div class="modal-header">
                                <h5 class="modal-title" id="add_payment_mode_ModalLabel">Add Payment Mode</h5>
                                <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal">&times;</button>
                              </div>
                              <div class="modal-body">
                                <div class="container-fluid">
                                  <div class="row g-3">
                                    <div class="col-md-12">
                                      <label class="form-label">Payment Mode</label>
                                      <input type="text" name="name" class="form-control" required>
                                    </div>
                                    <div class="col-md-12">
                                      <div class="form-group">
                                        <label for="exampleSelectGender">Status</label>
                                        <select class="form-control" name="status" id="exampleSelectGender">
                                          <option>Active</option>
                                          <option>Inactive</option>
                                        </select>
                                      </div>

                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="modal-footer">
                                <button type="submit" class="btn btn-info">Save Payment Mode</button>
                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                      <!-- END OF ADD Payment MODAL -->

        <!-- ADD MODAL -->
        <div class="modal fade" id="addModal" tabindex="-1">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">

              <form id="addForm" method="POST" action="add_invoice">
                <div class="modal-header">
                  <h5 class="modal-title">Add New Invoice</h5>
                  <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal">&times;</button>
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
                      <h4>Item Table</h4>

                      <table class="table table-bordered align-middle border rounded p-3 bg-light">
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
                              <button type="button" class="btn btn-danger btn-sm removeRow">&times;</button>
                            </td>
                          </tr>
                        </tbody>
                      </table>

                      <button type="button" id="addRow" class="btn btn-info btn-sm">+ Add New Item</button>
                      <!-- TOTALS -->
                      <div class="row mt-4">
                        <!-- LEFT BLANK -->
                        <div class="col-md-6">
                          <div class="border rounded p-3 bg-light row g-3">
                            <div class="col-md-4">
                              <label>Payment Mode</label>
                              <select name="payment_id" id="paymentSelect" class="form-control" required>
                                <option value="" disabled selected>Select payment mode</option>
                                <?php foreach ($payment as $payments): ?>
                                  <option
                                    value="<?= $payments['id'] ?>"
                                    data-customername="<?= htmlspecialchars($payments['name']) ?>">
                                    <?= htmlspecialchars($payments['name']) ?>
                                  </option>
                                <?php endforeach; ?>
                              </select>
                            </div>
                            <div class="col-md-4">
                              <label>Deposit to</label>
                              <select name="deposit_id" id="coaSelect" class="form-control" required>
                                <option value="" disabled selected>Select accounts</option>
                                <?php foreach ($account as $accounts): ?>
                                  <option
                                    value="<?= $accounts['id'] ?>"
                                    data-accountcode="<?= $accounts['account_code'] ?>">
                                    <?= htmlspecialchars($accounts['account_code']) ?> - <?= htmlspecialchars($accounts['account_name']) ?>
                                  </option>
                                <?php endforeach; ?>
                              </select>
                            </div>
                            <div class="col-md-4">
                              <label>Reference #</label>
                              <input type="text" style="height: 35px;" class="form-control" name="reference" id="reference">
                            </div>
                            <div class="col-md-12">
                              <label>Notes</label>
                              <textarea class="form-control" name="notes" rows="3"></textarea>
                            </div>
                          </div>
                        </div>
                        <!-- Right TOTALS -->
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
                              <span>Adjustment</span>
                              <input type="number" id="adjustment" class="form-control form-control-sm w-50" value="0">
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between mb-2 fw-bold">
                              <span style="font-weight: bold;">Total Amount</span>
                              <span style="font-weight: bold;" id="grand_total" >0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                              <span>Vatable</span>
                              <span id="vatable">0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                              <span>VAT Amt</span>
                              <span id="vat">0.00</span>
                              <input type="hidden" name="subtotal" id="subtotal_input">
                              <input type="hidden" name="tax_amount" id="tax_input">
                              <input type="hidden" name="total_amount" id="grand_total_input">
                              <input type="hidden" name="discount_total" id="discount_input">
                            </div>

                          </div>
                        </div>
                        <!-- END Right TOTALS -->
                      </div>
                      <!-- END TOTALS -->
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
<!-- END OF ADD Invoice MODAL -->

        <!-- EDIT MODAL -->
        <div class="modal fade" id="editModal" tabindex="-1">
          <div class="modal-dialog modal-mb">
            <div class="modal-content">

              <form id="editForm">
                <div class="modal-header">
                  <h5 class="modal-title">Edit Invoice</h5>
                  <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal">&times;</button>
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
                  <!-- <button class="btn btn-danger" id="deleteBtn" name="deleteBtn">Delete</button> -->
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
    // AUTO-FILL COMPANY NAME BASED ON CUSTOMER SELECTION
    document.getElementById('customerSelect').addEventListener('change', function() {
      let customername = this.options[this.selectedIndex].getAttribute('data-customername');
      let companyname = this.options[this.selectedIndex].getAttribute('data-companyname');

      document.getElementById('customerName').value = customername;
      document.getElementById('companyName').value = companyname;
    });
    // GLOBAL ITEMS ARRAY
    let items = [];

    // Load items from database
    function loadItems() {
      fetch("fetch_product_price.php")
        .then(res => res.json())
        .then(data => {
          items = data; // Save globally
          //console.log("Items loaded:", items);
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


      <button type="button" class="dropdown-item select-item" data-id="${i.product_id}" data-name="${i.name}" data-rate="${i.selling_price}">
        ${i.name} <span class="text-muted float-end"> ₱${i.selling_price}</span>
      </button>
    `).join("");

        dropdown.classList.add("show");
      }
    });
document.addEventListener("click", function(e) {
  if (e.target.classList.contains("select-item")) {
    e.preventDefault(); // ⛔ stop form submit

const id = e.target.dataset.id;
const name = e.target.dataset.name;
const rate = e.target.dataset.rate;

const wrapper = e.target.closest(".item-dropdown-wrapper");

// set visible name
wrapper.querySelector(".item-input").value = name;

// 👉 store product_id (hidden)
let hidden = wrapper.querySelector(".product-id");

if (!hidden) {
  hidden = document.createElement("input");
  hidden.type = "hidden";
  hidden.classList.add("product-id");
  wrapper.appendChild(hidden);
}

hidden.value = id;

// set price
const row = wrapper.closest("tr");
row.querySelector(".rate").value = rate;

computeRow(row);

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
      document.getElementById("discount_input").value = discount.toFixed(2);
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
      

      document.getElementById("subtotal").textContent = subtotal.toFixed(2);
      document.getElementById("subtotal_input").value = subtotal.toFixed(2);
      

      let grand = subtotal + shipping + adjustment;
      document.getElementById("grand_total").textContent = grand.toFixed(2);
      document.getElementById("grand_total_input").value = grand.toFixed(2);

      let vat = grand / 1.12; // 7.5%
      let vatable = grand - vat;
      document.getElementById("vatable").textContent = vatable.toFixed(2);
      document.getElementById("vat").textContent = vat.toFixed(2);
      document.getElementById("tax_input").value = vat.toFixed(2);
    }

    // Trigger recalculation when shipping or adjustment changes
    document.getElementById("shipping").addEventListener("input", computeTotals);
    document.getElementById("adjustment").addEventListener("input", computeTotals);


    // Add Row
    document.getElementById("addRow").onclick = function() {
      let row = document.querySelector("tbody tr").cloneNode(true);
      //row.querySelectorAll("input").forEach(i => i.value = 0);
      row.querySelector(".item-input").value = "";
      row.querySelector(".qty").value = 1;
      row.querySelector(".rate").value = 0;
      row.querySelector(".discount").value = 0;
      row.querySelector(".amount").value = 0;
      document.getElementById("itemRows").appendChild(row);
    };

    // Remove Row
    document.addEventListener("click", function(e) {
if (e.target.classList.contains("removeRow")) {
  e.target.closest("tr").remove();
  computeTotals();
}
    });

    // Close dropdown when clicking outside
  document.addEventListener("click", function(e) {
  document.querySelectorAll(".item-dropdown").forEach(d => {
    if (!d.contains(e.target) && !e.target.classList.contains("item-input")) {
      d.classList.remove("show");
    }
  });
});

    // TEMP LOCAL DATA (will be replaced by PHP + MySQL)
    // let data = {
    //   "Invoices": [{
    //       id: 1,
    //       code: "INV-0001",
    //       date: "2025-01-10",
    //       customer: "ABC Corp",
    //       amount: 15000
    //     },
    //     {
    //       id: 2,
    //       code: "INV-0002",
    //       date: "2025-01-12",
    //       customer: "John Doe",
    //       amount: 8950
    //     },
    //     {
    //       id: 3,
    //       code: "INV-0003",
    //       date: "2025-01-14",
    //       customer: "Metro Supplies",
    //       amount: 32000
    //     },
    //   ],
    //   "Bills": [{
    //     id: 1,
    //     code: "BILL-9001",
    //     date: "2025-01-08",
    //     vendor: "Water Utility",
    //     amount: 2500
    //   }, ]
    // };

    //Submit items as JSON string
 document.getElementById("addForm").addEventListener("submit", function(e) {

  let rows = [];

  document.querySelectorAll("#itemRows tr").forEach(row => {
    rows.push({
      product_id: row.querySelector(".product-id")?.value || 0,
      qty: row.querySelector(".qty").value,
      rate: row.querySelector(".rate").value,
      tax: row.querySelector(".tax").value,
      amount: row.querySelector(".amount").value
    });
  });

  let hidden = document.createElement("input");
  hidden.type = "hidden";
  hidden.name = "items";
  hidden.value = JSON.stringify(rows);

  this.appendChild(hidden);
});

const urlParams = new URLSearchParams(window.location.search);
const selectedId = urlParams.get("id");

function loadInvoices() {
  fetch(`fetch_invoices?id=${selectedId || ""}`)
    .then(res => res.json())
    .then(data => {

      let html = "";

      data.forEach(item => {
        html += `
        <div class="card mb-2 invoice-item" data-id="${item.invoice_id}">
          <div class="card-body border">
            <strong>${item.invoice_no ?? ''}</strong><br>
            <small>${item.invoice_date ?? ''}</small><br>
            <span>${item.customer_name ?? ''}</span>
          </div>
        </div>
        `;
      });

      document.getElementById("listContainer").innerHTML = html;
    });
}
document.addEventListener("click", function(e) {
  const card = e.target.closest(".invoice-item");
  if (card) {
    document.querySelectorAll(".invoice-item").forEach(el => {
      el.classList.remove("active");
    });

    card.classList.add("active");

    const id = card.dataset.id;
    loadPreview(id);
  }
});

// Load invoice details for preview
function loadPreview(id) {
  fetch("fetch_invoice_details?id=" + id)
    .then(res => res.json())
    .then(data => {

  let h = data.header;
  let c = data.company; // 👈 company data
      let itemsHtml = "";

      data.items.forEach(i => {
        itemsHtml += `
          <tr>
            <td>${i.name}</td>
            <td class="text-center">${i.quantity}</td>
            <td class="text-end">₱${parseFloat(i.unit_price).toFixed(2)}</td>
            <td class="text-end">₱${parseFloat(i.line_total).toFixed(2)}</td>
          </tr>
        `;
      });

let logo = c.logo 
  ? `<img src="${c.logo}" style="height:60px;">`
  : '';

  //Preview HTML
    //   <div>
    //   <button class="btn btn-info d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addModal">
    //     <i class="typcn typcn-plus"></i>
    //     New Transaction
    //   </button>
    // </div>
document.getElementById("previewContainer").innerHTML = `
  <div class="preview-box p-4 bg-white shadow-sm">
<div class="row mb-3">
  <div class="col-md-12 d-flex justify-content-between align-items-center">
<input type="hidden" name="invoice_id" value="${h.invoice_id}">
    <!-- LEFT SIDE (Primary Action) -->

   <!-- ACTION -->
            <button class="btn btn-primary"
              onclick="editInvoice(${h.invoice_id})"
              data-bs-toggle="modal"
              data-bs-target="#editModal"><i class="typcn typcn-edit"></i>
              Edit
            </button>
    <!-- RIGHT SIDE (Actions) -->
    <div class="d-flex align-items-center gap-2">

      <button class="btn btn-light d-flex align-items-center gap-2" onclick="printInvoice()">
        <i class="typcn typcn-printer"></i>
        Print
      </button>

      <!-- 3 DOTS DROPDOWN -->
      <div class="dropdown">
        <button class="btn btn-basic" type="button" data-bs-toggle="dropdown">
          <i class="typcn typcn-cog-outline"></i>
        </button>

        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
          <li><a class="dropdown-item sendInvoiceBtn" href="#">
  <i class=" typcn typcn-mail"></i> Send Invoice
</a></li>
          <li><a class="dropdown-item" href="#" onclick="printInvoice()">
            <i class="typcn typcn-export "></i> Export PDF
          </a></li>
          <li><a class="dropdown-item" href="#">
            <i class="typcn typcn-tabs-outline"></i> Duplicate Invoice
          </a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item text-danger deleteBtn" href="#"><i class="typcn typcn-trash"></i> Delete Invoice</a></li>
        </ul>
      </div>

    </div>

  </div>
</div>
    <!-- HEADER -->
    <div class="d-flex justify-content-between border-bottom pb-3 mb-3">

      <div>
        ${logo}
        <h5 class="mt-2">${c.company_name}</h5>
        <small>${c.address}</small><br>
        <small>${c.contact}</small>
      </div>

      <div class="text-end">
        <h4>INVOICE</h4>
        <strong>#${h.invoice_no}</strong><br>
        <small>Date: ${h.invoice_date}</small>
      </div>

    </div>

          <!-- CUSTOMER INFO -->
          <div class="row mb-3">
            <div class="col-6">
              <strong>BILL TO:</strong><br>
              ${h.customer_name || '-'}
            </div>
            <div class="col-6 text-end">
              <strong>Status:</strong> ${getStatusBadge(h.status)} <br>
              <strong>Due:</strong> ${h.due_date || '-'}
            </div>
          </div>

          <!-- TABLE -->
          <div class="table-responsive">
          <table class="table table-bordered">
            <thead class="table-light">
              <tr>
                <th>Item</th>
                <th class="text-center">Qty</th>
                <th class="text-end">Price</th>
                <th class="text-end">Total</th>
              </tr>
            </thead>
            <tbody>
              ${itemsHtml}
            </tbody>
          </table>
</div>
          <!-- TOTALS -->
          <div class="row justify-content-end">
            <div class="col-md-5">

              <table class="table table-sm">
                <tr>
                  <td>Subtotal</td>
                  <td class="text-end">₱${parseFloat(h.subtotal).toFixed(2)}</td>
                </tr>
                <tr>
                  <td>Tax</td>
                  <td class="text-end">₱${parseFloat(h.tax_amount).toFixed(2)}</td>
                </tr>
                <tr>
                  <td>Discount</td>
                  <td class="text-end">₱${parseFloat(h.discount).toFixed(2)}</td>
                </tr>
                <tr class="fw-bold">
                  <td>Total</td>
                  <td class="text-end">₱${parseFloat(h.total_amount).toFixed(2)}</td>
                </tr>
                <tr>
                  <td>Paid</td>
                  <td class="text-end">₱${parseFloat(h.amount_paid).toFixed(2)}</td>
                </tr>
                <tr class="fw-bold text-danger">
                  <td>Balance</td>
                  <td class="text-end">₱${parseFloat(h.balance).toFixed(2)}</td>
                </tr>
              </table>

            </div>
          </div>

          <!-- NOTES -->
          <div class="mt-3">
            <strong>Notes:</strong><br>
            ${h.notes || '-'}
          </div>

       
          

        </div>
      `;
    });
}
// Helper to display status badge
function getStatusBadge(status) {
  switch(status) {
    case 'Paid':
      return `<span class="badge bg-success">Paid</span>`;
    case 'Draft':
      return `<span class="badge bg-warning text-dark">Draft</span>`;
    case 'Overdue':
      return `<span class="badge bg-danger">Overdue</span>`;
    default:
      return `<span class="badge bg-secondary">${status}</span>`;
  }
}
// Print Invoice (opens in new window)
function printInvoice() {

  // Clone only the invoice content
  let content = document.querySelector(".preview-box").cloneNode(true);

  // ❌ Remove buttons (Edit, etc.)
  let list = content.querySelectorAll("ul")
  list.forEach(list=>list.remove());
  let buttons = content.querySelectorAll("button");
  buttons.forEach(btn => btn.remove());

let win = window.open("", "_blank");  

  win.document.write(`
    <html>
      <head>
        <title>Invoice</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

        <style>
          @page {
            size: A4;
            margin: 20mm;
          }

          body {
            font-family: Arial, sans-serif;
            background: #fff;
          }

          .invoice-container {
            max-width: 800px;
            margin: auto;
          }

          h4, h5 {
            margin: 0;
          }

          .table {
            width: 100%;
            border-collapse: collapse;
          }

          .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
          }

          .table th {
            background: #f5f5f5;
          }

          .text-end {
            text-align: right;
          }

          .text-center {
            text-align: center;
          }

          .fw-bold {
            font-weight: bold;
          }

          .totals-table td {
            border: none !important;
            padding: 4px 8px;
          }

          .totals-table tr:last-child td {
            border-top: 2px solid #000 !important;
            font-size: 16px;
          }

          .no-print {
            display: none;
          }
        </style>
      </head>

      <body>

        <div class="invoice-container">
          ${content.innerHTML}
        </div>

      </body>
    </html>
  `);

  win.document.close();

  // Wait for content to load before printing
  win.onload = function() {
    win.focus();
    win.print();
    win.close();
  };
}

// load on start
loadInvoices();
// Edit Invoice (pre-fill form)
function editInvoice(id) {
  fetch("fetch_invoice_details?id=" + id)
    .then(res => res.json())
    .then(data => {

      let h = data.header;

      // SET HEADER
      document.querySelector("#editForm [name=id]").value = h.invoice_id;
      document.querySelector("#editForm [name=customer]").value = h.customer_name;
      document.querySelector("#editForm [name=date]").value = h.invoice_date;
      document.querySelector("#editForm [name=amount]").value = h.total_amount;

      // LOAD ITEMS
      let tbody = document.getElementById("itemRows");
      tbody.innerHTML = "";

      data.items.forEach(i => {
        let row = `
        <tr>
          <td>
            <input type="text" class="form-control" value="${i.product_name}">
          </td>
          <td><input type="number" class="form-control" value="${i.quantity}"></td>
          <td><input type="number" class="form-control" value="${i.unit_price}"></td>
          <td><input type="text" class="form-control" value="${i.line_total}" readonly></td>
        </tr>
        `;
        tbody.innerHTML += row;
      });

    });
}

// Delete Invoice
// Use event delegation (works for dynamic content)
document.addEventListener("click", function(e) {
  if (e.target.classList.contains("deleteBtn")) {
    e.preventDefault();

    let id = document.querySelector("[name=invoice_id]").value;

    if (!id) {
      alert("No invoice selected.");
      return;
    }

    if (confirm("Delete this invoice?")) {
      fetch("delete_invoice", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "id=" + encodeURIComponent(id)
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === "success") {
          loadInvoices(); // reload list only
        } else {
          alert(data.message || "Delete failed");
        }
      })
      .catch(err => {
        console.error(err);
        alert("Something went wrong");
      });
    }
  }
});
  </script>
</body>

</html>