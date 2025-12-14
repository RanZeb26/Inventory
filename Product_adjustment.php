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
                      <h4 class="card-title">Adjustment</h4>
                      <div class="d-flex justify-content-end align-items-center mb-3">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Search for...">
                            <button class="btn btn-light" type="button"><i class="typcn typcn-zoom"></i></button>
                        </div>
                    </div>

                      <!-- ADD ITEM Modal -->
                      <div class="modal fade" id="add_item_Modal" tabindex="-1" aria-labelledby="add_item_ModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                          <div class="modal-content">
                            <form id="itemForm">
                              <div class="modal-header">
                                <h5 class="modal-title" id="add_item_ModalLabel">Add New Product</h5>
                                <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal" aria-label="Close">&times;</button>
                              </div>
                              <div class="modal-body">
                                <div class="container-fluid">
                                  <div class="row g-3">
                                    <div class="col-md-6">
                                      <label class="form-label">Product SKU / Code</label>
                                      <input type="text" name="sku" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                      <label class="form-label">Barcode</label>
                                      <input type="text" name="barcode" class="form-control">
                                    </div>
                                    <div class="col-md-12">
                                      <label class="form-label">Product Name</label>
                                      <input type="text" name="name" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                      <label class="form-label">Category</label>
                                      <input type="text" name="category" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                      <label class="form-label">Brand</label>
                                      <input type="text" name="brand" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                      <label class="form-label">Unit</label>
                                      <input type="text" name="unit" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                      <label class="form-label">Reorder Level</label>
                                      <input type="number" name="reorder_level" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                      <label class="form-label">Quantity in Stock</label>
                                      <input type="number" name="quantity" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                      <label class="form-label">Cost Price</label>
                                      <input type="number" name="cost_price" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                      <label class="form-label">Selling Price</label>
                                      <input type="number" name="selling_price" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                      <label class="form-label">Status</label>
                                      <select name="status" class="form-control">
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                      </select>
                                    </div>
                                    <div class="col-md-6">
                                      <label class="form-label">Product Image</label>
                                      <input type="file" name="image" class="form-control">
                                    </div>
                                    <div class="col-md-12">
                                      <label class="form-label">Description</label>
                                      <textarea name="description" class="form-control" rows="3"></textarea>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Save Product</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                      <!-- END OF ADD ITEM MODAL -->

                      <div class="row g-3">
                         <div class="input-group col-md-2">
                        <button type="button" class="btn btn-info">
                          <a style="color:aliceblue; text-decoration:none;" href="QuantityAdjustment">Quantity Adjustment</a>
                        </button>
                    </div>
                    <div class="input-group col-md-2">
                        <button type="button" class="btn btn-info">
                          <a style="color:aliceblue; text-decoration:none;" href="CostAdjustment">Cost Adjustment</a>
                        </button>
                    </div>
                    <div class="input-group col-md-2">
                        <button type="button" class="btn btn-info">
                          <a style="color:aliceblue; text-decoration:none;" href="OrderTransactions">Order Transactions</a>
                        </button>
                    </div>
                    <div class="input-group col-md-2">
                        <button type="button" class="btn btn-info">
                          <a style="color:aliceblue; text-decoration:none;" href="Warehouse">Warehouse</a>
                        </button>
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
</body>
</html>