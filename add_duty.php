<?php include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $personnel = $_POST['personnel_id'];
  $post = $_POST['duty_post_id'];
  $date = $_POST['duty_date'];
  $conn->query("INSERT INTO duty_schedule (personnel_id, duty_post_id, duty_date)
                VALUES ('$personnel', '$post', '$date')");
  header("Location: index.php");
  exit;
}

$personnel = $conn->query("SELECT * FROM personnel");
$posts = $conn->query("SELECT * FROM duty_post");
?>

<form method="POST">
  <label>Personnel:</label>
  <select name="personnel_id">
    <?php while($p = $personnel->fetch_assoc()): ?>
      <option value="<?= $p['id'] ?>"><?= $p['name'] ?></option>
    <?php endwhile; ?>
  </select><br>

  <label>Post:</label>
  <select name="duty_post_id">
    <?php while($dp = $posts->fetch_assoc()): ?>
      <option value="<?= $dp['id'] ?>"><?= $dp['location'] ?> - <?= $dp['shift'] ?> - <?= $dp['post_name'] ?></option>
    <?php endwhile; ?>
  </select><br>

  <label>Date:</label>
  <input type="date" name="duty_date"><br>

  <button type="submit">Assign Duty</button>
</form>
