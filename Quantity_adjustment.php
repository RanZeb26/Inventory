<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
  header("Location: login");
  exit;
}
include 'config/db.php';
include 'Get/fetch_qty_adjustment.php';
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
                      <h4 class="card-title">Quantity Adjustment</h4>
                      <!--<a href="Adjustment" class="btn btn-primary mb-3"><< Back to Adjustment</a>-->
                      <div class="d-flex justify-content-end align-items-center mb-3">
                        <div class="input-group">
                          <form method="GET" class="mb-3 d-flex">
                            <input type="text" name="search" class="form-control me-2"
                              placeholder="Search by Name" value="<?= htmlspecialchars($search) ?>">
                            <button class="btn btn-light"><i class="typcn typcn-zoom"></i></button>
                          </form>
                        </div>
                        <div class="control-form col-md-3">
                          <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#add_item_Modal">
                            New Qty Adjustment
                          </button>

                        </div>
                      </div>

                      <!-- ADD ITEM Modal -->
                      <div class="modal fade" id="add_item_Modal" tabindex="-1" aria-labelledby="add_item_ModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-md">
                          <div class="modal-content">
                            <form id="itemForm" action="save_adj_desc" method="POST">

                              <div class="modal-header">
                                <h5 class="modal-title">Add New Quantity Adjustment</h5>
                                <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal">&times;</button>
                              </div>

                              <div class="modal-body">
                                <div class="container-fluid">
                                  <div class="row g-3">

                                    <!-- PRODUCT SELECT -->
                                    <div class="col-md-12">
                                      <label class="form-label">Select Product</label>
                                      <select name="product_id" id="productSelect" class="form-control" required>
                                        <option value="" disabled selected>Search / Select product...</option>
                                        <?php foreach ($category as $categories): ?>
                                          <option
                                            value="<?= $categories['product_id'] ?>"
                                            data-name="<?= htmlspecialchars($categories['name']) ?>"
                                            data-qty="<?= $categories['quantity'] ?>">
                                            <?= htmlspecialchars($categories['name']) ?>
                                          </option>
                                        <?php endforeach; ?>
                                      </select>
                                    </div>

                                    <!-- HIDDEN NAME -->
                                    <input type="hidden" name="name" id="itemName">
                                    <!-- PREVIOUS QUANTITY -->
                                    <div class="col-md-12">
                                      <label class="form-label">Current Stock</label>
                                      <input name="previous_qty" type="text" id="productQty" class="form-control" readonly>
                                    </div>

                                    <!-- ADJUSTMENT -->
                                    <div class="col-md-12">
                                      <label class="form-label">Quantity Adjustment</label>
                                      <input type="text" name="adjustment_qty" class="form-control" required>
                                    </div>
                                    <!-- REASON -->
                                    <div class="col-md-12">
                                      <label class="form-label">Reason</label>
                                      <textarea name="reason" class="form-control" rows="3" placeholder="e.g. Damaged item, Incoming shipment, Inventory correction" required></textarea>
                                    </div>
                                    <!-- Upload photo -->
                                    <div class="col-md-12">
                                      <div class="form-group">
                                        <label>File upload</label>
                                        <input type="file" name="img[]" class="file-upload-default">
                                        <div class="input-group col-xs-12">
                                          <input type="text" class="form-control file-upload-info" disabled placeholder="Upload Image">
                                          <span class="input-group-append">
                                            <button class="file-upload-browse btn btn-light" type="button">Upload</button>
                                          </span>
                                        </div>
                                      </div>
                                    </div>



                                  </div>
                                </div>
                              </div>

                              <div class="modal-footer">
                                <button type="submit" class="btn btn-info">Save Adjustment</button>
                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                              </div>

                            </form>
                          </div>
                        </div>
                      </div>
                      <!-- END MODAL -->



                      <!-- Delete Confirmation Modal -->
                      <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                          <div class="modal-content">
                            <div class="modal-header bg-light text-black">
                              <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                              <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal" aria-label="Close">&times;</button>
                            </div>
                            <div class="modal-body">
                              Are you sure you want to delete this product?
                              <input type="hidden" id="delete_id">
                            </div>
                            <div class="modal-footer">
                              <button type="button" id="confirmDeleteBtn" class="btn btn-info">Delete</button>
                              <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>

                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="table-responsive pt-3">
                        <table class="table table-hover bg-white shadow-sm">
                          <thead class="table-light">
                            <tr>
                              <th>ID</th>
                              <th>Name</th>
                              <th>Reason</th>
                              <th>Status</th>
                              <th>Current Quantity</th>
                              <th>Adjustment Quantity</th>
                              <th>Action</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php if (!empty($result)): ?>
                              <?php foreach ($result as $row): ?>
                                <tr>
                                  <td>
                                    <div>
                                      <div><?= htmlspecialchars($row['adj_id']) ?></div>
                                    </div>
                                  </td>
                                  <td>
                                    <div class="fw-bold"><?= htmlspecialchars($row['name']) ?></div>
                                  </td>
                                  <td><?= htmlspecialchars($row['reason']) ?></td>
                                  <td>
                                    <span class="badge bg-<?= $row['status'] == 'Active' ? 'success' : 'danger' ?>">
                                      <?= htmlspecialchars($row['status']) ?>
                                    </span>
                                  </td>
                                  <td><?= htmlspecialchars($row['quantity']) ?></td>
                                  <td><?= htmlspecialchars($row['adjustment_qty']) ?></td>
                                  <td>
                                    <!-- EDIT BUTTON -->
                                    <button class="btn btn-inverse-warning btn-icon mr-2 edit-btn"
                                      data-bs-toggle="modal" data-bs-target="#editModal<?= $row['adj_id'] ?>">
                                      <i class="typcn typcn-edit"></i>
                                    </button>
                                    <!-- VIEW BUTTON 
                                    <button type="button" class="btn btn-inverse-info btn-icon mr-2 view-btn"
                                      data-bs-toggle="modal" data-bs-target="#viewModal<?= $row['adj_id'] ?>" onclick="redirectToList(<?= $row['adj_id'] ?>)">
                                      <i class="typcn typcn-eye-outline"></i>
                                    </button>-->
                                    <button class="btn btn-inverse-danger btn-icon open-delete-modal"
                                      data-adj_id="<?= $row['adj_id'] ?>">
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
                        <!-- Edit ITEM Modal -->
                        <?php foreach ($result as $row): ?>
                          <div class="modal fade" id="editModal<?= $row['adj_id'] ?>" tabindex="-1" aria-labelledby="editItemModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-md">
                              <div class="modal-content">
                                <form id="editItemForm" action="update_adj_desc" method="POST" enctype="multipart/form-data">
                                  <input type="hidden" id="edit_item_id" name="adj_id" value="<?= $row['adj_id'] ?>">
                                  <input type="hidden" name="productID" value="<?= $row['product_id'] ?>">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="editItemModalLabel">Edit Product</h5>
                                    <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal">&times;</button>
                                  </div>
                                  <div class="modal-body">
                                    <div class="container-fluid">
                                      <div class="row g-3">
                                        <div class="col-md-12">
                                          <label class="form-label">Name</label>
                                          <input type="text" value="<?= $row['name'] ?>" name="name" class="form-control" required>
                                        </div>
                                        <div class="col-md-12">
                                          <label class="form-label">Reason</label>
                                          <textarea name="reason" class="form-control" rows="3"><?= $row['reason'] ?></textarea>
                                        </div>
                                        <!-- PREVIOUS QUANTITY -->
                                        <div class="col-md-12">
                                          <label class="form-label">Previous Quantity</label>
                                          <input value="<?= $row['previous_qty'] ?>" name="previous_qty" type="text" id="productQty" class="form-control" readonly>
                                        </div>

                                        <!-- ADJUSTMENT -->
                                        <div class="col-md-12">
                                          <label class="form-label">Quantity Adjustment</label>
                                          <input value="<?= $row['adjustment_qty'] ?>" type="text" name="adjustment_qty" class="form-control" required>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="modal-footer">
                                    <button type="submit" class="btn btn-info">Update Product</button>
                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                                  </div>
                                </form>
                              </div>
                            </div>
                          </div>
                        <?php endforeach; ?>
                        <!-- END OF EDIT ITEM MODAL -->
                        <!-- Pagination -->
                        <nav>
                          <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                              <li class="page-item"><a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= $search ?>">&laquo; Prev</a></li>
                            <?php endif; ?>
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                              <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>&search=<?= $search ?>"><?= $i ?></a>
                              </li>
                            <?php endfor; ?>
                            <?php if ($page < $total_pages): ?>
                              <li class="page-item"><a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= $search ?>">Next &raquo;</a></li>
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

  <script>
    document.getElementById('productSelect').addEventListener('change', function() {
      let qty = this.options[this.selectedIndex].getAttribute('data-qty');
      let name = this.options[this.selectedIndex].getAttribute('data-name');

      document.getElementById('productQty').value = qty;
      document.getElementById('itemName').value = name;
    });

    $(document).ready(function() {
      let deleteId = null;
      let deleteModalEl = document.getElementById('deleteModal');

      // Open modal and set ID
      $(document).on("click", ".open-delete-modal", function() {
        deleteId = $(this).data("adj_id"); // ✅ matches attribute
        $("#delete_id").val(deleteId);
        $("#deleteModal").modal("show");
      });

      // Confirm deletion
      $("#confirmDeleteBtn").on("click", function() {
        if (deleteId) {
          $.ajax({
            url: "delete_adj_desc", // ✅ full filename
            type: "POST",
            data: {
              adj_id: deleteId
            }, // ✅ matches PHP
            success: function(response) {
              if (response.status === "success") {
                $("#deleteModal").modal("hide");
                location.reload();
              } else {
                alert(response.message || "Failed to delete product.");
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


    function redirectToList(referenceId) {
      console.log("Redirecting to: list?id=" + referenceId); // Debugging
      window.location.href = `list?id=${referenceId}`;
    }
  </script>
  
</body>

</html>