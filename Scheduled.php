<?php
include 'db.php';

// --- Helper: sanitize POST/GET inputs ---
function in($conn, $k, $default = null) {
  if (isset($_REQUEST[$k])) return $conn->real_escape_string($_REQUEST[$k]);
  return $default;
}

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

// Fetch personnel and posts (main queries)
$personnel = $conn->query("SELECT * FROM personnel ORDER BY name");
$posts = $conn->query("SELECT * FROM duty_post ORDER BY location, shift, post_name");

// Build post data and mapping
$schedule = [];
$postList = []; // key => post id
$postKeys = []; // ordered list of keys for table rows
while ($post = $posts->fetch_assoc()) {
  // Unique key includes post_name to avoid collisions
  $key = $post['location'] . ' - ' . $post['shift'] . ' - ' . $post['post_name'];
  $postList[$key] = $post['id'];
  $postKeys[] = $key;
  $schedule[$key] = [];
  foreach ($dates as $date) {
    $schedule[$key][$date] = [];
  }
}

// Fetch scheduled duties
$sql = "SELECT ds.*, p.name AS personnel_name, p.id AS personnel_id, dp.id AS post_id, dp.post_name, dp.shift, dp.location
        FROM duty_schedule ds
        JOIN personnel p ON ds.personnel_id = p.id
        JOIN duty_post dp ON ds.duty_post_id = dp.id
        WHERE ds.duty_date BETWEEN '$weekStart' AND '$weekEnd'";

$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
  $key = $row['location'] . ' - ' . $row['shift'] . ' - ' . $row['post_name'];
  // Ensure the schedule key exists (in case posts changed)
  if (!isset($schedule[$key])) {
    // initialize if a post was added after we built post list
    $schedule[$key] = [];
    foreach ($dates as $date) $schedule[$key][$date] = [];
    $postList[$key] = $row['post_id'];
    $postKeys[] = $key;
  }

  // Push item with schedule id so we can update/delete precisely
  $schedule[$key][$row['duty_date']][] = [
    'schedule_id' => $row['id'],
    'personnel_id' => $row['personnel_id'],
    'personnel_name' => $row['personnel_name'],
    'post_id' => $row['post_id']
  ];
}

// ---------- Handle POST actions ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Add duty post
  if (isset($_POST['add_post'])) {
    $post_name = $conn->real_escape_string($_POST['post_name']);
    $location = $conn->real_escape_string($_POST['location']);
    $shift = $conn->real_escape_string($_POST['shift']);
    $conn->query("INSERT INTO duty_post (post_name, location, shift) VALUES ('$post_name', '$location', '$shift')");
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
  }

  // Add personnel
  if (isset($_POST['add_personnel'])) {
    $name = $conn->real_escape_string($_POST['name']);
    if ($name !== '') {
      $conn->query("INSERT INTO personnel (name) VALUES ('$name')");
    }
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
  }

  // Assign duty
  if (isset($_POST['assign_personnel_id'], $_POST['assign_duty_post_id'], $_POST['assign_duty_date'])) {
    $pid = (int) $_POST['assign_personnel_id'];
    $dpid = (int) $_POST['assign_duty_post_id'];
    $ddate = $conn->real_escape_string($_POST['assign_duty_date']);
    $conn->query("INSERT INTO duty_schedule (personnel_id, duty_post_id, duty_date) VALUES ($pid, $dpid, '$ddate')");
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
  }

  // Move duty (drag and drop) - expects schedule_id, new_date, new_post_id
  if (isset($_POST['move_schedule_id'])) {
    $sid = (int) $_POST['move_schedule_id'];
    $new_date = $conn->real_escape_string($_POST['new_date']);
    $new_post_id = (int) $_POST['new_post_id'];
    // Update by schedule id so we don't accidentally delete other rows
    $conn->query("UPDATE duty_schedule SET duty_post_id = $new_post_id, duty_date = '$new_date' WHERE id = $sid");
    echo json_encode(['ok' => true]);
    exit;
  }

  // Edit duty
  if (isset($_POST['edit_schedule_id'])) {
    $sid = (int) $_POST['edit_schedule_id'];
    $pid = (int) $_POST['edit_personnel_id'];
    $dpid = (int) $_POST['edit_duty_post_id'];
    $ddate = $conn->real_escape_string($_POST['edit_duty_date']);
    $conn->query("UPDATE duty_schedule SET personnel_id = $pid, duty_post_id = $dpid, duty_date = '$ddate' WHERE id = $sid");
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
  }

  // Delete duty
  if (isset($_POST['delete_schedule_id'])) {
    $sid = (int) $_POST['delete_schedule_id'];
    $conn->query("DELETE FROM duty_schedule WHERE id = $sid");
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
  }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Weekly Duty Schedule</title>
  <link rel="stylesheet" href="vendors/typicons.font/font/typicons.css" />
  <link rel="stylesheet" href="css/vertical-layout-light/style.css" />
  <link rel="shortcut icon" href="images/favicon.png" />
  <style>
  .badge-person { cursor: grab; display: inline-block; margin: 2px 0; }
  .dropzone { min-height: 60px; vertical-align: top; }
  .dropzone.drag-over { outline: 2px dashed #007bff; }
  </style>
</head>
<body>
  <div class="container-scroller">
    <?php include 'Navbar/nav.php'; ?>
    <div class="container-fluid page-body-wrapper">
      <?php include 'sidebar.php'; ?>
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="card p-3">
            <h2 class="text-center mb-4">Weekly Duty Schedule</h2>
            <form method="GET" style="margin-bottom: 20px;">
              <label><strong>📆 Select Week Start:</strong></label>
              <input type="date" name="week" value="<?= htmlspecialchars($_GET['week'] ?? date('Y-m-d')) ?>" required>
              <button class="btn btn-secondary rounded-pill" type="submit">View</button>
            </form>

            <div class="mb-3">
              <button class="btn btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#addPostModal">➕ Add Duty Post</button>
              <button class="btn btn-success rounded-pill" data-bs-toggle="modal" data-bs-target="#addPersonnelModal">➕ Add Personnel</button>
              <button class="btn btn-light rounded-pill" data-bs-toggle="modal" data-bs-target="#assignDutyModal">➕ Assign Duty</button>
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
                <?php foreach ($postKeys as $postKey): ?>
                  <tr>
                    <td><?= htmlspecialchars($postKey) ?></td>
                    <?php foreach ($dates as $d): ?>
                      <?php $cellPostId = $postList[$postKey] ?? '0'; ?>
                      <td class="dropzone" data-date="<?= $d ?>" data-post-id="<?= $cellPostId ?>">
                        <?php foreach ($schedule[$postKey][$d] as $person): ?>
                          <div class="draggable badge-person" draggable="true"
                               data-schedule-id="<?= $person['schedule_id'] ?>"
                               data-person-id="<?= $person['personnel_id'] ?>"
                               data-post-id="<?= $person['post_id'] ?>"
                               title="<?= htmlspecialchars($person['personnel_name']) ?>">
                            <span class="badge bg-primary"><?= htmlspecialchars($person['personnel_name']) ?></span>
                            <div style="display:inline-block; margin-left:6px; font-size:0.8em; color:#333;">&nbsp;</div>
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

        <!-- Edit Duty Modal -->
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
                <select class="form-select" name="edit_personnel_id" id="edit_personnel_id">
                  <?php
                  $personnel2 = $conn->query("SELECT * FROM personnel ORDER BY name");
                  while($p = $personnel2->fetch_assoc()):
                  ?>
                  <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                  <?php endwhile; ?>
                </select>

                <label class="mt-2">Post:</label>
                <select class="form-select" name="edit_duty_post_id" id="edit_duty_post_id">
                  <?php
                  $posts3 = $conn->query("SELECT * FROM duty_post ORDER BY location, shift, post_name");
                  while($dp = $posts3->fetch_assoc()):
                  ?>
                  <option value="<?= $dp['id'] ?>"><?= htmlspecialchars($dp['location'] . ' - ' . $dp['shift'] . ' - ' . $dp['post_name']) ?></option>
                  <?php endwhile; ?>
                </select>

                <label class="mt-2">Date:</label>
                <input type="date" name="edit_duty_date" id="edit_duty_date" class="form-control">
              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save Changes</button>
              </div>
            </form>
          </div>
        </div>

        <!-- Delete Duty Modal -->
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

        <!-- Add Duty Post Modal -->
        <div class="modal fade" id="addPostModal" tabindex="-1">
          <div class="modal-dialog modal-lg">
            <form method="POST" class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Add Duty Post</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <input type="hidden" name="add_post" value="1" />
                <div class="mb-3">
                  <label class="form-label">Post Name</label>
                  <input type="text" class="form-control" name="post_name" required placeholder="e.g. Main Gate">
                </div>
                <div class="mb-3">
                  <label class="form-label">Location</label>
                  <input type="text" class="form-control" name="location" required placeholder="e.g. DPO (NSPL)">
                </div>
                <div class="mb-3">
                  <label class="form-label">Shift</label>
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

        <!-- Add Personnel Modal -->
        <div class="modal fade" id="addPersonnelModal" tabindex="-1">
          <div class="modal-dialog modal-lg">
            <form method="POST" class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Add Personnel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <input type="hidden" name="add_personnel" value="1" />
                <div class="mb-3">
                  <label class="form-label">Name</label>
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

        <!-- Assign Duty Modal -->
        <div class="modal fade" id="assignDutyModal" tabindex="-1">
          <div class="modal-dialog modal-lg">
            <form method="POST" class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Assign Duty</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <div class="mb-3">
                  <label class="form-label">Personnel:</label>
                  <select class="form-control" name="assign_personnel_id">
                    <?php
                    $personnel3 = $conn->query("SELECT * FROM personnel ORDER BY name");
                    while($p = $personnel3->fetch_assoc()):
                    ?>
                      <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                    <?php endwhile; ?>
                  </select>
                </div>

                <div class="mb-3">
                  <label class="form-label">Post:</label>
                  <select class="form-control" name="assign_duty_post_id">
                    <?php
                    $posts4 = $conn->query("SELECT * FROM duty_post ORDER BY location, shift, post_name");
                    while($dp = $posts4->fetch_assoc()):
                    ?>
                      <option value="<?= $dp['id'] ?>"><?= htmlspecialchars($dp['location'] . ' - ' . $dp['shift'] . ' - ' . $dp['post_name']) ?></option>
                    <?php endwhile; ?>
                  </select>
                </div>

                <div class="mb-3">
                  <label class="form-label">Date:</label>
                  <input class="form-control" type="date" name="assign_duty_date">
                </div>
              </div>
              <div class="modal-footer">
                <button class="btn btn-secondary rounded-pill" type="submit">Assign Duty</button>
                <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
              </div>
            </form>
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
  <script>
  document.addEventListener("DOMContentLoaded", function () {
    // Drag start
    document.querySelectorAll('.draggable').forEach(el => {
      el.addEventListener('dragstart', e => {
        const payload = {
          schedule_id: el.dataset.scheduleId,
          person_id: el.dataset.personId,
          post_id: el.dataset.postId
        };
        e.dataTransfer.setData('text/plain', JSON.stringify(payload));
      });

      // Click to open edit modal
      el.addEventListener('click', e => {
        const sid = el.dataset.scheduleId;
        const pid = el.dataset.personId;
        const postid = el.dataset.postId;
        const date = el.closest('.dropzone').dataset.date;
        document.getElementById('edit_schedule_id').value = sid;
        document.getElementById('edit_duty_date').value = date;
        // set selects
        const personSelect = document.getElementById('edit_personnel_id');
        personSelect.value = pid;
        const postSelect = document.getElementById('edit_duty_post_id');
        postSelect.value = postid;
        new bootstrap.Modal(document.getElementById('editDutyModal')).show();
      });
    });

    // Drop handlers
    document.querySelectorAll('.dropzone').forEach(zone => {
      zone.addEventListener('dragover', e => {
        e.preventDefault();
        zone.classList.add('drag-over');
      });
      zone.addEventListener('dragleave', e => zone.classList.remove('drag-over'));

      zone.addEventListener('drop', e => {
        e.preventDefault();
        zone.classList.remove('drag-over');
        try {
          const data = JSON.parse(e.dataTransfer.getData('text/plain'));
        
          const form = new FormData();
          form.append('move_schedule_id', data.schedule_id);
          form.append('new_date', zone.dataset.date);
          form.append('new_post_id', zone.dataset.postId);

          fetch('', { method: 'POST', body: form })
            .then(r => r.json())
            .then(j => { if (j.ok) location.reload(); else location.reload(); })
            .catch(err => { console.error(err); location.reload(); });
        } catch (err) {
          console.error('Invalid drag data', err);
        }
      });
    });
  });
  </script>
</body>
</html>
