<?php
include "config/db.php"; // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    // Validate input
    if (empty($username) || empty($email) || empty($password) || empty($role)) {
        $error = "Please fill in all fields";
        header("Location: signup?error=" . urlencode($error));
        exit;
    }

    // Check if email or username already exists
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email OR username = :username");
        $stmt->execute([':email' => $email, ':username' => $username]);
        if ($stmt->rowCount() > 0) {
        $error = "Email or username already exists";
        header("Location: signup?error=" . urlencode($error));
        exit;
        }

        // Insert user into database
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, created) VALUES (:username, :email, :password, :role, NOW())");
        $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password' => $hashedPassword,
            ':role' => $role
        ]);
        $error = "Registration successful!";
        header("Location: login?success=" . urlencode($error));
        exit;

    } catch (PDOException $e) {
        error_log('Database error: ' . $e->getMessage());
        $error = "Server error. Please try again later.";
        header("Location: signup?error=" . urlencode($error));
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Sign Up</title>
  <!-- base:css -->
  <link rel="stylesheet" href="vendors/typicons.font/font/typicons.css">
  <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
  <!-- endinject -->
  <!-- plugin css for this page -->
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="css/vertical-layout-light/style.css">
  <!-- endinject -->
  <link rel="shortcut icon" href="images/RS logo.png" />
</head>

<body>
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
      <div class="content-wrapper d-flex align-items-center auth px-0">
        <div class="row w-100 mx-0">
          <div class="col-lg-4 mx-auto">
            <div class="auth-form-light text-left py-5 px-4 px-sm-5">
              <div class="brand-logo">
                <img style="width:3em; height:2em;" src="images/RS logo.png" alt="logo">
              </div>
              <form class="pt-3" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
              <h4>New here?</h4>
              <h6 class="font-weight-light">Signing up is easy. It only takes a few steps</h6>
              <form class="pt-3">
                <div class="form-group">
                  <input name="username" type="text" class="form-control form-control-lg" id="exampleInputUsername1" placeholder="Username" required>
                </div>
                <div class="form-group">
                  <input name="email" type="email" class="form-control form-control-lg" id="exampleInputEmail1" placeholder="Email" required>
                </div>
                <div class="form-group">
                  <input name="password" type="password" class="form-control form-control-lg" id="exampleInputPassword1" placeholder="Password" required>
                </div>
                                <div class="form-group">
                  <select name="role" class="form-control form-control-lg" id="exampleFormControlSelect2">
                    <option>Admin</option>
                    <option>Manager</option>
                    <option>Accountant</option>
                    <option>Sales</option>
                    <option>Support</option>
                    <option>Other</option>
                  </select>
                </div>

                <div class="mt-3">
                  <button type="submit" class="btn btn-block btn-info btn-lg font-weight-medium auth-form-btn">SIGN UP</button>
                </div>
                <div class="text-center mt-4 font-weight-light">
                  Already have an account? <a href="login" class="text-info">Login</a>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <!-- content-wrapper ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->
  <!-- base:js -->
  <script src="vendors/js/vendor.bundle.base.js"></script>
  <!-- endinject -->
  <!-- inject:js -->
  <script src="js/off-canvas.js"></script>
  <script src="js/hoverable-collapse.js"></script>
  <script src="js/template.js"></script>
  <script src="js/settings.js"></script>
  <script src="js/todolist.js"></script>
  <!-- endinject -->
</body>

</html>
