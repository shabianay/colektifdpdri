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

// Proses update
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
    // Hapus format rupiah sebelum disimpan ke database
    $nilai_kuitansi  = preg_replace('/[^0-9]/', '', $_POST["nilai_kuitansi"]); // hapus Rp, titik, dll
    $verifikator    = $_POST["verifikator"];

    $query = "UPDATE berkas SET
        nomor_berkas = '$nomor_berkas',
        jenis_berkas = '$jenis_berkas',
        jenis_kontraktual = " . ($jenis_kontraktual ? "'$jenis_kontraktual'" : "NULL") . ",
        nomor_spp = '$nomor_spp',
        keterangan = '$keterangan',
        unit_kerja = '$unit_kerja',
        ppk = '$ppk',
        nama_pengolah = '$nama_pengolah',
        nomor_telpon = '$nomor_telpon',
        atas_nama = '$atas_nama',
        jumlah_kuitansi = '$jumlah_kuitansi',
        nilai_kuitansi = '$nilai_kuitansi',
        verifikator = '$verifikator'
        WHERE id = $berkas_id";

    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Data berhasil diupdate'); window.location='daftar_berkas_masuk.php';</script>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}

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
                                    <h6 class="m-0 font-weight-bold">Edit Berkas</h6>
                                </div>
                                <div class="card-body">
                                    <form method="post">
                                        <div class="form-group">
                                            <label>Nomor Berkas</label>
                                            <input type="text" name="nomor_berkas" class="form-control" placeholder="Nomor Berkas"
                                                value="<?php echo htmlspecialchars($berkas['nomor_berkas']); ?>" required>
                                        </div>

                                        <div class="form-group">
                                            <label>Jenis Berkas</label>
                                            <select name="jenis_berkas" id="jenisBerkas" class="form-control" required>
                                                <option value="">-- Pilih Jenis Berkas --</option>
                                                <option value="Pengajuan" <?php if ($berkas['jenis_berkas'] == 'Pengajuan') echo 'selected'; ?>>Pengajuan</option>
                                                <option value="Pertanggungjawaban" <?php if ($berkas['jenis_berkas'] == 'Pertanggungjawaban') echo 'selected'; ?>>Pertanggungjawaban</option>
                                            </select>
                                        </div>

                                        <div id="pengajuanFields" style="display: <?php echo $berkas['jenis_berkas'] == 'Pengajuan' ? 'block' : 'none'; ?>;">
                                            <div class="form-group">
                                                <label>Jenis Kontraktual</label>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="card border p-3">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="jenis_kontraktual" value="Kontraktual"
                                                                    <?php if ($berkas['jenis_kontraktual'] == 'Kontraktual') echo 'checked'; ?> id="kontraktual">
                                                                <label class="form-check-label" for="kontraktual">Kontraktual</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="card border p-3">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="jenis_kontraktual" value="Non-Kontraktual"
                                                                    <?php if ($berkas['jenis_kontraktual'] == 'Non-Kontraktual') echo 'checked'; ?> id="nonKontraktual">
                                                                <label class="form-check-label" for="nonKontraktual">Non-Kontraktual</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Nomor SPP</label>
                                            <input type="text" name="nomor_spp" class="form-control" placeholder="Nomor SPP"
                                                value="<?php echo htmlspecialchars($berkas['nomor_spp']); ?>">
                                        </div>

                                        <div class="form-group">
                                            <label>Keterangan</label>
                                            <textarea name="keterangan" class="form-control" placeholder="Keterangan"><?php echo htmlspecialchars($berkas['keterangan']); ?></textarea>
                                        </div>

                                        <div class="form-group">
                                            <label>Unit Kerja</label>
                                            <select name="unit_kerja" id="unitKerja" class="form-control">
                                                <option value="">Unit Kerja</option>
                                                <?php
                                                $units = [
                                                    "Deputi Bidang Administrasi",
                                                    "Deputi Bidang Persidangan",
                                                    "Pusperjakum",
                                                    "Puskadaran",
                                                    "Inspektorat",
                                                    "Biro Persidangan I",
                                                    "Komite I",
                                                    "Komite III",
                                                    "PPUU",
                                                    "BULD",
                                                    "BKSP",
                                                    "Biro Persidangan II",
                                                    "Komite II",
                                                    "Komite IV",
                                                    "Panmus",
                                                    "Pansus",
                                                    "Bagian Sekretariat BK",
                                                    "Bagian Sekretariat PURT",
                                                    "Bagian Sekretariat Badan Akuntabilitas Publik",
                                                    "Biro Sekretariat Pimpinan",
                                                    "Bagian Sekretariat Ketua",
                                                    "Bagian Sekretariat Waka I",
                                                    "Bagian Sekretariat Waka II",
                                                    "Biro Perencanaan dan keuangan",
                                                    "Bagian Perencanaan",
                                                    "Bagian Administrasi, Gaji, Tunjangan dan Honorarium",
                                                    "Bagian Perbendaharaan",
                                                    "Bagian Akuntansi dan Pelaporan",
                                                    "Biro Sistem Informasi dan Dokumentasi",
                                                    "Bagian Pengelolaan Sistem Informasi",
                                                    "Bagian Risalah",
                                                    "Bagian Kearsipan, Perpustakaan, dan Penerbitan",
                                                    "Bagian Pengelolaan Barang Milik Negara",
                                                    "Biro Umum",
                                                    "Bagian Pengelolaan Barang Milik Negara",
                                                    "Bagian Pemeliharaan dan Perlengkapan",
                                                    "Bagian Layanan dan Pengadaan",
                                                    "Bagian Pengamanan Dalam",
                                                    "Biro Protokol, Hubungan, Masyarakat, dan Media",
                                                    "Bagian Protokol",
                                                    "Bagian Hubungan Masyarakat dan Fasilitasi Pengaduan",
                                                    "Bagian Pemberitaan dan Media",
                                                    "Biro OKK",
                                                    "Bagian OKK",
                                                    "Bagian AKK",
                                                    "Bagian PSDM",
                                                    "Bagian Hukum",
                                                    "ACEH",
                                                    "Sumatera Utara",
                                                    "Sumatera Barat",
                                                    "Riau",
                                                    "Jambi",
                                                    "Sumatera Selatan",
                                                    "Bengkulu",
                                                    "Lampung",
                                                    "Bangka Belitung",
                                                    "Kepulauan Riau",
                                                    "DKI JKT",
                                                    "Jawa Barat",
                                                    "Jawa Tengah",
                                                    "D.I Yogyakarta",
                                                    "Jawa Timur",
                                                    "Banten",
                                                    "Bali",
                                                    "NTB",
                                                    "NTT",
                                                    "Kalimantan Barat",
                                                    "Kalimantan Tengah",
                                                    "Kalimantan Selatan",
                                                    "Kalimantan Timur",
                                                    "Kalimantan Utara",
                                                    "Sulawesi Utara",
                                                    "Sulawesi Tengah",
                                                    "Sulawesi Selatan",
                                                    "Sultra",
                                                    "Gorontalo",
                                                    "Sulawesi Barat",
                                                    "Maluku",
                                                    "Maluku Utara",
                                                    "Papua",
                                                    "Papua Barat",
                                                    "Papua Selatan",
                                                    "Papua Tengah",
                                                    "Papua Pegunungan",
                                                    "Papua Barat Daya"
                                                ];

                                                foreach ($units as $unit) {
                                                    $selected = ($berkas['unit_kerja'] == $unit) ? 'selected' : '';
                                                    echo "<option value=\"$unit\" $selected>$unit</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>PPK</label>
                                            <select name="ppk" class="form-control">
                                                <option value="">PPK</option>
                                                <?php
                                                $ppks = [
                                                    "Kepala Biro Sisdok",
                                                    "Inspektorat",
                                                    "Kepala Biro PHM (Setjend)",
                                                    "Kepala Biro PHM (Dewan)",
                                                    "Kepala Biro Umum",
                                                    "Kepala Biro Perencanaan dan Keuangan",
                                                    "Kepala Biro OKK",
                                                    "Kepala Biro Setpim",
                                                    "Kepala Biro Rosid I",
                                                    "Kepala Biro Rosid II",
                                                    "Kepala Pusat Kajian Daerah",
                                                    "Kepala Pusat Kajian Hukum"
                                                ];

                                                foreach ($ppks as $ppk) {
                                                    $selected = ($berkas['ppk'] == $ppk) ? 'selected' : '';
                                                    echo "<option value=\"$ppk\" $selected>$ppk</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Nama Pengolah</label>
                                            <input type="text" name="nama_pengolah" class="form-control" placeholder="Nama Pengolah"
                                                value="<?php echo htmlspecialchars($berkas['nama_pengolah']); ?>">
                                        </div>

                                        <div class="form-group">
                                            <label>Nomor Telpon</label>
                                            <input type="text" name="nomor_telpon" class="form-control" placeholder="Nomor Telpon"
                                                value="<?php echo htmlspecialchars($berkas['nomor_telpon']); ?>">
                                        </div>

                                        <div class="form-group">
                                            <label>Atas Nama</label>
                                            <input type="text" name="atas_nama" class="form-control" placeholder="Atas Nama"
                                                value="<?php echo htmlspecialchars($berkas['atas_nama']); ?>">
                                        </div>

                                        <div class="form-group">
                                            <label>Jumlah Kuitansi</label>
                                            <input type="number" name="jumlah_kuitansi" class="form-control" placeholder="Jumlah Kuitansi"
                                                value="<?php echo htmlspecialchars($berkas['jumlah_kuitansi']); ?>">
                                        </div>

                                        <div class="form-group">
                                            <label>Nilai Kuitansi</label>
                                            <input type="text" name="nilai_kuitansi" class="form-control" placeholder="Nilai Kuitansi"
                                                value="<?php echo 'Rp ' . number_format($berkas['nilai_kuitansi'], 0, ',', '.'); ?>">
                                        </div>

                                        <div class="form-group">
                                            <label>Verifikator</label>
                                            <select name="verifikator" class="form-control">
                                                <option value="">Verifikator</option>
                                                <?php
                                                $verifikators = ["Anda", "Dinnie", "Fatma", "Firman", "Inne", "Junisha", "Santi", "Taufan"];

                                                foreach ($verifikators as $v) {
                                                    $selected = $berkas['verifikator'] == $v ? 'selected' : '';
                                                    echo "<option value='$v' $selected>$v</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="text-right">
                                            <button type="submit" class="btn btn-primary">Perbarui Data</button>
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

    <!-- Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script>
        document.getElementById("jenisBerkas").addEventListener("change", function() {
            let value = this.value;
            let pengajuanFields = document.getElementById("pengajuanFields");

            if (value === "Pengajuan") {
                pengajuanFields.style.display = "block";
            } else {
                pengajuanFields.style.display = "none";
                document.querySelectorAll('input[name="jenis_kontraktual"]').forEach(el => el.checked = false);
            }
        });

        // Format Rupiah untuk Nilai Kuitansi
        const nilaiKuitansi = document.getElementById('nilaiKuitansi');

        function formatRupiah(angka) {
            let number_string = angka.replace(/[^,\d]/g, '').toString();
            let split = number_string.split(',');
            let sisa = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return rupiah ? 'Rp ' + rupiah : '';
        }

        nilaiKuitansi.addEventListener('keyup', function(e) {
            nilaiKuitansi.value = formatRupiah(this.value);
        });

        // Inisialisasi Select2 untuk Unit Kerja
        $(document).ready(function() {
            $('#unitKerja').select2({
                placeholder: "-- Pilih atau Cari Unit Kerja --",
                allowClear: true
            });
        });
    </script>
</body>

</html>