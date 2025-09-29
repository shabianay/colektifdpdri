<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  require_once "./include/koneksi.php";

  // Ambil nilai dari form
  $username = mysqli_real_escape_string($koneksi, $_POST['username']);
  $password = mysqli_real_escape_string($koneksi, $_POST['password']);

  // Query untuk mencari user berdasarkan username
  $query = "SELECT * FROM users WHERE username = ?";
  $stmt = mysqli_prepare($koneksi, $query);
  mysqli_stmt_bind_param($stmt, "s", $username);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);

  if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);

    // Verifikasi password (⚠️ sebaiknya pakai password_hash di DB, ini masih plain text)
    if ($password === $user['password']) {
      // Set session variables
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['username'] = $user['username'];
      $_SESSION['role'] = $user['role'];
      $_SESSION['user'] = $user;

      // Redirect berdasarkan role
      if ($user['role'] == 'setjen') {
        header("Location: ./setjen/setjen_dashboard.php");
      } elseif ($user['role'] == 'dewan') {
        header("Location: ./dewan/dewan_dashboard.php");
      } else {
        $error_message = "Akses ditolak. Role tidak valid.";
      }
      exit();
    } else {
      $error_message = "Username atau password salah. Silakan coba lagi.";
    }
  } else {
    $error_message = "Akun tidak terdaftar. Silakan coba lagi.";
  }

  mysqli_stmt_close($stmt);
  mysqli_close($koneksi);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="" />
  <meta name="author" content="" />

  <title>Colektif</title>

  <!-- Custom fonts for this template-->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css" />
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet" />

  <!-- Custom styles for this template-->
  <link href="css/sb-admin-2.css" rel="stylesheet" />
  <style>
    body {
      height: 100vh;
      display: flex;
      align-items: center;
    }
  </style>
</head>

<body class="bg-gradient-primary">
  <div class="container">
    <div class="row d-flex align-items-center" style="min-height: 100vh;">
      <div class="col-lg-12">
        <div class="card o-hidden border-0 shadow-lg my-5 mx-auto" style="max-width: 500px;">
          <div class="card-body p-0">
            <div class="p-5">
              <div class="text-center">
                <img src="img/logodpdri.png" class="mb-2" style="width:20%; height: auto;">
                <h1 class="h4 text-gray-900 m-4">Halo, Silahkan Masuk!</h1>
              </div>
              <?php
              // Check if error message is set and not empty
              if (isset($error_message) && !empty($error_message)) {
                echo '<div class="alert alert-danger" role="alert">' . $error_message . '</div>';
              }
              ?>
              <form class="user" method="post" action="">
                <div class="form-group">
                  <input type="text" class="form-control form-control-user" name="username" id="username" placeholder="Username" required />
                </div>
                <div class="form-group">
                  <div class="input-group">
                    <input type="password" class="form-control form-control-user" name="password" id="password" placeholder="Password" required />
                    <div class="input-group-append">
                      <span class="input-group-text" style="cursor: pointer;" onclick="togglePasswordVisibility()">
                        <i id="password-icon" class="fas fa-eye-slash"></i>
                      </span>
                    </div>
                  </div>
                </div>
                <button type="submit" class="btn btn-primary btn-user btn-block" style="margin-top:30px;">
                  Masuk
                </button>
                <p class="text-center mt-3 mb-0"><a href="index.php" style="font-weight:500; text-decoration:none;">Balik ke Landing Page</a></p>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap core JavaScript-->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Core plugin JavaScript-->
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

  <!-- Custom scripts for all pages-->
  <script src="js/sb-admin-2.min.js"></script>

  <script>
    function togglePasswordVisibility() {
      var passwordInput = document.getElementById("password");
      var icon = document.getElementById("password-icon");

      if (passwordInput.type === "password") {
        passwordInput.type = "text";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
      } else {
        passwordInput.type = "password";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
      }
    }
  </script>
</body>

</html>