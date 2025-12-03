<?php
session_start();
include "config/db.php"; // Ensure database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields";
        header("Location: login?error=" . urlencode($error));
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Set session data
            $_SESSION['id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['logged_in'] = true;
            // Update last_login BEFORE redirecting
            $updateStmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE email = :email");
            $updateStmt->execute([':email' => $email]);

            header("Location: Dashboard");
            exit;
        } else {
            $error = "Invalid email or password";
            header("Location: login?error=" . urlencode($error));
            exit;
        }
    } catch (PDOException $e) {
        error_log('Database error: ' . $e->getMessage());
        $error = "Server error. Please try again later.";
        header("Location: login?error=" . urlencode($error));
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
  <title>Inventory</title>
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
              <h4>Sign in to continue.</h4>
              <form class="pt-3">
                <div class="form-group">
                  <input type="email" class="form-control form-control-lg" id="exampleInputEmail1" placeholder="Email" name="email" required>
                </div>
                <div class="form-group">
                  <input name="password" type="password" class="form-control form-control-lg" id="exampleInputPassword1" placeholder="Password">
                </div>
                <div class="mt-3">
                  <button type="submit" class="btn btn-block btn-info btn-lg font-weight-medium auth-form-btn">SIGN UP</button>
                  
                </div>
                <div class="my-2 d-flex justify-content-between align-items-center">
                  <div >
                    
                    <label class="form-check-label">
                      <input type="checkbox">
                      Keep me signed in
                    </label>
                  </div>
                  
                  <a href="#" class="auth-link text-black">Forgot password?</a>
                </div>
                <div class="text-center mt-4 font-weight-light">
                  Don't have an account? <a href="signup" class="text-info">Create</a>
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

</body>

</html>
