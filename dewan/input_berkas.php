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

if ($_SESSION['role'] == 'user') {
    header("Location: ../setjen/setjen_dashboard.php");
    exit();
}

require_once "../include/koneksi.php";

// Proses input
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nomor_berkas   = $_POST["nomor_berkas"];
    $jenis_berkas   = $_POST["jenis_berkas"];
    $jenis_kontraktual = isset($_POST['jenis_kontraktual']) ? $_POST['jenis_kontraktual'] : NULL;
    $nomor_spp      = $_POST["nomor_spp"];
    $keterangan     = $_POST["keterangan"];
    $unit_kerja     = $_POST["unit_kerja"];
    $ppk            = $_POST["ppk"];
    $nama_pengolah  = $_POST["nama_pengolah"];
    $nomor_telpon   = $_POST["nomor_telpon"];
    $atas_nama      = $_POST["atas_nama"];
    $jumlah_kuitansi = $_POST["jumlah_kuitansi"];
    $nilai_kuitansi = $_POST["nilai_kuitansi"];
    $verifikator    = $_POST["verifikator"];

    $query = "INSERT INTO berkas 
        (nomor_berkas, jenis_berkas, jenis_kontraktual, nomor_spp, keterangan, unit_kerja, ppk, nama_pengolah, nomor_telpon, atas_nama, jumlah_kuitansi, nilai_kuitansi, verifikator)
        VALUES 
        ('$nomor_berkas', '$jenis_berkas', " . ($jenis_kontraktual ? "'$jenis_kontraktual'" : "NULL") . ", '$nomor_spp', '$keterangan', 
        '$unit_kerja', '$ppk', '$nama_pengolah', '$nomor_telpon', '$atas_nama', 
        '$jumlah_kuitansi', '$nilai_kuitansi', '$verifikator')";

    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Data berhasil ditambahkan'); window.location='input_berkas.php';</script>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
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
    <link href="../css/sb-admin-2.css" rel="stylesheet">
</head>

<body id="page-top">
    <!-- Page Wrapper -->
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
                                    <h6 class="m-0 font-weight-bold">Input Berkas</h6>
                                </div>
                                <div class="card-body">
                                    <form method="post">
                                        <div class="form-group">
                                            <label>Nomor Berkas</label>
                                            <input type="text" name="nomor_berkas" class="form-control" placeholder="Nomor Berkas" required>
                                        </div>

                                        <div class="form-group">
                                            <label>Jenis Berkas</label>
                                            <select name="jenis_berkas" id="jenisBerkas" class="form-control" required>
                                                <option value="">-- Pilih Jenis Berkas --</option>
                                                <option value="Pengajuan">Pengajuan</option>
                                                <option value="Pertanggungjawaban">Pertanggungjawaban</option>
                                            </select>
                                        </div>

                                        <!-- Bagian yang akan muncul kalau pilih Pengajuan -->
                                        <div id="pengajuanFields" style="display: none;">
                                            <div class="form-group">
                                                <label>Jenis Kontraktual</label>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="card border p-3">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="jenis_kontraktual" value="Kontraktual" id="kontraktual">
                                                                <label class="form-check-label" for="kontraktual">
                                                                    Kontraktual
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="card border p-3">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="jenis_kontraktual" value="Non-Kontraktual" id="nonKontraktual">
                                                                <label class="form-check-label" for="nonKontraktual">
                                                                    Non-Kontraktual
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Nomor SPP</label>
                                            <input type="text" name="nomor_spp" class="form-control" placeholder="Nomor SPP">
                                        </div>
                                        <div class="form-group">
                                            <label>Keterangan</label>
                                            <textarea name="keterangan" class="form-control" placeholder="Keterangan"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>Unit Kerja</label>
                                            <select name="unit_kerja" class="form-control">
                                                <option>Unit Kerja</option>
                                                <option>Deputi Bidang Administrasi</option>
                                                <option>Deputi Bidang Persidangan</option>
                                                <option>Pusperjakum</option>
                                                <option>Puskadaran</option>
                                                <option>Inspektorat</option>
                                                <option>Biro Persidangan I</option>
                                                <option>Komite I</option>
                                                <option>Komite III</option>
                                                <option>PPUU</option>
                                                <option>BULD</option>
                                                <option>BKSP</option>
                                                <option>Biro Persidangan II</option>
                                                <option>Komite II</option>
                                                <option>Komite IV</option>
                                                <option>Panmus</option>
                                                <option>Pansus</option>
                                                <option>Bagian Sekretariat BK</option>
                                                <option>Bagian Sekretariat PURT</option>
                                                <option>Bagian Sekretariat Badan Akuntabilitas Publik</option>
                                                <option>Biro Sekretariat Pimpinan</option>
                                                <option>Bagian Sekretariat Ketua</option>
                                                <option>Bagian Sekretariat Waka I</option>
                                                <option>Bagian Sekretariat Waka II</option>
                                                <option>Biro Perencanaan dan keuangan</option>
                                                <option>Bagian Perencanaan</option>
                                                <option>Bagian Administrasi, Gaji, Tunjangan dan Honorarium</option>
                                                <option>Bagian Perbendaharaan</option>
                                                <option>Bagian Akuntansi dan Pelaporan</option>
                                                <option>Biro Sistem Informasi dan Dokumentasi</option>
                                                <option>Bagian Pengelolaan Sistem Informasi</option>
                                                <option>Bagian Risalah</option>
                                                <option>Bagian Kearsipan, Perpustakaan, dan Penerbitan</option>
                                                <option>Bagian Pengelolaan Barang Milik Negara</option>
                                                <option>Biro Umum</option>
                                                <option>Bagian Pengelolaan Barang Milik Negara</option>
                                                <option>Bagian Pemeliharaan dan Perlengkapan</option>
                                                <option>Bagian Layanan dan Pengadaan</option>
                                                <option>Bagian Pengamanan Dalam</option>
                                                <option>Biro Protokol, Hubungan, Masyarakat, dan Media</option>
                                                <option>Bagian Protokol</option>
                                                <option>Bagian Hubungan Masyarakat dan Fasilitasi Pengaduan</option>
                                                <option>Bagian Pemberitaan dan Media</option>
                                                <option>Biro OKK</option>
                                                <option>Bagian OKK</option>
                                                <option>Bagian AKK</option>
                                                <option>Bagian PSDM</option>
                                                <option>Bagian Hukum</option>
                                                <option>ACEH</option>
                                                <option>Sumatera Utara</option>
                                                <option>Sumatera Barat</option>
                                                <option>Riau</option>
                                                <option>Jambi</option>
                                                <option>Sumatera Selatan</option>
                                                <option>Bengkulu</option>
                                                <option>Lampung</option>
                                                <option>Bangka Belitung</option>
                                                <option>Kepulauan Riau</option>
                                                <option>DKI JKT</option>
                                                <option>Jawa Barat</option>
                                                <option>Jawa Tengah</option>
                                                <option>D.I Yogyakarta</option>
                                                <option>Jawa Timur</option>
                                                <option>Banten</option>
                                                <option>Bali</option>
                                                <option>NTB</option>
                                                <option>NTT</option>
                                                <option>Kalimantan Barat</option>
                                                <option>Kalimantan Tengah</option>
                                                <option>Kalimantan Selatan</option>
                                                <option>Kalimantan Timur</option>
                                                <option>Kalimantan Utara</option>
                                                <option>Sulawesi Utara</option>
                                                <option>Sulawesi Tengah</option>
                                                <option>Sulawesi Selatan</option>
                                                <option>Sultra</option>
                                                <option>Gorontalo</option>
                                                <option>Sulawesi Barat</option>
                                                <option>Maluku</option>
                                                <option>Maluku Utara</option>
                                                <option>Papua</option>
                                                <option>Papua Barat</option>
                                                <option>Papua Selatan</option>
                                                <option>Papua Tengah</option>
                                                <option>Papua Pegunungan</option>
                                                <option>Papua Barat Daya</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>PPK</label>
                                            <select name="ppk" class="form-control">
                                                <option>PPK</option>
                                                <option>Kepala Biro Sisdok</option>
                                                <option>Inspektorat</option>
                                                <option>Kepala Biro PHM (Setjend)</option>
                                                <option>Kepala Biro PHM (Dewan)</option>
                                                <option>Kepala Biro Umum</option>
                                                <option>Kepala Biro Perencanaan dan Keuangan</option>
                                                <option>Kepala Biro OKK</option>
                                                <option>Kepala Biro Setpim</option>
                                                <option>Kepala Biro Rosid I</option>
                                                <option>Kepala Biro Rosid II</option>
                                                <option>Kepala Pusat Kajian Daerah</option>
                                                <option>Kepala Pusat Kajian Hukum</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Nama Pengolah</label>
                                            <input type="text" name="nama_pengolah" class="form-control" placeholder="Nama Pengolah">
                                        </div>
                                        <div class="form-group">
                                            <label>Nomor Telpon</label>
                                            <input type="text" name="nomor_telpon" class="form-control" placeholder="Nomor Telpon">
                                        </div>
                                        <div class="form-group">
                                            <label>Atas Nama</label>
                                            <input type="text" name="atas_nama" class="form-control" placeholder="Atas Nama">
                                        </div>
                                        <div class="form-group">
                                            <label>Jumlah Kuitansi</label>
                                            <input type="number" name="jumlah_kuitansi" class="form-control" placeholder="Jumlah Kuitansi">
                                        </div>
                                        <div class="form-group">
                                            <label>Nilai Kuitansi</label>
                                            <input type="number" name="nilai_kuitansi" class="form-control" placeholder="Nilai Kuitansi">
                                        </div>
                                        <div class="form-group">
                                            <label>Verifikator</label>
                                            <select name="verifikator" class="form-control">
                                                <option>Verifikator</option>
                                                <option>Anda</option>
                                                <option>Dinnie</option>
                                                <option>Fatma</option>
                                                <option>Firman</option>
                                                <option>Inne</option>
                                                <option>Junisha</option>
                                                <option>Santi</option>
                                                <option>Taufan</option>
                                            </select>
                                        </div>
                                        <div class="text-right">
                                            <button type="submit" class="btn btn-primary">Tambah Data</button>
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

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <script>
        document.getElementById("jenisBerkas").addEventListener("change", function() {
            let value = this.value;
            let pengajuanFields = document.getElementById("pengajuanFields");

            if (value === "Pengajuan") {
                pengajuanFields.style.display = "block";
            } else {
                pengajuanFields.style.display = "none";
                // reset value biar gak keikut saat bukan pengajuan
                document.querySelectorAll('input[name="jenis_kontraktual"]').forEach(el => el.checked = false);
            }
        });
    </script>

    <!-- Bootstrap core JavaScript-->
    <script src="../vendor/jquery/jquery.min.js"></script>
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