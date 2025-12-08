<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
  header("Location: login");
  exit;
}
include 'config/db.php';
include 'Get/fetch_category_item.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required for 🛒 Purchases

        *Record purchase orders from suppliers

        *Track order status (Pending, Received, Cancelled)

        *Supplier management -->
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
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
                      <a href="QuantityAdjustment" class="btn btn-primary mb-3">
                        << Back to Quantity Adjustment</a>
                          <div class="row mb-3">
                            <div class="col-md-6">
                              <input id="search"
                                class="form-control form-control-lg"
                                placeholder="Search item by name...">
                            </div>
                            <div class="col-md-6 text-end">
                              <button id="btnAdd" class="btn btn-primary btn-lg">
                                + Add Item
                              </button>
                            </div>
                          </div>
                          <div class="card p-3">
                            <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle">
                              <thead class="table-dark">
                                <tr>
                                  <th style="width:70px">ID</th>
                                  <th>Item Name</th>
                                  <th style="width:120px">Rate</th>
                                  <th style="width:120px">Tax (%)</th>
                                  <th>Description</th>
                                  <th style="width:150px">Actions</th>
                                </tr>
                              </thead>
                              <tbody id="itemsTbody"></tbody>
                            </table>
                            </div>
                          </div>
                          <div class="modal fade" id="itemModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-md">
                              <div class="modal-content">
                                <form id="itemForm">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="modalTitle">Add Item</h5>
                                    <button type="button" class="btn-close" data-dismiss="modal"></button>
                                  </div>
                                  <div class="modal-body p-3">
                                    <input type="hidden" id="itemId">
                                    <div class="mb-3">
                                      <select name="category" class="form-control">
                                        <option value="" disabled selected>Select Category</option>
                                        <?php foreach ($category as $categories): ?>
                                          <option value="<?= $categories['id'] ?>"><?= htmlspecialchars($categories['cat_name']) ?></option>
                                        <?php endforeach; ?>
                                      </select>
                                      <input id="itemName" class="form-control" required>
                                    </div>
                                    <div class="mb-3 row">
                                      <div class="col">
                                        <label class="form-label">Rate</label>
                                        <input id="itemRate" type="number" step="0.01" class="form-control" value="0">
                                      </div>
                                      <div class="col">
                                        <label class="form-label">Tax (%)</label>
                                        <input id="itemTax" type="number" step="0.01" class="form-control" value="0">
                                      </div>
                                    </div>
                                    <div class="mb-3">
                                      <label class="form-label">Description</label>
                                      <textarea id="itemDesc" class="form-control" rows="3"></textarea>
                                    </div>
                                  </div>

                                  <div class="modal-footer">
                                    <button type="submit" class="btn btn-info" id="saveBtn">Save</button>
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                  </div>
                                </form>
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
  <script>
    const apiUrl = 'Get/fetch_items.php';
    let items = [];
    const itemModal = new bootstrap.Modal(document.getElementById('itemModal'));

    // Elements
    const tbody = document.getElementById('itemsTbody');
    const search = document.getElementById('search');
    const btnAdd = document.getElementById('btnAdd');
    const modalTitle = document.getElementById('modalTitle');
    const itemForm = document.getElementById('itemForm');
    const itemId = document.getElementById('itemId');
    const itemName = document.getElementById('itemName');
    const itemRate = document.getElementById('itemRate');
    const itemTax = document.getElementById('itemTax');
    const itemDesc = document.getElementById('itemDesc');

    // Load items from server
    async function loadItems(q = '') {
      const url = q ? `${apiUrl}?q=${encodeURIComponent(q)}` : apiUrl;
      const res = await fetch(url);
      items = await res.json();
      renderTable();
    }

    function renderTable() {
      tbody.innerHTML = '';

      if (!Array.isArray(items)) return;

      items.forEach(it => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
      <td class="text-center">${it.id}</td>
      <td>${escapeHtml(it.item_name)}</td>
      <td class="text-end">${Number(it.rate).toLocaleString()}</td>
      <td class="text-end">${Number(it.tax_rate)}</td>
      <td>${escapeHtml(it.description ?? '')}</td>
      <td class="text-center">
        <button class="btn btn-warning btn-sm editBtn" data-id="${it.id}">
          Edit
        </button>
        <button class="btn btn-danger btn-sm deleteBtn" data-id="${it.id}">
          Delete
        </button>
      </td>
    `;
        tbody.appendChild(tr);
      });
    }


    // Simple escape
    function escapeHtml(s) {
      return s ? s.replace(/[&<>"']/g, c => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;'
      } [c])) : '';
    }

    // Add new
    btnAdd.addEventListener('click', () => {
      modalTitle.textContent = 'Add Item';
      itemForm.reset();
      itemId.value = '';
      itemModal.show();
    });

    // Submit form: create or update
    itemForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const payload = {
        item_name: itemName.value.trim(),
        rate: parseFloat(itemRate.value) || 0,
        tax_rate: parseFloat(itemTax.value) || 0,
        description: itemDesc.value.trim()
      };

      if (!payload.item_name) {
        alert('Item name required');
        return;
      }

      if (itemId.value) {
        // update via PUT
        payload.id = itemId.value;
        const res = await fetch(apiUrl, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(payload)
        });
        if (!res.ok) {
          alert('Update failed');
          return;
        }
      } else {
        // create via POST (form-encoded can be used; we'll use JSON)
        const res = await fetch(apiUrl, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(payload)
        });
        if (!res.ok) {
          alert('Create failed');
          return;
        }
      }

      itemModal.hide();
      loadItems(search.value.trim());
    });

    // Delegated event handlers for edit/delete
    tbody.addEventListener('click', async (e) => {
      if (e.target.classList.contains('editBtn')) {
        const id = e.target.dataset.id;
        const it = items.find(x => String(x.id) === String(id));
        if (!it) return;
        modalTitle.textContent = 'Edit Item';
        itemId.value = it.id;
        itemName.value = it.item_name;
        itemRate.value = it.rate;
        itemTax.value = it.tax_rate;
        itemDesc.value = it.description || '';
        itemModal.show();
      }

      if (e.target.classList.contains('deleteBtn')) {
        const id = e.target.dataset.id;
        if (!confirm('Delete this item?')) return;
        const res = await fetch(apiUrl + '?id=' + encodeURIComponent(id), {
          method: 'DELETE'
        });
        if (res.ok) {
          loadItems(search.value.trim());
        } else {
          alert('Delete failed');
        }
      }
    });

    // Live search
    let searchTimer = null;

    search.addEventListener('input', () => {
      clearTimeout(searchTimer);
      searchTimer = setTimeout(() => loadItems(search.value.trim()), 300);
    });


    // initial load
    loadItems();
  </script>
</body>

</html>