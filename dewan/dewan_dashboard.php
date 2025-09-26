<?php
session_start();

// Set session timeout in seconds (e.g., 30 minutes)
$session_timeout = 1800; // 30 minutes * 60 seconds

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
  // Check the time of the last activity
  if (isset($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > $session_timeout) {
    // Session has expired, destroy the session and redirect to the login page
    session_unset();
    session_destroy();
    header("Location: ../login.php");
    exit();
  } else {
    // Update the last activity time
    $_SESSION['last_activity'] = time();
  }
} else {
  // If the user is not logged in, redirect to the login page
  header("Location: ../login.php");
  exit();
}

// Handling
if ($_SESSION['role'] == 'user') {
  header("Location: ../dewan/dewan_dashboard.php");
  exit();
}
// Lakukan koneksi ke database
require_once '../include/koneksi.php';

// Query untuk mengambil jumlah pengguna dari database (misalnya dari tabel 'users')
$query = "SELECT COUNT(*) AS total_pengguna FROM users";
$result = mysqli_query($koneksi, $query);

if ($result) {
  // Jika query berhasil, ambil hasilnya
  $row = mysqli_fetch_assoc($result);
  $total_pengguna = $row['total_pengguna'];
} else {
  // Jika query gagal, atur jumlah pengguna menjadi 0 atau tampilkan pesan kesalahan
  $total_pengguna = 0;
  echo "Error: " . mysqli_error($koneksi);
}

// Ambil informasi pengguna dari database
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($koneksi, $query);
if (!$result) {
  // Error saat mengambil data dari database
  die("Query error: " . mysqli_error($koneksi));
}
$user = mysqli_fetch_assoc($result);
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
  <link href="../css/sb-admin-2.css" rel="stylesheet" />
</head>

<body id="page-top">
  <!-- Page Wrapper -->
  <div id="wrapper">
    <?php
    require_once('../include/navbar_admin.php')
    ?>
    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <?php
        require_once('../include/topbar_admin.php')
        ?>
        <div class="container-fluid">
          <div class="d-flex mb-3">
            <!-- Tombol Tambah -->
            <a href="input_berkas.php" class="btn btn-primary mr-2">
              <i class="fa-solid fa-plus mr-2"></i>
              Tambah Berkas Baru
            </a>

            <!-- Tombol Cari -->
            <a href="cari_berkas.php" class="btn btn-primary">
              <i class="fa-solid fa-magnifying-glass mr-2"></i>
              Cari Berkas
            </a>
          </div>

          <div class="row">
            <div class="col-xl-12 col-md-12">
              <div class="card shadow mb-4">
                <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold text-primary">
                    Informasi
                  </h6>
                </div>
                <div class="card-body">
                  <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas vitae tellus ante. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Ut quis tristique turpis. Etiam bibendum diam in fermentum ultricies. Donec fermentum non arcu at consequat. Nullam sit amet volutpat est, vestibulum sagittis sem. Mauris ultricies ante a ultricies porttitor. Duis quis dictum mauris. Etiam consectetur pulvinar nisi id venenatis. Etiam lacinia nunc sed pretium ultricies. Nullam efficitur lorem in odio vulputate rutrum. Donec eu suscipit mi. Nullam vitae odio ligula. Suspendisse justo metus, volutpat vel ante a, auctor placerat odio.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <!-- Bootstrap core JavaScript-->
  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Core plugin JavaScript-->
  <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>

  <!-- Custom scripts for all pages-->
  <script src="../js/sb-admin-2.min.js"></script>

  <!-- Page level plugins -->
  <script src="../vendor/chart.js/Chart.min.js"></script>

</body>

</html>