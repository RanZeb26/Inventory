<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    header("Location: login");
    exit;
}
include 'config/db.php';
include 'get_product.php';
include 'Get/fetch_category_item.php';
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
                      <h4 class="card-title">Products</h4>
                      <div id="editResponseMessage"></div>
                      <div class="d-flex justify-content-end align-items-center mb-3">
                        <div class="input-group">
                          <form class="d-flex" method="GET">
                            <input name="search" value="<?= $search ?>" class="form-control me-2" type="search" placeholder="Search...">
                            <button class="btn btn-light"><i class="typcn typcn-zoom"></i></button>
                          </form>
                          <!--<input type="text" class="form-control" placeholder="Search for...">
                          <button class="btn btn-light" type="button"><i class="typcn typcn-zoom"></i></button>-->
                        </div>
                          <div class="control-form col-md-3">
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#add_item_Modal">
                          <i class="typcn typcn-plus-outline"></i> Add Product
                        </button>
                        <!--
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#qtyModal">
                          <i class="typcn typcn-upload-outline"></i> Qty Adjustment
                        </button>-->
                      </div>
                      </div>

<!-- QTY adjustment Modal -->
<div class="modal fade" id="qtyModal" tabindex="-1" role="dialog" aria-labelledby="itemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="itemModalLabel">Item Selection</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">

                <!-- Search Bar -->
                <div class="mb-3">
                    <input type="text" id="search" class="form-control" placeholder="Search items...">
                </div>

                <!-- Available Items -->
                <div class="card mb-4">
                    <div class="card-header bg-light"><strong>Available Items</strong></div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover" id="availableItemsTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Item Code</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Quantity</th>
                                        <th>Item Cost</th>
                                        <th>Total Cost</th>
                                        <th>Category</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="itemList">
                                    <?php foreach ($items as $item): ?>
                                        <tr>
                                            <td><?= $item['sku'] ?></td>
                                            <td><?= $item['name'] ?></td>
                                            <td><?= $item['description'] ?></td>
                                            <td><?= $item['quantity'] ?></td>
                                            <td><?= number_format($item['cost_price'], 2) ?> Php</td>
                                            <td><?= number_format($item['total_cost'], 2) ?> Php</td>
                                            <td><?= $item['category'] ?></td>
                                            <td>
                                                <button class="btn btn-primary addItem"
                                                    data-id="<?= $item['id'] ?>"
                                                    data-sku="<?= $item['sku'] ?>"
                                                    data-name="<?= $item['name'] ?>"
                                                    data-description="<?= $item['description'] ?>"
                                                    data-cost="<?= $item['cost_price'] ?>"
                                                    data-category="<?= $item['category'] ?>">
                                                    Select
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Selected Items -->
                <div class="card">
                    <div class="card-header bg-light"><strong>Selected Items</strong></div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="selectedItemsTable" class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Item Code</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Quantity</th>
                                        <th>Item Cost</th>
                                        <th>Total Cost</th>
                                        <th>Category</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Dynamically filled via JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Close
                </button>
                <button type="button" class="btn btn-success" id="saveToDatabase">
                    <i class="fas fa-save"></i> Save
                </button>
            </div>

        </div>
    </div>
</div>

<!-- ADD ITEM Modal -->
<div class="modal fade" id="add_item_Modal" tabindex="-1" aria-labelledby="add_item_ModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <form id="itemForm" action="add_item" method="POST" enctype="multipart/form-data">
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
                                      <select name="category" class="form-control">
                                        <option value="" disabled selected>Select Category</option>
                                         <?php foreach ($category as $categories): ?>
                                          <option value="<?= $categories['id'] ?>"><?= htmlspecialchars($categories['cat_name']) ?></option>
                                          <?php endforeach; ?>
                                      </select>
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
                                      <div class="form-group">
                                        <label for="exampleSelectGender">Status</label>
                                          <select class="form-control" id="exampleSelectGender" name="status">
                                            <option>Active</option>
                                            <option>Inactive</option>
                                          </select>
                                      </div>
                                    </div>
                                    <div class="col-md-6">
                                      <label class="form-label">Cost Price</label>
                                      <input type="number" name="cost_price" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                      <label class="form-label">Selling Price</label>
                                      <input type="number" name="selling_price" class="form-control">
                                    </div>
                                    <div class="col-md-12">
                                      <label class="form-label">Reorder Level</label>
                                      <input type="number" name="stock_level" class="form-control">
                                    </div>
                                    <div class="col-md-12">
                                      <div class="form-group">
                                        <label>File upload</label>
                                        <input type="file" name="image" class="file-upload-default">
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

                      <!-- Add Category Modal -->
                      <div class="modal fade" id="add_category_Modal" tabindex="-1" aria-labelledby="add_category_ModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                          <div class="modal-content">
                            <form id="itemForm">
                              <div class="modal-header">
                                <h5 class="modal-title" id="add_item_ModalLabel">Add Category</h5>
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
  <table id="productTable" class="table table-hover bg-white shadow-sm">
    <thead class="table-light">
      <tr>
        <th>Product</th>
        <th>Price</th>
        <th>Quantity</th>
        <th>Sales</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row = $result->fetch_assoc()): ?>
      <tr>
        <td class="d-flex align-items-center">
          <img src="<?= $row['image'] ?>" alt="img" class="me-3" width="40" height="40" style="object-fit:cover; border-radius:5px;">
          <div>
            <div class="fw-bold"> <?= $row['name'] ?></div>
            <div class="text-muted small">SKU <?= $row['sku'] ?></div>
          </div>
        </td>
        <td><?= number_format($row['cost_price'], 2) ?> Php</td>
        <td><?= $row['quantity'] ?></td>
        <?php
          $sold = $row['sold'];
          $stock = $row['quantity'];
          $sale = ($stock + $sold) > 0 ? ($sold / $stock ) * 100 : 0;
          ?>
        <td>
        <?php
          $badge = 'text-danger';
          $icon = '▼';
          if ($sale >= 80) {
          $badge = 'text-success';
          $icon = '▲';
          } elseif ($sale >= 30) {
          $badge = 'text-warning';
          $icon = '▼';
          }
        ?>
        <span class="<?= $badge ?>">
        <?= number_format($sale, 2) ?>% <?= $icon ?>
        </span>
        </td>
        <td>
          <span style="color:white;" class="badge bg-<?= $row['status'] == 'Active' ? 'success' : 'danger' ?>">
            <?= $row['status'] ?>
          </span>
        </td>
        <td>
          <button class="btn btn-inverse-warning btn-icon mr-2 edit-btn" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['product_id'] ?>"><i class="typcn typcn-edit"></i></button>
          <button class="btn btn-inverse-danger btn-icon open-delete-modal" data-id="<?= $row['product_id'] ?>"><i class="typcn typcn-delete-outline"></i></button>
        </td>
      </tr>
<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-light text-black">
        <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this product?
        <input type="hidden" id="delete_id">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" id="confirmDeleteBtn" class="btn btn-danger">Delete</button>
      </div>
    </div>
  </div>
</div>



<!-- Edit ITEM Modal -->
                      <div class="modal fade" id="editModal<?= $row['product_id'] ?>" tabindex="-1" aria-labelledby="editItemModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-md">
                          <div class="modal-content">
                            <form id="editItemForm" action="update_sales" method="POST" enctype="multipart/form-data">
                              <input type="hidden" id="edit_item_id" name="id" value="<?= $row['product_id'] ?>">
                              <div class="modal-header">
                                <h5 class="modal-title" id="editItemModalLabel">Edit Product</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body">
                                <div class="container-fluid">
                                  <div class="row g-3">
                                    <div class="col-md-6">
                                      <label class="form-label">Product SKU / Code</label>
                                      <input type="text" name="sku" value="<?= $row['sku'] ?>" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                      <label class="form-label">Barcode</label>
                                      <input type="text" name="barcode" value="<?= $row['barcode'] ?>" class="form-control">
                                    </div>
                                    <div class="col-md-12">
                                      <label class="form-label">Product Name</label>
                                      <input type="text" name="name" value="<?= $row['name'] ?>" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                      <label class="form-label">Category</label>
                                      <input type="text" name="category" value="<?= $row['category'] ?>" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                      <label class="form-label">Brand</label>
                                      <input type="text" name="brand" value="<?= $row['brand'] ?>" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                      <label class="form-label">Unit</label>
                                      <input type="text" name="unit" value="<?= $row['unit'] ?>" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                      <div class="form-group">
                                        <label for="exampleSelectGender">Status</label>
                                        <select class="form-control" id="exampleSelectGender" name="status">
                                          <option value="Active" <?= $row['status'] === 'Active' ? 'selected' : '' ?>>Active</option>
                                          <option value="Inactive" <?= $row['status'] === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                                        </select>
                                      </div>
                                    </div>
                                    <div class="col-md-6">
                                      <label class="form-label">Cost Price</label>
                                      <input type="number" name="price" value="<?= $row['cost_price'] ?>" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                      <label class="form-label">Selling Price</label>
                                      <input type="number" name="selling_price" value="<?= $row['selling_price'] ?>" class="form-control">
                                    </div>
                                      <div class="col-md-12">
                                      <label class="form-label">Reorder Level</label>
                                      <input type="number" name="stock_level" value="<?=$row['reorder_level']?>" class="form-control">
                                    </div>
                                    <div class="col-md-12">
                                      <div class="form-group">
                        <label>File upload</label>
                        <input type="file" name="image" class="file-upload-default">
                        <div class="input-group col-xs-12">
                        <input type="text" class="form-control file-upload-info" disabled placeholder="Upload Image">
                        <span class="input-group-append">
                          <button class="file-upload-browse btn btn-light" type="button">Upload</button>
                          
                        </span>
                        </div>
                        <small class="text-muted">Current: <?= $row['image'] ?></small>
                        </div>
                                    </div>
                                    <div class="col-md-12">
                                      <label class="form-label">Description</label>
                                      <textarea name="description" value="<?= $row['description'] ?>" class="form-control" rows="3"></textarea>
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
                      <!-- END OF ADD ITEM MODAL -->

<!-- Modal -->
      <div class="modal fade" id="leditModal<?= $row['product_id'] ?>" tabindex="-1">
        <div class="modal-dialog modal-md" >
          <form class="modal-content" action="update_sales" method="POST" enctype="multipart/form-data">
            <div class="modal-header">
              <h5 class="modal-title">Edit Product</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <input type="hidden" name="id" value="<?= $row['id'] ?>">

              <div class="mb-2">
                <label>Name</label>
                <input name="name" value="<?= $row['name'] ?>" class="form-control">
              </div>
              <div class="mb-2">
                <label>SKU</label>
                <input name="sku" value="<?= $row['sku'] ?>" class="form-control">
              </div>
              <div class="mb-2">
                <label>Price</label>
                <input name="price" value="<?= $row['price'] ?>" class="form-control" type="number" step="0.01">
              </div>
              <div class="mb-2">
                <label>Quantity</label>
                <input name="quantity" value="<?= $row['quantity'] ?>" class="form-control" type="number">
              </div>
              <div class="mb-2">
                <label>Status</label>
                <select name="status" class="form-select">
                  <option value="Active" <?= $row['status'] === 'Active' ? 'selected' : '' ?>>Active</option>
                  <option value="Inactive" <?= $row['status'] === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
              </div>
              <div class="mb-2">
                <label>Upload Image</label><br>
                <input type="file" name="image" class="form-control">
                <small class="text-muted">Current: <?= $row['image'] ?></small>
              </div>
            </div>
            <div class="modal-footer">
              <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">Save</button>
            </div>
          </form>
        </div>
      </div>

      <!-- EDIT ITEM MODAL (Ensure it's inside <body> at the same level as other modals) -->
<div class="modal fade" id="leditModal<?= $row['product_id'] ?>" tabindex="-1" aria-labelledby="editItemModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editItemModalLabel">Edit Item</h5>
       </div>
      <div class="modal-body">
        <form id="editItemForm" action="update_sales" method="POST" enctype="multipart/form-data">
          <input type="hidden" id="edit_item_id" name="id" value="<?= $row['product_id'] ?>">

          <div class="form-group">
    <label for="edit_item_name">Job Site Name</label>
    <div class="mb-2">
                <label>Name</label>
                <input name="name" value="<?= $row['name'] ?>" class="form-control">
              </div>
              <div class="mb-2">
                <label>SKU</label>
                <input name="sku" value="<?= $row['sku'] ?>" class="form-control">
              </div>
              <div class="mb-2">
                <label>Price</label>
                <input name="price" value="<?= $row['cost_price'] ?>" class="form-control" type="number" step="0.01">
              </div>
              <div class="mb-2">
                <label>Quantity</label>
                <input name="quantity" value="<?= $row['quantity'] ?>" class="form-control" type="number">
              </div>
              <div class="mb-2">
                <label>Status</label>
                <select name="status" class="form-group">
                  <option value="Active" <?= $row['status'] === 'Active' ? 'selected' : '' ?>>Active</option>
                  <option value="Inactive" <?= $row['status'] === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
              </div>
              <div class="mb-2">
                <label>Upload Image</label><br>
                <input type="file" name="image" class="form-control">
                <small class="text-muted">Current: <?= $row['image'] ?></small>
              </div>
            </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Update Item</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
      <!--End of Edit Modal-->

      <?php endwhile; ?>
    </tbody>
  </table>

<!-- Pagination -->
  <nav>
    <ul class="pagination justify-content-center">
      <?php if ($page > 1): ?>
        <li class="page-item"><a class="page-link" href="?page=<?= $page-1 ?>&search=<?= $search ?>">&laquo; Prev</a></li>
      <?php endif; ?>
      <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
          <a class="page-link" href="?page=<?= $i ?>&search=<?= $search ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>
      <?php if ($page < $total_pages): ?>
        <li class="page-item"><a class="page-link" href="?page=<?= $page+1 ?>&search=<?= $search ?>">Next &raquo;</a></li>
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

  $(document).ready(function () {
    $('#productTable').DataTable({
      paging: true,
      searching: true,
      ordering: true,
      responsive: true
    });
  });
function updateSlider(slider) {
  const tooltip = document.getElementById('rangeTooltip');
  tooltip.textContent = slider.value;

  // Position tooltip
  const percent = (slider.value - slider.min) / (slider.max - slider.min);
  tooltip.style.left = `calc(${percent * 100}% - 20px)`;

  // Update background
  slider.style.background = `linear-gradient(to right, #f44336 0%, #f44336 ${percent * 100}%, #e0e0e0 ${percent * 100}%, #e0e0e0 100%)`;
}
function updateSlider2(slider) {
  const tooltip = document.getElementById('rangeTooltip2');
  tooltip.textContent = slider.value;

  // Position tooltip
  const percent = (slider.value - slider.min) / (slider.max - slider.min);
  tooltip.style.left = `calc(${percent * 100}% - 20px)`;

  // Update background
  slider.style.background = `linear-gradient(to right, #f44336 0%, #f44336 ${percent * 100}%, #e0e0e0 ${percent * 100}%, #e0e0e0 100%)`;
}

// Delete functions
$(document).ready(function() {
    let deleteId = null;

    // Open modal and set ID
    $(document).on("click", ".open-delete-modal", function() {
        deleteId = $(this).data("id");
        $("#delete_id").val(deleteId);
        $("#deleteModal").modal("show");
    });

// Confirm delete
  $("#confirmDeleteBtn").click(function() {
        const id = $("#delete_id").val();

        $.ajax({
            url: "delete_item",
            type: "POST",
            data: { id: id },
            dataType: "json",
            success: function(response) {
                if (response.status === "success") {
                    $("#deleteModal").modal("hide");
                    // Reload DataTable or whole page
                    $("#inventory").DataTable().ajax.reload(null, false);
                } else {
                    alert("Error: " + response.message);
                }
            },
            error: function() {
                alert("Error deleting product.");
            }
        });
    });
});

let selectedItems = [];

// Format currency
function formatCurrency(value) {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP'
    }).format(value);
}

// Render selected items
function renderSelectedItems() {
    let tbody = $("#selectedItemsTable tbody");
    tbody.empty();

    selectedItems.forEach((item) => {
        let row = `
            <tr>
                <td>${item.sku}</td>
                <td>${item.name}</td>
                <td>${item.description}</td>
                <td>
                    <input type="number" class="form-control itemQuantity" 
                        data-id="${item.id}" value="${item.quantity}" min="1">
                </td>
                <td>${formatCurrency(item.items_cost)}</td>
                <td>${formatCurrency(item.total_cost)}</td>
                <td>${item.category}</td>
                <td>
                    <button class="btn btn-danger btn-sm removeItem" data-id="${item.id}">Remove</button>
                </td>
            </tr>`;
        tbody.append(row);
    });
}

$(document).ready(function () {

    // Add item
    $(document).on("click", ".addItem", function () {
        let itemId = $(this).data("id");
        let itemSKU = $(this).data("sku");
        let itemName = $(this).data("name");
        let itemDescription = $(this).data("description");
        let itemCost = parseFloat($(this).data("cost"));
        let itemCategory = $(this).data("category");

        if (!selectedItems.some(item => item.id == itemId)) {
            selectedItems.push({
                id: itemId,
                sku: itemSKU,
                name: itemName,
                description: itemDescription,
                quantity: 1,
                items_cost: itemCost,
                total_cost: itemCost,
                category: itemCategory
            });
            renderSelectedItems();
        }
    });

    // Remove item
    $(document).on("click", ".removeItem", function () {
        let id = $(this).data("id");
        selectedItems = selectedItems.filter(item => item.id != id);
        renderSelectedItems();
    });

    // Update quantity
    $(document).on("input", ".itemQuantity", function () {
        let id = $(this).data("id");
        let qty = parseInt($(this).val()) || 1;
        selectedItems.forEach(item => {
            if (item.id == id) {
                item.quantity = qty;
                item.total_cost = qty * item.items_cost;
            }
        });
        renderSelectedItems();
    });

   $("#saveToDatabase").click(function () {
    if (selectedItems.length === 0) {
        alert("No items selected.");
        return;
    }

    $.ajax({
        url: "Add/save_selected_items.php",
        type: "POST",
        contentType: "application/json",
        data: JSON.stringify({ items: selectedItems }),
        dataType: "json",
        success: function (response) {
            if (response.status === "success") {
                alert("Items saved successfully!");
                $("#qtyModal").modal("hide");
                selectedItems = [];
                renderSelectedItems();
            } else {
              console.log(response.message);
                alert("Error saving items: " + response.message);
            }
        },
        error: function (xhr, status, error) {
            console.log(xhr.responseText);
            alert("AJAX error: " + error);
        }
    });
});


    // Search filter
    $("#search").on("keyup", function () {
        let value = $(this).val().toLowerCase();
        $("#availableItemsTable tbody tr").filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

});


</script>
 
  
</body>
</html>
