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
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inventory</title>
  <link rel="stylesheet" href="vendors/typicons.font/font/typicons.css">
  <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="css/vertical-layout-light/style.css">
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
          <p class="settings-heading">SIDEBAR SKINS</p>
          <div class="sidebar-bg-options" id="sidebar-light-theme">
            <div class="img-ss rounded-circle bg-light border mr-3"></div>
            Light
          </div>
          <div class="sidebar-bg-options selected" id="sidebar-dark-theme">
            <div class="img-ss rounded-circle bg-dark border mr-3"></div>
            Dark
          </div>
          <p class="settings-heading mt-2">HEADER SKINS</p>
          <div class="color-tiles mx-0 px-4">
            <div class="tiles success"></div>
            <div class="tiles warning"></div>
            <div class="tiles danger"></div>
            <div class="tiles primary"></div>
            <div class="tiles info"></div>
            <div class="tiles dark"></div>
            <div class="tiles default border"></div>
          </div>
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
                      <h4 class="card-title">Products</h4>
                      <div class="d-flex justify-content-end align-items-center mb-3">
                        <div class="input-group">
                          <input type="text" class="form-control" placeholder="Search for...">
                      <button class="btn btn-light" type="button"><i class="typcn typcn-zoom"></i></button>
                    </div>
                      <div class="input-group col-md-5">
                        <button type="button" class="btn btn-info " data-bs-toggle="modal" data-bs-target="#add_item_Modal">
                          <i class="typcn typcn-plus-outline"></i> Add Product
                        </button>
                        <button type="button" class="btn btn-success ms-2" data-bs-toggle="modal" data-bs-target="#itemModal">
                          <i class="typcn typcn-upload-outline"></i> Qty Adjustment
                        </button>
                      </div>
                      </div>

                      <!-- ADD ITEM Modal -->
                      <div class="modal fade" id="add_item_Modal" tabindex="-1" aria-labelledby="add_item_ModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                          <div class="modal-content">
                            <form id="itemForm">
                              <div class="modal-header">
                                <h5 class="modal-title" id="add_item_ModalLabel">Add New Product</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                      <div class="form-group">
                      <label for="exampleSelectGender">Status</label>
                        <select class="form-control" id="exampleSelectGender">
                          <option>Active</option>
                          <option>Inactive</option>
                        </select>
                      </div>
                                      
                                    </div>
                                    <div class="col-md-6">
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
                                    <div class="col-md-12">
                                      <label class="form-label">Description</label>
                                      <textarea name="description" class="form-control" rows="3"></textarea>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="modal-footer">
                                <button type="submit" class="btn btn-info">Save Product</button>
                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                      <!-- END OF ADD ITEM MODAL -->

                      <div class="table-responsive pt-3">
                        <table class="table table-hover" id="inventory">
                          <thead>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
          <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
          <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
          <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Unit</th>
          <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
          <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Reorder</th>
          <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
          <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
        </tr>
        </thead>
                          </thead>
                          <tbody id="inventoryBody">
                             <!-- Sample Row -->
        <tr class="hover:bg-gray-50">
          <td class="px-4 py-2">SKU001</td>
          <td class="px-4 py-2">iPhone 14</td>
          <td class="px-4 py-2">Electronics</td>
          <td class="px-4 py-2">pcs</td>
          <td class="px-4 py-2 text-red-600 font-bold">5</td>
          <td class="px-4 py-2">10</td>
          <td class="px-4 py-2">₱50,000</td>
          <td class="px-4 py-2 space-x-2">
            <button type="button" class="btn btn-inverse-warning btn-icon mr-2 edit-btn" data-id="${data}">
                            <i class="typcn typcn-edit"></i>
                        </button>
            <button type="button" class="btn btn-inverse-danger btn-icon delete-btn" data-id="${data}">
                            <i class="typcn typcn-delete-outline"></i>
                        </button>
          </td>
        </tr>

        <!-- Another Sample Row -->
        <tr>
          <td class="px-4 py-2">SKU002</td>
          <td class="px-4 py-2">USB Cable</td>
          <td class="px-4 py-2">Accessories</td>
          <td class="px-4 py-2">pcs</td>
          <td class="px-4 py-2">50</td>
          <td class="px-4 py-2">20</td>
          <td class="px-4 py-2">₱100</td>
          <td class="px-4 py-2 space-x-2">
           <button type="button" class="btn btn-inverse-warning btn-icon mr-2 edit-btn" data-id="${data}">
                            <i class="typcn typcn-edit"></i>
            </button>
            <button type="button" class="btn btn-inverse-danger btn-icon delete-btn" data-id="${data}">
                            <i class="typcn typcn-delete-outline"></i>
                        </button>
          </td>
        </tr>
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
      <!-- End of Main Panel -->
    </div>
  </div>
<!-- DataTables Activation Script -->
<script>
  $(document).ready(function () {
    $('#productTable').DataTable({
      paging: true,
      searching: true,
      ordering: true,
      responsive: true
    });
  });
</script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/bootstrap.bundle.min.js"></script>
  <script src="vendors/js/vendor.bundle.base.js"></script>
  <script src="js/off-canvas.js"></script>
  <script src="js/hoverable-collapse.js"></script>
  <script src="js/template.js"></script>
  <script src="js/settings.js"></script>
  <script src="js/todolist.js"></script>
  <script src="vendors/progressbar.js/progressbar.min.js"></script>
  <script src="vendors/chart.js/Chart.min.js"></script>
  <script src="js/dashboard.js"></script>
  <script src="js/file-upload.js"></script>
</body>
</html>
