<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
  header("Location: login");
  exit;
}
include 'config/db.php';

include 'Get/fetch_customer.php';
include 'Get/fetch_products.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Inventory</title>
  <link rel="stylesheet" href="vendors/typicons.font/font/typicons.css">
  <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="css/vertical-layout-light/style.css">
  <link rel="shortcut icon" href="images/favicon.png" />
</head>
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
<body>
  <div class="container-scroller">
    <?php include 'Navbar/nav.php'; ?>
    <div class="container-fluid page-body-wrapper">
      <?php include 'sidebar.php'; ?>
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="col-12 grid-margin">
            <div class="card-body">
              <div class="row">
                <div class="col-lg-12 d-flex grid-margin stretch-card">
                  <div class="card">
                    <div class="card-body">
                      <h4 class="card-title">Invoices</h4>
                      <!--<a href="Adjustment" class="btn btn-primary mb-3"><< Back to Adjustment</a>-->
                      <div class="d-flex justify-content-end align-items-center mb-3">
                        <div class="input-group">
                          <form method="GET" class="mb-3 d-flex">
                            <input type="text" name="search" class="form-control me-2"
                              placeholder="Search by Invoice #" value="<?= htmlspecialchars($search) ?>">
                            <button class="btn btn-light"><i class="typcn typcn-zoom"></i></button>
                          </form>
                        </div>
                        <div class="control-form col-md-3">
                          <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#addModal">+ New Invoice</button>
                        </div>
                      </div>
                      <div class="table-responsive pt-3">
                        <table class="table table-hover bg-white shadow-sm">
                          <thead class="table-light">
                            <tr>
                              <th>Customer</th>
                              <th>Contact</th>
                              <th>Date</th>
                              <th>Status</th>
                              <th>Action</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php if (!empty($result)): ?>
                              <?php foreach ($result as $row): ?>
                                <tr>
                                  <td class="d-flex align-items-center">
                                    <!-- <img src="<?= !empty($row['image']) ? htmlspecialchars($row['image']) : 'images/default.png' ?>" alt="img" class="me-3" width="40" height="40" style="object-fit:cover; border-radius:5px; padding:2px; border:1px solid #ccc;"> -->
                                    <div>
                                      <div class="fw-bold" style="font-weight: 900;"> <?= $row['company_name'] ?></div>
                                      <div class="fw-bold"> <?= $row['customer_name'] ?></div>
                                      <div class="text-muted small">INV <?= $row['invoice_no'] ?></div>
                                    </div>
                                  </td>
                                  <td>
                                    <div class="fw-bold" style="font-weight: 900;"> <?= $row['email'] ?></div>
                                    <div class="fw-bold"> <?= $row['phone'] ?></div>
                                  </td>
                                  <td><?= date("d M, Y", strtotime($row['invoice_date'])) ?></td>
                                  <td><span style="color:white;" class="badge bg-<?= $row['status'] == 'Paid' ? 'success' : 'danger' ?>">
                                      <?= $row['status'] ?>
                                    </span></td>
                                  <td>
                                    <!-- EDIT BUTTON -->
                                    <button class="btn btn-inverse-warning btn-icon mr-2 edit-btn"
                                      data-bs-toggle="modal" data-bs-target="#editModal<?= $row['invoice_id'] ?>">
                                      <i class="typcn typcn-edit"></i>
                                    </button>
                                    <!-- VIEW BUTTON -->
                                    <button type="button" class="btn btn-inverse-info btn-icon mr-2 view-btn" onclick="redirectToList(<?= $row['customer_id'] ?>)">
                                      <i class="typcn typcn-eye-outline"></i>
                                    </button>
                                    <!-- DELETE BUTTON -->
                                    <button class="btn btn-inverse-danger btn-icon open-delete-modal"
                                      data-invoice_id="<?= $row['invoice_id'] ?>">
                                      <i class="typcn typcn-delete-outline"></i>
                                    </button>
                                  </td>
                                </tr>

                              <?php endforeach; ?>
                            <?php else: ?>
                              <tr>
                                <td colspan="7" class="text-center text-muted">No records found</td>
                              </tr>

                            <?php endif; ?>
                          </tbody>
                        </table>

                        <!-- Pagination -->
                        <nav>
                          <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                              <li class="page-item"><a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>">&laquo; Prev</a></li>
                            <?php endif; ?>
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                              <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                              </li>
                            <?php endfor; ?>
                            <?php if ($page < $total_pages): ?>
                              <li class="page-item"><a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>">Next &raquo;</a></li>
                            <?php endif; ?>
                          </ul>
                        </nav>
                      </div>

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
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <footer class="footer">
          <div class="d-sm-flex justify-content-center justify-content-sm-between">
            <span class="text-center text-sm-left d-block d-sm-inline-block">Copyright © <a href="#">randolfh.com</a> 2025</span>
          </div>
        </footer>
      </div>
    </div>
  </div>

  <script src="js/bootstrap.bundle.min.js"></script>
  <script src="vendors/js/vendor.bundle.base.js"></script>
  <script src="js/off-canvas.js"></script>
  <script src="js/hoverable-collapse.js"></script>
  <script src="js/template.js"></script>
  <script src="js/settings.js"></script>
  <script src="js/file-upload.js"></script>
  <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
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

    $(document).ready(function() {
      let deleteId = null;
      let deleteModalEl = document.getElementById('deleteModal');

      // Open modal and set ID
      $(document).on("click", ".open-delete-modal", function() {
        deleteId = $(this).data("customer_id"); // ✅ matches attribute

        $("#delete_id").val(deleteId);
        var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
modal.show();

      });

      // Confirm deletion
      $("#confirmDeleteBtn").on("click", function() {
        console.log("Deleting customer ID: " + deleteId); // Debugging
        if (deleteId) {
          $.ajax({
            url: "delete_customer", // ✅ full filename
            type: "POST",
            data: {customer_id: deleteId}, // ✅ matches PHP
            dataType: "json", 
            success: function(response) {
              if (response.status === "success") {
                $("#deleteModal").modal("hide");
                location.reload();
              } else {
                alert(response.message || "Failed to delete customer.");
              }
            },
            error: function(xhr) {
              console.log(xhr.responseText); // shows exact PHP error
              alert("An error occurred.");
            }
          });
        }
      });
    });
function redirectToList(itemId) {
  console.log("Redirecting to: viewinvoice?id=" + itemId);
  window.location.href = `viewinvoice?id=${itemId}`;
}
  </script>
</body>

</html>