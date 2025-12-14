<!DOCTYPE html>
<html>
<head>
  <title>Company & System Information</title>
    <link rel="stylesheet" href="vendors/typicons.font/font/typicons.css">
  <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="css/vertical-layout-light/style.css">
  <link rel="stylesheet" href="css/product.css">
  <link rel="shortcut icon" href="images/favicon.png" />
</head>
<body class="bg-light">

<div class="container py-4">
  <h4 class="fw-bold">🏢 Company & System Information</h4>
  <p class="text-muted">Manage company profile and system defaults.</p>

  <div id="alertBox"></div>

  <form id="settingsForm" enctype="multipart/form-data">
    <div class="card mb-3">
      <div class="card-body row g-3">
        <div class="col-md-8">
          <label class="form-label">Company Name *</label>
          <input type="text" name="company_name" class="form-control" required>
        </div>

        <div class="col-md-4">
          <label class="form-label">Logo</label>
          <input type="file" name="company_logo" class="form-control file-upload-info" onchange="previewLogo(this)">
          <img id="logoPreview" class="mt-2" style="max-height:100px;">
        </div>

        <div class="col-md-12">
          <label class="form-label">Address</label>
          <textarea name="company_address" class="form-control"></textarea>
        </div>

        <div class="col-md-4">
          <label class="form-label">Phone</label>
          <input type="text" name="company_phone" class="form-control">
        </div>

        <div class="col-md-4">
          <label class="form-label">Email</label>
          <input type="email" name="company_email" class="form-control">
        </div>

        <div class="col-md-4">
          <label class="form-label">Tax ID</label>
          <input type="text" name="tax_id" class="form-control">
        </div>
      </div>
    </div>

    <div class="card mb-3">
      <div class="card-body row g-3">
        <div class="col-md-4">
          <label>Currency</label>
          <select name="currency" class="form-select">
            <option value="PHP">PHP</option>
            <option value="USD">USD</option>
          </select>
        </div>

        <div class="col-md-4">
          <label>Timezone</label>
          <select name="timezone" class="form-select">
            <option value="Asia/Manila">Asia/Manila</option>
            <option value="UTC">UTC</option>
          </select>
        </div>

        <div class="col-md-4">
          <label>Date Format</label>
          <select name="date_format" class="form-select">
            <option value="Y-m-d">YYYY-MM-DD</option>
            <option value="m/d/Y">MM/DD/YYYY</option>
          </select>
        </div>
      </div>
    </div>

    <div class="text-end">
      <button class="btn btn-primary">Save Settings</button>
    </div>
  </form>
</div>

<script>
function previewLogo(input) {
  if (input.files[0]) {
    document.getElementById("logoPreview").src = URL.createObjectURL(input.files[0]);
  }
}

// Load existing settings
fetch("get_settings.php")
.then(res => res.json())
.then(data => {
  if (!data.company_name) return;
  Object.keys(data).forEach(key => {
    if (document.querySelector(`[name="${key}"]`)) {
      document.querySelector(`[name="${key}"]`).value = data[key];
    }
  });
  if (data.company_logo) {
    document.getElementById("logoPreview").src = data.company_logo;
  }
});

// AJAX submit
document.getElementById("settingsForm").addEventListener("submit", function(e){
  e.preventDefault();
  let formData = new FormData(this);

  fetch("save_settings.php", {
    method: "POST",
    body: formData
  })
  .then(res => res.json())
  .then(res => {
    document.getElementById("alertBox").innerHTML =
      `<div class="alert alert-${res.status=='success'?'success':'danger'}">${res.message}</div>`;
  });
});
</script>

</body>
</html>
