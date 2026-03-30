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
                          <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#add_customer_Modal">+ New Invoice</button>
                        </div>
                      </div>
                      <div class="table-responsive pt-3">
                        <table class="table table-hover bg-white shadow-sm">
                          <thead class="table-light">
                            <tr>
                              <th>Customer</th>
                              <th>Contact</th>
                              <th>Address</th>
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
                                      <div class="text-muted small">CS <?= $row['customer_id'] ?></div>
                                    </div>
                                  </td>
                                  <td>
                                    <div class="fw-bold" style="font-weight: 900;"> <?= $row['email'] ?></div>
                                    <div class="fw-bold"> <?= $row['phone'] ?></div>
                                  </td>
                                  <td><?= htmlspecialchars($row['address']) ?></td>
                                  <td><span style="color:white;" class="badge bg-<?= $row['status'] == 'Active' ? 'success' : 'danger' ?>">
                                      <?= $row['status'] ?>
                                    </span></td>
                                  <td>
                                    <!-- EDIT BUTTON -->
                                    <button class="btn btn-inverse-warning btn-icon mr-2 edit-btn"
                                      data-bs-toggle="modal" data-bs-target="#editModal<?= $row['customer_id'] ?>">
                                      <i class="typcn typcn-edit"></i>
                                    </button>
                                    <!-- VIEW BUTTON -->
                                    <button type="button" class="btn btn-inverse-info btn-icon mr-2 view-btn" onclick="redirectToList(<?= $row['customer_id'] ?>)">
                                      <i class="typcn typcn-eye-outline"></i>
                                    </button>
                                    <!-- DELETE BUTTON -->
                                    <button class="btn btn-inverse-danger btn-icon open-delete-modal"
                                      data-customer_id="<?= $row['customer_id'] ?>">
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