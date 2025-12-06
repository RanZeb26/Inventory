<?php 
  include('./auth.php');//login verification.
  include "../config/db.php"; // Ensure database connection

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Salemtec Jobsite</title>

    <?php include 'css.php';?>

  <style>
      .select-box{
        width: 180px;
      }
      .search-bar{
        width: 250px;
      }

      .table th, .table td {
    text-align: center;
    vertical-align: middle;
}

.table-secondary {
    background-color: #f0f0f0;
    font-weight: bold;
}

.btn-danger {
    font-size: 14px;
}

  </style>

  </head>
  <body>
    <div class="row"  >
      <div class="col-12">
              <i class="typcn typcn-delete-outline" id="bannerClose"></i>
        </span>
      </div>
     </div>
     <div class="container-scroller">

      <!-- NAVBAR -->
      <?php include './navbar/nav.php';?>
     <!-- SIDEBAR -->
      <?php include './sidebar/sidebar.php';?>


        <!-- partial -->
        <div class="main-panel">
          <div class="content-wrapper">
          <div class="col-lg-12 grid-margin stretch-card">
   <div class="card">
    <div class="card-body">
      <h4 class="card-title">Purchase Request - List</h4>
      
      <!-- Row for Select and Search -->
      <div class="d-flex justify-content-end align-items-center mb-3">
 
   <!-- Button Modal -->
   <button type="button" class="btn btn-primary btn-rounded btn-fw" data-bs-toggle="modal" data-bs-target="#itemModal"><i class="typcn typcn-plus-outline"> </i>Add</button>
 
   </div>


   <div class="table-responsive pt-3">
    <div id="responseMessage"></div> 
    <table class="table table-bordered" id="ItemsTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Description</th>
                <th>Quantity</th>
                <th>Item Cost</th>
                <th>Total Cost</th>
                <th>Category</th>
                <th>Action</th>
            </tr>

        <tbody id="ItemsTableBody">
            <!-- Data will be loaded here dynamically -->
        </tbody>
    </table>
   </div>

   <div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
   <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addItemModalLabel">Add Item</h5>
       </div>
      <div class="modal-body">
        <form id="addItemForm">
          <input type="hidden" id="add_item_id" name="id">

          <div class="form-group">
            <label for="add_item_name">Item Name</label>
            <input type="text" class="form-control" id="add_item_name" name="name" required>
          </div>

          <div class="form-group">
            <label for="add_item_desc">Description</label>
            <textarea class="form-control" id="add_item_desc" name="description" required></textarea>
          </div>

          <div class="form-group">
            <label for="add_item_qty">Quantity</label>
            <input type="number" class="form-control" id="add_item_qty" name="quantity" required>
          </div>

          <div class="form-group">
            <label for="add_item_cost">Price (₱ per unit)</label>
            <input type="number" class="form-control" id="add_item_cost" name="cost" required>
          </div>
          <div class="form-group">
            <label for="add_item_total_cost">Price (₱ per unit)</label>
            <input type="number" class="form-control" id="add_item_total_cost" name="totalcost" readonly>
          </div>
         <div class="row">
         <div class="col-md-6">
              <div class="form-group">
                <label for="add_item_unit">Unit</label>
                <select class="form-control" id="add_item_unit" name="add_item_unit">
                <option >Others</option>
                      <option >Box</option>
                      <option >Length</option>
                      <option >Lot</option>
                      <option >Pack</option>
                      <option >Piece</option>
                      <option >Rolls</option>
                      <option >Units</option>
                </select>
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="form-group">
                <label for="add_category_item">Category</label>
                <select class="form-control" id="add_category_item" name="add_category_item">
                <option selected>Select Category</option>
                      <option >Computing Devices</option>
                      <option >Networking Equipment</option>
                      <option >Peripherals & Accessories</option>
                      <option >Storage Devices</option>
                      <option >Software & Licenses</option>
                      <option >Networking & Communication Devices</option>
                      <option >Security Equipment</option>
                      <option >Development & Testing Devices</option>
                      <option >Cloud & Virtual Resources</option>
                      <option >Office IT Equipment</option>
                </select>
              </div>
            </div>
         </div>

          <div id="editResponseMessage"></div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Add Item</button>
          </div>
        </form>
      </div>

    </div>
   </div>
   </div>
   <!-- add Item Modal -->
   <div class="modal fade" id="itemModal" tabindex="-1" role="dialog" aria-labelledby="itemModalLabel" aria-hidden="true">
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

                <!-- Available Items Section -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <strong>Available Items</strong>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Item Code</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Quantity</th>
                                        <th>Items Cost</th>
                                        <th>Total Cost</th>
                                        <th>Category</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="itemList"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Selected Items Section -->
                <div class="card">
                    <div class="card-header bg-light">
                        <strong>Selected Items</strong>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="selectedItemsTable" class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Item Code</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Quantity</th>
                                        <th>Items Cost</th>
                                        <th>Total Cost</th>
                                        <th>Category</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Selected items will be inserted here dynamically -->
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


          <!-- content-wrapper 
           <div class="modal-footer">
                <button type="button" class="btn btn-success" id="saveToDatabase">
                    <i class="fas fa-save"></i> Save
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Close
                </button>
            </div>
          ends -->
          <!-- partial:partials/_footer.html -->

          <!-- partial -->
        </div>
                      <?php include './footer/footer.php'?>
        <!-- main-panel ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>    
    <!-- container-scroller -->

    <?php include 'js.php'?>
<script>

// ✅ Get selected ID from the URL (assuming it's in the query string)
const urlParams = new URLSearchParams(window.location.search);
const selectedId = urlParams.get("id"); // Get "id" from URL

$(document).ready(function () {
    // 🔍 Step 1: Get ID from URL
    function getQueryParam(param) {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(param);
    }

    const itemId = getQueryParam('id'); // e.g., from ?id=3

    // 🧾 Step 2: Initialize DataTable
    var ItemsTable = $('#ItemsTable').DataTable({
        "ajax": {
            "url": "get_pritem", // PHP file that fetches inventory data
            "type": "POST",
            "data": function (d) {
                d.id = itemId; // 🔄 Send the item ID to PHP
            },
            "dataSrc": ""
        },
        "columns": [
            {
                "data": null,
                "render": function (data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            {
                "data": null,
                "render": function (data, type, row) {
                    return `<div class="d-flex"><div><div>${row.name}</div></div></div>`;
                }
            },
            {
                "data": null,
                "render": function (data, type, row) {
                    return `<div class="d-flex"><div><div>${row.description}</div></div></div>`;
                }
            },
            {
                "data": null,
                "render": function (data, type, row) {
                    return `<div class="d-flex"><div><div>${row.quantity}</div></div></div>`;
                }
            },
            {
                "data": null,
                "render": function (data, type, row) {
                    return `<div class="d-flex"><div><div>${row.items_cost}</div></div></div>`;
                }
            },
            {
                "data": null,
                "render": function (data, type, row) {
                    return `<div class="d-flex"><div><div>${row.total_cost}</div></div></div>`;
                }
            },
            {
                "data": null,
                "render": function (data, type, row) {
                    return `<div class="d-flex"><div><div>${row.category}</div></div></div>`;
                }
            },
            {
                "data": "id",
                "render": function (data) {
                    return `
                    <div class="d-flex">
                        
                        <button type="button" class="btn btn-inverse-danger btn-icon delete-btn" data-id="${data}">
                            <i class="typcn typcn-delete-outline"></i>
                        </button>
                    </div>`;
                }
            }
        ],
        "processing": true,
        "language": {
            "processing": '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>'
        }
    });
});

// 🌐 Redirect function to view item by ID
// function redirectToList(id) {
    // window.location.href = `inventory_list.html?id=${id}`;
// }



$(document).ready(function() {
    $('.edit-btn, .add-btn').on('click', function() {
        $(this).blur(); // ✅ Remove focus from button
        var modal = new bootstrap.Modal(document.getElementById("add_item_Modal"));
        modal.show();
    });
});
$('#add_item_Modal').on('hidden.bs.modal', function () {
    $(this).removeAttr('aria-hidden'); // ✅ Removes lingering aria-hidden
});
$('#add_item_Modal').on('shown.bs.modal', function () {
    $('#item_name').focus(); // ✅ Focuses on the first input field
});
$(".modal").on("hidden.bs.modal", function () {
    $(".modal-backdrop").remove(); // ✅ Ensures only one backdrop exists
});
// Initialize selected items array
let selectedItems = [];


// Fetch Items from PHP using AJAX
function fetchItems(searchTerm = '') {
    $.ajax({
        url: "select_items",
        method: "GET",
        data: { search: searchTerm },
        dataType: "json",
        success: function (items) {
            //console.log("Fetched items:", items); // Debugging log
            if (Array.isArray(items)) {
                let tableRows = "";

                items.forEach(item => {
                    tableRows += `
                        <tr>
                            <td>${item.items_id}</td>
                            <td>${item.name}</td>
                            <td>${item.description}</td>
                            <td>${item.quantity}</td>
                            <td>${item.items_cost}</td>
                            <td>${item.total_cost}</td>
                            <td>${item.category}</td>
                            <td>
                                <button class="btn btn-success btn-sm addItem"
                                    data-id="${item.items_id}"
                                    data-name="${item.name}"
                                    data-description="${item.description}"
                                    data-quantity="${item.quantity}"
                                    data-items-cost="${item.items_cost}"
                                    data-total-cost="${item.total_cost}"
                                    data-category="${item.category}">
                                    Add
                                </button>

                            </td>
                        </tr>
                    `;
                });

                $("#itemList").html(tableRows);
            } else {
                console.error("Invalid items format:", items);
            }
        },
        error: function (xhr, status, error) {
            console.error("Error fetching items:", xhr.responseText);
        }
    });
}

// Format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat("en-PH", { style: "currency", currency: "PHP" }).format(amount);
}

// Search functionality for filtering items in the table
$("#searchInput").on("keyup", function () {
    let searchTerm = $(this).val().toLowerCase();
    $("#selectedItemsTable tbody tr").filter(function () {
        $(this).toggle($(this).text().toLowerCase().indexOf(searchTerm) > -1);
    });
});

// Add Item to Selection
$(document).on("click", ".addItem", function () {
    let itemId = $(this).data("id");
    let itemName = $(this).data("name");
    let itemDescription = $(this).data("description");
    let itemCost = $(this).data("items-cost").toString().replace(/[^\d.]/g, "");
    let itemCategory = $(this).data("category");

    let quantity = 1;
    let itemsCostNumber = parseFloat(itemCost);
    let totalCost = quantity * itemsCostNumber;

    if (!selectedItems.some(item => item.id == itemId)) {
        selectedItems.push({
            id: itemId,
            name: itemName,
            description: itemDescription,
            quantity: quantity,
            items_cost: itemsCostNumber,
            total_cost: totalCost,
            category: itemCategory
        });

        renderSelectedItems();
    }
});

// Function to render selected items in the table
function renderSelectedItems() {
    let tbody = $("#selectedItemsTable tbody");
    tbody.empty();

    selectedItems.forEach((item) => {
        let row = `<tr>
            <td>${item.id}</td>
            <td>${item.name}</td>
            <td>${item.description}</td>
            <td>
                <input type="number" class="form-control itemQuantity" data-id="${item.id}" value="${item.quantity}" min="1">
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

// Remove Item from Selection
$(document).on("click", ".removeItem", function () {
    let itemId = $(this).data("id");
    selectedItems = selectedItems.filter(item => item.id !== itemId);
    renderSelectedItems();
});

// Update Quantity and Total Cost
$(document).on("input", ".itemQuantity", function () {
    let itemId = $(this).data("id");
    let newQuantity = parseFloat($(this).val());

    if (isNaN(newQuantity) || newQuantity <= 0) return;

    selectedItems.forEach(item => {
        if (item.id == itemId) {
            item.quantity = newQuantity;
            item.total_cost = item.quantity * item.items_cost; // Recalculate total cost
        }
    });

    renderSelectedItems();
});

$("#saveToDatabase").click(function () {
    if (!selectedId) {
        alert("❌ Error: No selected ID found! Cannot save.");
        //console.error("❌ Error: No selected ID found! Cannot save.");
        return;
    }

    $.ajax({
        url: "add_pritems",
        method: "POST",
        data: { 
            trxid: selectedId,
            items: JSON.stringify(selectedItems)
        },
        contentType: "application/x-www-form-urlencoded",
        dataType: "json", // ✅ Important!
        success: function (response) {
            console.log("📌 Server Response:", response);
            //console.log("Selected ID:", selectedId);
            //console.log("Selected Items before AJAX request:", selectedItems);

            if (response.status === "success") {
                Swal.fire({
                    icon: "success",
                    title: "Success!",
                    text: response.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    $("#itemModal").modal("hide"); 
                    location.reload();
                });
            } else {
                //console.error("❌ Error: " + response.message);
                Swal.fire({
                    icon: "error",
                    title: "Error!",
                    text: response.message,
                    showConfirmButton: true
                });
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            //console.error("❌ AJAX request failed: " + textStatus + " - " + errorThrown);
            //console.log("🔎 Raw server response:", jqXHR.responseText);
            Swal.fire({
                icon: "error",
                title: "Error!",
                text: "❌ An unexpected error occurred.",
                showConfirmButton: true
            });
        }
    });
});

// Remove item when clicking the remove button
$(document).on("click", ".removeItem", function () {
    let itemId = $(this).data("id");
    selectedItems = selectedItems.filter(item => item.id != itemId);
    renderSelectedItems(); // Update the table
});

// Initial fetch of items when modal is opened (optional)
$('#itemModal').on('shown.bs.modal', function () {
    fetchItems();
});

$('#itemModal').on('shown.bs.modal', function () {
    console.log("Modal opened");
    fetchItems();
});

// delete items in the table
console.log("Selected items:", selectedItems);
$(document).on("click", ".delete-btn", function() {
    let itemId = $(this).data("id"); // Get item ID

    // Show confirmation popup (SweetAlert2)
    Swal.fire({
        title: "Do you want to delete?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "delete_prlis", // PHP file for deletion
                type: "POST",
                data: { id: itemId },
                dataType: "json",
                success: function(response) {
                    if (response.status === "success") {
                        Swal.fire("Deleted!", response.message, "success");
                        $("#inventory").DataTable().ajax.reload(null, false); // Refresh table smoothly
                    } else {
                        Swal.fire("Error!", response.message, "error");
                    }
                },
                error: function() {
                    Swal.fire("Error!", "Failed to delete the item.", "error");
                }
            });
        }
    });
});
</script>

</body>

</html>