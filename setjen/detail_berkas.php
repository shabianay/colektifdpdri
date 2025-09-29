<?php
session_start();

// Set session timeout (30 menit)
$session_timeout = 1800;

if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > $session_timeout) {
        session_unset();
        session_destroy();
        header("Location: ../login.php");
        exit();
    } else {
        $_SESSION['last_activity'] = time();
    }
} else {
    header("Location: ../login.php");
    exit();
}

// Redirect user biasa
if ($_SESSION['role'] == 'user') {
    header("Location: ../setjen/setjen_dashboard.php");
    exit();
}

require_once "../include/koneksi.php";

if (!isset($_GET['id'])) {
    // Tampilkan halaman redirect yang menarik
    include 'id_berkas_notfound.php';
    exit();
}

$berkas_id = $_GET['id'];

// Ambil data berkas dari database
$query_berkas = "SELECT * FROM berkas WHERE id = $berkas_id";
$result_berkas = mysqli_query($koneksi, $query_berkas);

if (!$result_berkas || mysqli_num_rows($result_berkas) == 0) {
    die("Data berkas tidak ditemukan");
}

$berkas = mysqli_fetch_assoc($result_berkas);

// Ambil info user (opsional)
$user_id = $_SESSION['user_id'];
$query_user = "SELECT * FROM users WHERE id = $user_id";
$result_user = mysqli_query($koneksi, $query_user);
$user = mysqli_fetch_assoc($result_user);
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
    <link href="../css/sb-admin-2.css" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        <?php require_once('../include/navbar_admin.php') ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php require_once('../include/topbar_admin.php') ?>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Detail Berkas: "<?php echo htmlspecialchars($berkas['nomor_berkas']); ?>" "<?php echo htmlspecialchars($berkas['nomor_spp']); ?>"</h6>
                                </div>
                                <div class="card-body">
                                    <form method="post">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Nomor Berkas</label>
                                            <div class="col-sm-9">
                                                <input type="text" name="nomor_berkas" class="form-control" placeholder="Nomor Berkas"
                                                    value="<?php echo htmlspecialchars($berkas['nomor_berkas']); ?>" readonly>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Nomor SPP</label>
                                            <div class="col-sm-9">
                                                <input type="text" name="nomor_spp" class="form-control" placeholder="Nomor SPP"
                                                    value="<?php echo htmlspecialchars($berkas['nomor_spp']); ?>" readonly>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Keterangan</label>
                                            <div class="col-sm-9">
                                                <textarea name="keterangan" class="form-control" placeholder="Keterangan" readonly><?php echo htmlspecialchars($berkas['keterangan']); ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Unit Kerja</label>
                                            <div class="col-sm-9">
                                                <input type="text" name="unit_kerja" class="form-control" placeholder="Unit Kerja"
                                                    value="<?php echo htmlspecialchars($berkas['unit_kerja']); ?>" readonly>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">PPK</label>
                                            <div class="col-sm-9">
                                                <input type="text" name="ppk" class="form-control" placeholder="PPK"
                                                    value="<?php echo htmlspecialchars($berkas['ppk']); ?>" readonly>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Nama Pengolah</label>
                                            <div class="col-sm-9">
                                                <input type="text" name="nama_pengolah" class="form-control" placeholder="Nama Pengolah"
                                                    value="<?php echo htmlspecialchars($berkas['nama_pengolah']); ?>" readonly>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Nomor Telpon</label>
                                            <div class="col-sm-9">
                                                <input type="text" name="nomor_telpon" class="form-control" placeholder="Nomor Telpon"
                                                    value="<?php echo htmlspecialchars($berkas['nomor_telpon']); ?>" readonly>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Atas Nama</label>
                                            <div class="col-sm-9">
                                                <input type="text" name="atas_nama" class="form-control" placeholder="Atas Nama"
                                                    value="<?php echo htmlspecialchars($berkas['atas_nama']); ?>" readonly>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Jumlah Kuitansi</label>
                                            <div class="col-sm-9">
                                                <input type="number" name="jumlah_kuitansi" class="form-control" placeholder="Jumlah Kuitansi"
                                                    value="<?php echo htmlspecialchars($berkas['jumlah_kuitansi']); ?>" readonly>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Nilai Kuitansi</label>
                                            <div class="col-sm-9">
                                                <input type="text" name="nilai_kuitansi" class="form-control" placeholder="Nilai Kuitansi"
                                                    value="<?php echo 'Rp ' . number_format($berkas['nilai_kuitansi'], 0, ',', '.'); ?>" readonly>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Verifikator</label>
                                            <div class="col-sm-9">
                                                <input type="text" name="verifikator" class="form-control" placeholder="Verifikator"
                                                    value="<?php echo htmlspecialchars($berkas['verifikator']); ?>" readonly>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">History Keluar Berkas</label>
                                            <div class="col-sm-9">
                                                <input type="text" name="history_keluar" class="form-control" placeholder="History Keluar Berkas"
                                                    value="<?php echo htmlspecialchars($berkas['history_keluar'] ?? ''); ?>" readonly>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">History Masuk Berkas</label>
                                            <div class="col-sm-9">
                                                <input type="text" name="history_masuk" class="form-control" placeholder="History Masuk Berkas"
                                                    value="<?php echo htmlspecialchars($berkas['history_masuk'] ?? ''); ?>" readonly>
                                            </div>
                                        </div>

                                        <div class="d-flex mb-3 justify-content-end">
                                            <!-- Tombol Tambah -->
                                            <a type="button" onclick="window.history.back()" class="btn btn-primary mr-2" style="background-color: #EAECF4; color: #5A5C69; border: 1px solid #d1d3e2;">Kembali</a>

                                            <a href="edit_berkas.php?id=<?php echo $berkas['id']; ?>"
                                                class="btn btn-primary">
                                                Edit
                                            </a>
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

    <!-- Bootstrap core JavaScript-->
    <script src=" ../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="../js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="../vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="../js/demo/datatables-demo.js"></script>
</body>

</html>