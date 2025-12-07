<?php
include 'db.php';

// Get week range
$selected = $_GET['week'] ?? date('Y-m-d');
$start = new DateTime($selected);
$start->modify('Monday this week');
$weekStart = $start->format("Y-m-d");
$end = clone $start;
$end->modify('+6 days');
$weekEnd = $end->format("Y-m-d");

// Generate dates for the week
$dates = [];
$period = new DatePeriod(new DateTime($weekStart), new DateInterval('P1D'), (new DateTime($weekEnd))->modify('+1 day'));
foreach ($period as $day) {
  $dates[] = $day->format("Y-m-d");
}

// Fetch posts and personnel
$personnel = $conn->query("SELECT * FROM personnel ORDER BY name");
$posts = $conn->query("SELECT * FROM duty_post ORDER BY location, shift");

// Build post data
$schedule = [];
$postList = [];
foreach ($posts as $post) {
  $key = $post['location'] . ' - ' . $post['shift'] ;
  $schedule[$key] = [];
  foreach ($dates as $date) {
    $schedule[$key][$date] = [];
  }
  $postList[$key] = $post['id'];
}

// Fetch scheduled duties
$sql = "SELECT ds.*, p.name AS personnel, p.id AS personnel_id, dp.id AS post_id, dp.post_name, dp.shift, dp.location
        FROM duty_schedule ds
        JOIN personnel p ON ds.personnel_id = p.id
        JOIN duty_post dp ON ds.duty_post_id = dp.id
        WHERE ds.duty_date BETWEEN '$weekStart' AND '$weekEnd'";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
  $key = $row['location'] . ' - ' . $row['shift'] . ' - ' . $row['post_name'];
  $schedule[$key][$row['duty_date']][] = [
    'id' => $row['personnel_id'],
    'name' => $row['personnel'],
    'post_id' => $row['post_id'],
    'schedule_id' => $row['id']
  ];
}

// Add duty post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_post'])) {
  $post = $_POST['post_name'];
  $location = $_POST['location'];
  $shift = $_POST['shift'];
  $conn->query("INSERT INTO duty_post (post_name, location, shift) VALUES ('$post', '$location', '$shift')");
  echo "<script>window.location.href=window.location.href;</script>";
  exit;
}

// Add personnel
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name']) && !isset($_POST['personnel_id'])) {
  $name = $_POST['name'];
  $conn->query("INSERT INTO personnel (name) VALUES ('$name')");
  echo "<script>window.location.href=window.location.href;</script>";
  exit;
}

// Assign duty
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['personnel_id'], $_POST['duty_post_id'], $_POST['duty_date'])) {
  $pid = (int) $_POST['personnel_id'];
  $dpid = (int) $_POST['duty_post_id'];
  $ddate = $_POST['duty_date'];
  $conn->query("INSERT INTO duty_schedule (personnel_id, duty_post_id, duty_date) VALUES ($pid, $dpid, '$ddate')");
  echo "<script>window.location.href=window.location.href;</script>";
  exit;
}

// Move duty (drag and drop)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['move_personnel'])) {
  $personnel_id = (int) $_POST['id'];
  $new_date = $_POST['date'];
  $new_post_id = (int) $_POST['post_id'];
  $conn->query("DELETE FROM duty_schedule WHERE personnel_id = $personnel_id AND duty_date = '$new_date'");
  $conn->query("INSERT INTO duty_schedule (personnel_id, duty_post_id, duty_date) VALUES ($personnel_id, $new_post_id, '$new_date')");
  exit;
}

// Edit duty
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_schedule_id'])) {
  $sid = (int) $_POST['edit_schedule_id'];
  $pid = (int) $_POST['edit_personnel_id'];
  $dpid = (int) $_POST['edit_duty_post_id'];
  $ddate = $_POST['edit_duty_date'];
  $conn->query("UPDATE duty_schedule SET personnel_id = $pid, duty_post_id = $dpid, duty_date = '$ddate' WHERE id = $sid");
  echo "<script>window.location.href=window.location.href;</script>";
  exit;
}

// Delete duty
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_schedule_id'])) {
  $sid = (int) $_POST['delete_schedule_id'];
  $conn->query("DELETE FROM duty_schedule WHERE id = $sid");
  echo "<script>window.location.href=window.location.href;</script>";
  exit;
}
?>



<!DOCTYPE html>
<html lang="en">
<head>

  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Inventory</title>

  <!-- Bootstrap 5 CSS -->
  <link rel="stylesheet" href="vendors/typicons.font/font/typicons.css" />
  <link rel="stylesheet" href="css/vertical-layout-light/style.css" />
  <link rel="shortcut icon" href="images/favicon.png" />
  
</head>
<body>
  <div class="container-scroller">
    <?php include 'Navbar/nav.php'; ?>
    <div class="container-fluid page-body-wrapper">
      <div class="theme-setting-wrapper">
        <div id="settings-trigger"><i class="typcn typcn-cog-outline"></i></div>
        <div id="theme-settings" class="settings-panel">
          <i class="settings-close typcn typcn-delete-outline"></i>
          <p class="settings-heading">SIDEBAR SKINS</p>
          <div class="sidebar-bg-options" id="sidebar-light-theme">
            <div class="img-ss rounded-circle bg-light border me-3"></div>
            Light
          </div>
          <div class="sidebar-bg-options selected" id="sidebar-dark-theme">
            <div class="img-ss rounded-circle bg-dark border me-3"></div>
            Dark
          </div>
          <p class="settings-heading mt-2">HEADER SKINS</p>
          <div class="color-tiles mx-0 px-4">
            <div class="tiles bg-success"></div>
            <div class="tiles bg-warning"></div>
            <div class="tiles bg-danger"></div>
            <div class="tiles bg-primary"></div>
            <div class="tiles bg-info"></div>
            <div class="tiles bg-dark"></div>
            <div class="tiles border"></div>
          </div>
        </div>
      </div>

      <?php include 'sidebar.php'; ?>
<!-- Main content -->
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-12">
              <div class="card p-3">
                <h2 class="text-center mb-4">Weekly Duty Schedule</h2>
                <form method="GET" style="margin-bottom: 20px;">
  <label><strong>📆 Select Week Start:</strong></label>
  <input type="date" name="week" value="<?= htmlspecialchars($_GET['week'] ?? date('Y-m-d')) ?>" required>
  <button class="btn btn-secondary rounded-pill" type="submit">View</button>

</form>
<div>
  <button class="btn btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#addPostModal">
  ➕ Add Duty Post
</button>
  <button class="btn btn-success rounded-pill" data-bs-toggle="modal" data-bs-target="#addPersonnelModal">
  ➕ Add Personnel
</button>
<button class="btn btn-light rounded-pill" data-bs-toggle="modal" data-bs-target="#addPersonneldutyModal">
  ➕ Add Assign Duty
</button>
</div>



                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>Post</th>
                      <?php foreach ($dates as $d): ?>
                        <th><?= date("d M Y (D)", strtotime($d)) ?></th>
                      <?php endforeach; ?>
                    </tr>
                  </thead>
                  <tbody>
   <?php foreach ($schedule as $post => $row): ?>
  <tr>
    <td><?= $post ?></td>
    <?php foreach ($dates as $d): ?>


<td class="dropzone" data-date="<?= $d ?>" data-post-id="<?= $postMap[$post] ?>">
  <?php foreach ($row[$d] as $person): ?>
    <div class="draggable" draggable="true" 
         data-id="<?= $person['id'] ?>" 
         data-post-id="<?= $person['post_id'] ?>">
      <span class="badge badge-primary ml-3"><?= htmlspecialchars($person['name']) ?></span>
    </div>
  <?php endforeach; ?>
</td>



    <?php endforeach; ?>
  </tr>
<?php endforeach; ?>
                  </tbody>
                </table>
                

              </div>
              
            </div>
          </div>

<!-- Modals for Edit/Delete -->
<div class="modal fade" id="editDutyModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Duty</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="edit_schedule_id" id="edit_schedule_id">
        <label>Personnel:</label>
        <select class="form-select" name="edit_personnel_id" id="edit_personnel_id"></select>
        <label>Post:</label>
        <select class="form-select" name="edit_duty_post_id" id="edit_duty_post_id"></select>
        <label>Date:</label>
        <input type="date" name="edit_duty_date" id="edit_duty_date" class="form-control">
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save Changes</button>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="deleteDutyModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Delete Duty</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="delete_schedule_id" id="delete_schedule_id">
        <p>Are you sure you want to delete this duty assignment?</p>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-danger">Delete</button>
      </div>
    </form>
  </div>
</div>

          <!-- 📋 Modal for adding duty posts -->
          <div class="modal fade" id="addPostModal" tabindex="-1" aria-labelledby="addPostModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addPostModalLabel">Add Duty Post</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="add_post" value="1" />

        <div class="mb-3">
          <label for="location" class="form-label">Location</label>
          <input type="text" class="form-control" name="location" required placeholder="e.g. DPO (NSPL)">
        </div>


        <div class="mb-3">
          <label for="shift" class="form-label">Shift</label>
          <select class="form-control" name="shift" required>
            <option value="1st">1st</option>
            <option value="2nd">2nd</option>
          </select>
        </div>
      </div>

      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Save Post</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </form>
  </div>
</div>
                <!-- End of 📋 Modal -->

                          <!-- 📋 Modal for adding personnel -->
          <div class="modal fade" id="addPersonnelModal" tabindex="-1" aria-labelledby="addPostModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addPostModalLabel">Add Personnel</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="name" value="1" />

        <div class="mb-3">
          <label for="location" class="form-label">Name</label>
          <input type="text" class="form-control" name="name" required placeholder="e.g. SN1 Herrera PN">
        </div>
      </div>

      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Save Personnel</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </form>
  </div>
</div>
                <!-- End of 📋 Modal -->

                                          <!-- 📋 Modal for adding duty posts -->
          <div class="modal fade" id="addPersonneldutyModal" tabindex="-1" aria-labelledby="addPostModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addPostModalLabel">Add Personnel Duty</h5>
        </div>
        <div class="modal-body">
        <div class="mb-3">
 <label class="form-label">Personnel:</label>
  <select class="form-control" name="personnel_id">
    <?php while($p = $personnel->fetch_assoc()): ?>
      <option value="<?= $p['id'] ?>"><?= $p['name'] ?></option>
    <?php endwhile; ?>
  </select>
        </div>
        <div class="mb-3">
  <label class="form-label">Post:</label>
  <select class="form-control" name="duty_post_id">
    <?php while($dp = $posts->fetch_assoc()): ?>
      <option value="<?= $dp['id'] ?>"><?= $dp['location'] ?> - <?= $dp['shift'] ?></option>
    <?php endwhile; ?>
  </select>
        </div>
        <div class="mb-3">
  <label class="form-label">Date:</label>
  <input class="form-control" type="date" name="duty_date">
        </div>
  
              <div class="modal-footer">
                <button class="btn btn-secondary rounded-pill" type="submit">Assign Duty</button>
              <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
      
    </form>
  </div>
</div>
                <!-- End of 📋 Modal -->
        </div>
        <footer class="footer">
          <div class="d-sm-flex justify-content-center justify-content-sm-between">
            <span class="text-center text-sm-left d-block d-sm-inline-block">Copyright © <a href="#">randolfh.com</a> 2025</span>
          </div>
        </footer>
      </div>
      <!-- End of main content -->
    </div>
  </div>
  <!-- JS logic -->
  <script>
document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".draggable").forEach(el => {
    el.addEventListener("dragstart", e => {
      e.dataTransfer.setData("text/plain", JSON.stringify({
        id: e.target.dataset.id,
        postId: e.target.dataset.postId
      }));
    });

    el.addEventListener("click", e => {
      const scheduleId = el.dataset.scheduleId;
      const date = el.closest('.dropzone').dataset.date;
      const postId = el.dataset.postId;
      document.getElementById('edit_schedule_id').value = scheduleId;
      document.getElementById('delete_schedule_id').value = scheduleId;
      document.getElementById('edit_duty_date').value = date;
      // Populate personnel and post dropdowns (static or dynamic)
      new bootstrap.Modal(document.getElementById('editDutyModal')).show();
    });
  });
});
</script>
</body>
</html>