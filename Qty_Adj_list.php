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
  <!-- Required for 🛒 Purchases

        *Record purchase orders from suppliers

        *Track order status (Pending, Received, Cancelled)

        *Supplier management -->
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Inventory</title>
  <link rel="stylesheet" href="vendors/typicons.font/font/typicons.css">
  <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="css/vertical-layout-light/style.css">
  <link rel="shortcut icon" href="images/favicon.png" />
</head>
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
                      <h4 class="card-title">Quantity Adjustment List</h4>
                      <a href="QuantityAdjustment" class="btn btn-primary mb-3"><< Back to Quantity Adjustment</a>
                      <div class="row g-3">
                        <div class="input-group col-md-4">
                          <input type="text" class="form-control" placeholder="Search for...">
                      <button class="btn btn-light" type="button"><i class="typcn typcn-zoom"></i></button>
                    </div>
                    <div class="input-group col-md-4">
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#add_item_Modal">
                          <i class="typcn typcn-plus-outline"></i> Add Product
                        </button>
                    </div>

                    <div class="input-group col-md-4">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add_item_Modal">
                          <i class="typcn typcn-plus-outline"></i> Submit Adjustment
                        </button>
                    </div>
                        <!--
                        <button type="button" class="btn btn-success rounded-pill ms-2" data-bs-toggle="modal" data-bs-target="#itemModal">
                          <i class="typcn typcn-upload-outline"></i> Quantity Adjustment
                        </button>
                        -->
                      </div>
                      <!-- ADD ITEM Modal -->
                      <div class="modal fade" id="add_item_Modal" tabindex="-1" aria-labelledby="add_item_ModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-md">
                          <div class="modal-content">
                            <form id="itemForm">
                              <div class="modal-header">
                                <h5 class="modal-title" id="add_item_ModalLabel">Add New Product</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body">
                                <div class="container-fluid">
                                  <div class="row g-3">
                                    <div class="col-md-12">
                                    <select class="form-control" name="product_id" id="product_id" required>
                                      <option value="">-- Select Product --</option>
                                      <?php
                                      $stmt = $pdo->query("SELECT product_id, name FROM products ORDER BY name ASC");
                                      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo "<option value='{$row['product_id']}'>{$row['name']}</option>";
                                      }
                                      ?>
                                      </select>
                                    </div>
                                    <div class="col-md-12">
                                     <label for="quantity" class="form-label">Adjustment Quantity</label>
                                     <input type="number" class="form-control" name="quantity" required>
                                    </div>
                                     <div class="col-md-12">
                                      <label class="form-label">Reason</label>
                                      <textarea name="reason" class="form-control" rows="3"></textarea>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Add Product</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                      <!-- END OF ADD ITEM MODAL -->
                       <div class="table-responsive pt-3">
                        <table class="table table-hover bg-white shadow-sm">
                          <thead class="table-light">
                            <tr>
                              <th>Product</th>
                              <th>Reason</th>
                              <th>Quantity</th>
                              <th>Action</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php if (!empty($result)): ?>
                              <?php foreach ($result as $row): ?>
                                <tr>
                                  <td>
                                    <div>
                                      <div><?= htmlspecialchars($row['reference_id']) ?></div>
                                    </div>
                                  </td>
                                  <td><div class="fw-bold"><?= htmlspecialchars($row['product_name']) ?></div></td>
                                  <td><?= htmlspecialchars($row['reasons']) ?></td>
                                  <td>
                                    <span class="badge bg-<?= $row['status'] == 'Active' ? 'success' : 'danger' ?>">
                                      <?= htmlspecialchars($row['status']) ?>
                                    </span>
                                  </td>
                                  <td><?= htmlspecialchars($row['created_at']) ?></td>
                                  <td><?= htmlspecialchars($row['updated_at'] ?? $row['created_at']) ?></td>
                                  <td>
                                    <button class="btn btn-inverse-warning btn-icon mr-2 edit-btn" 
                                    data-bs-toggle="modal" data-bs-target="#editModal<?= $row['reference_id'] ?>">
                                    <i class="typcn typcn-edit"></i>
                                  </button>
                                  <button type="button" class="btn btn-inverse-info btn-icon mr-2 view-btn" 
                                  data-bs-toggle="modal" data-bs-target="#viewModal<?= $row['reference_id'] ?>" onclick="redirectToList(<?= $row['reference_id'] ?>)">
                                  <i class="typcn typcn-eye-outline"></i>
                                </button>
                                <button class="btn btn-inverse-danger btn-icon open-delete-modal" 
                                data-reference_id="<?= $row['reference_id'] ?>">
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
  <script src="js/todolist.js"></script>
</body>
</html>