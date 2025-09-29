<?php
session_start();

// Session timeout (30 menit)
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

if ($_SESSION['role'] == 'user') {
    header("Location: ../setjen/setjen_dashboard.php");
    exit();
}

require_once "../include/koneksi.php";

// Ambil informasi user login
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($koneksi, $query);
if (!$result) {
    die("Query error: " . mysqli_error($koneksi));
}
$user = mysqli_fetch_assoc($result);

// Handle search functionality
$where_conditions = array();

if (!empty($_GET['nomor_berkas'])) {
    $nomor_berkas = mysqli_real_escape_string($koneksi, $_GET['nomor_berkas']);
    $where_conditions[] = "nomor_berkas LIKE '%$nomor_berkas%'";
}
if (!empty($_GET['nomor_spp'])) {
    $nomor_spp = mysqli_real_escape_string($koneksi, $_GET['nomor_spp']);
    $where_conditions[] = "nomor_spp LIKE '%$nomor_spp%'";
}
if (!empty($_GET['nama_pengolah'])) {
    $nama_pengolah = mysqli_real_escape_string($koneksi, $_GET['nama_pengolah']);
    $where_conditions[] = "nama_pengolah LIKE '%$nama_pengolah%'";
}
if (!empty($_GET['verifikator'])) {
    $verifikator = mysqli_real_escape_string($koneksi, $_GET['verifikator']);
    $where_conditions[] = "verifikator = '$verifikator'";
}
if (!empty($_GET['unit_kerja'])) {
    $unit_kerja = mysqli_real_escape_string($koneksi, $_GET['unit_kerja']);
    $where_conditions[] = "unit_kerja = '$unit_kerja'";
}
if (!empty($_GET['tanggal_dari'])) {
    $tanggal_dari = mysqli_real_escape_string($koneksi, $_GET['tanggal_dari']);
    $where_conditions[] = "DATE(created_at) >= '$tanggal_dari'";
}
if (!empty($_GET['tanggal_sampai'])) {
    $tanggal_sampai = mysqli_real_escape_string($koneksi, $_GET['tanggal_sampai']);
    $where_conditions[] = "DATE(created_at) <= '$tanggal_sampai'";
}
if (!empty($_GET['keterangan'])) {
    $keterangan = mysqli_real_escape_string($koneksi, $_GET['keterangan']);
    $where_conditions[] = "keterangan LIKE '%$keterangan%'";
}

// Tambahkan filter Jenis Berkas Lengkap
if (!empty($_GET['jenis_berkas_lengkap'])) {
    $jenis_lengkap = $_GET['jenis_berkas_lengkap'];
    switch ($jenis_lengkap) {
        case 'Pengajuan_LS':
            $where_conditions[] = "jenis_berkas='Pengajuan' AND jenis_kontraktual='LS Bendahara'";
            break;
        case 'Pengajuan_Kontraktual':
            $where_conditions[] = "jenis_berkas='Pengajuan' AND jenis_kontraktual='Kontraktual'";
            break;
        case 'Pengajuan_NonKontraktual':
            $where_conditions[] = "jenis_berkas='Pengajuan' AND jenis_kontraktual='Non-Kontraktual'";
            break;
        case 'Pertanggungjawaban':
            $where_conditions[] = "jenis_berkas='Pertanggungjawaban'";
            break;
    }
}

$where_clause = "";
if (!empty($where_conditions)) {
    $where_clause = "WHERE " . implode(" AND ", $where_conditions);
}

$query = "SELECT * FROM berkas_dewan $where_clause ORDER BY created_at DESC";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Colektif</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet" />
    <link href="../css/sb-admin-2.css" rel="stylesheet">
    <link href="../vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <style>
        .badge {
            padding: 8px 12px;
            border-radius: 4px;
            display: inline-block;
            font-weight: 600;
            min-width: 140px;
        }

        .badge-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn-action {
            margin: 2px;
            border-radius: 4px;
            display: inline-block;
            font-weight: 600;
            min-width: 100px;
        }

        .card-title {
            color: #5a5c69;
            font-weight: bold;
            margin-bottom: 1.5rem;
        }

        .search-form .form-group {
            margin-bottom: 1rem;
        }

        .search-form label {
            font-weight: 600;
            color: #5a5c69;
            margin-bottom: 0.5rem;
        }
    </style>
</head>

<body id="page-top">
    <div id="wrapper">
        <?php require_once('../include/navbar_admin.php') ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php require_once('../include/topbar_admin.php') ?>
                <div class="container-fluid">
                    <h1 class="h3 mb-4 text-gray-800">Cari Berkas Masuk</h1>

                    <!-- Search Form -->
                    <div class="card shadow mb-4">
                        <div class="card-body search-form">
                            <form method="GET" action="cari_berkas.php">
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label for="nomor_berkas">Nomor Berkas</label>
                                        <input placeholder="Masukkan Nomor Berkas" type="text" class="form-control" id="nomor_berkas" name="nomor_berkas"
                                            value="<?php echo htmlspecialchars($_GET['nomor_berkas'] ?? ''); ?>">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="nomor_spp">Nomor SPP</label>
                                        <input placeholder="Masukkan Nomor SPP" type="text" class="form-control" id="nomor_spp" name="nomor_spp"
                                            value="<?php echo htmlspecialchars($_GET['nomor_spp'] ?? ''); ?>">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="nama_pengolah">Nama Pengolah</label>
                                        <input placeholder="Masukkan Nama Pengolah" type="text" class="form-control" id="nama_pengolah" name="nama_pengolah"
                                            value="<?php echo htmlspecialchars($_GET['nama_pengolah'] ?? ''); ?>">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="verifikator">Verifikator</label>
                                        <select class="form-control" id="verifikator" name="verifikator">
                                            <option value="">Pilih Verifikator</option>
                                            <?php
                                            $verifikator_list = ['Anda', 'Dinnie', 'Fatma', 'Firman', 'Inne', 'Junisha', 'Santi', 'Taufan'];
                                            foreach ($verifikator_list as $v) {
                                                $sel = (($_GET['verifikator'] ?? '') == $v) ? 'selected' : '';
                                                echo "<option value='$v' $sel>$v</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label for="unit_kerja">Unit Kerja</label>
                                        <select class="form-control" id="unit_kerja" name="unit_kerja">
                                            <option value="">Pilih Unit Kerja</option>
                                            <?php
                                            $unit_kerja_list = [
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

                                            foreach ($unit_kerja_list as $u) {
                                                $sel = (($_GET['unit_kerja'] ?? '') == $u) ? 'selected' : '';
                                                echo "<option value=\"$u\" $sel>$u</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="tanggal_dari">Tanggal Masuk (Dari)</label>
                                        <input type="date" class="form-control" id="tanggal_dari" name="tanggal_dari"
                                            value="<?php echo htmlspecialchars($_GET['tanggal_dari'] ?? ''); ?>">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="tanggal_sampai">Tanggal Masuk (Sampai)</label>
                                        <input type="date" class="form-control" id="tanggal_sampai" name="tanggal_sampai"
                                            value="<?php echo htmlspecialchars($_GET['tanggal_sampai'] ?? ''); ?>">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="keterangan">Keterangan</label>
                                        <input placeholder="Masukkan Keterangan" type="text" class="form-control" id="keterangan" name="keterangan"
                                            value="<?php echo htmlspecialchars($_GET['keterangan'] ?? ''); ?>">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="jenis_berkas_lengkap">Jenis Berkas Lengkap</label>
                                        <select class="form-control" id="jenis_berkas_lengkap" name="jenis_berkas_lengkap">
                                            <option value="">Semua</option>
                                            <option value="Pengajuan_LS" <?php echo (($_GET['jenis_berkas_lengkap'] ?? '') == 'Pengajuan_LS') ? 'selected' : ''; ?>>Pengajuan : LS Bendahara</option>
                                            <option value="Pengajuan_Kontraktual" <?php echo (($_GET['jenis_berkas_lengkap'] ?? '') == 'Pengajuan_Kontraktual') ? 'selected' : ''; ?>>Pengajuan : Kontraktual</option>
                                            <option value="Pengajuan_NonKontraktual" <?php echo (($_GET['jenis_berkas_lengkap'] ?? '') == 'Pengajuan_NonKontraktual') ? 'selected' : ''; ?>>Pengajuan : Non Kontraktual</option>
                                            <option value="Pertanggungjawaban" <?php echo (($_GET['jenis_berkas_lengkap'] ?? '') == 'Pertanggungjawaban') ? 'selected' : ''; ?>>Pertanggungjawaban</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-secondary mr-2" onclick="window.location.href='cari_berkas.php'">Reset</button>
                                    <button type="submit" class="btn btn-primary">Cari</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Results Table -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Hasil Data Pencarian</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Nomor Berkas</th>
                                            <th class="text-center">Nomor SPP</th>
                                            <th class="text-center">Nama Pengolah</th>
                                            <th class="text-center">Verifikator</th>
                                            <th class="text-center">Unit Kerja</th>
                                            <th class="text-center">Tanggal Masuk</th>
                                            <th class="text-center">Keterangan</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $counter = 0;
                                        $statusPattern = array(
                                            array('status' => 'Selesai', 'badge' => 'badge-success'),
                                            array('status' => 'Revisi', 'badge' => 'badge-danger'),
                                            array('status' => 'Dalam Verifikasi', 'badge' => 'badge-warning'),
                                            array('status' => 'Berkas Kembali', 'badge' => 'badge-info')
                                        );

                                        if (mysqli_num_rows($result) > 0) {
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                $patternIndex = $counter % 4;
                                                $status = $statusPattern[$patternIndex]['status'];
                                                $badgeClass = $statusPattern[$patternIndex]['badge'];

                                                echo "<tr>";
                                                echo "<td>" . htmlspecialchars($row['nomor_berkas']) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['nomor_spp']) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['nama_pengolah']) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['verifikator']) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['unit_kerja']) . "</td>";
                                                echo "<td>" . date('d F Y, H:i:s', strtotime($row['created_at'])) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['keterangan']) . "</td>";
                                                echo "<td class='text-center'><span class='badge $badgeClass'>$status</span></td>";

                                                echo "<td class='text-center'>";
                                                echo "<a href='edit_berkas.php?id=" . $row['id'] . "' class='btn btn-warning btn-sm btn-action' title='Edit'><i class='fa-solid fa-pen-to-square'></i></a> ";
                                                echo "<a href='hapus_berkas.php?id=" . $row['id'] . "' class='btn btn-danger btn-sm btn-action' onclick='return confirmDelete()' title='Hapus'><i class='fa-solid fa-trash'></i></a> ";
                                                echo "<a href='detail_berkas.php?id=" . $row['id'] . "' class='btn btn-info btn-sm btn-action' title='Detail'><i class='fa-solid fa-eye'></i></a>";
                                                if ($status == 'Revisi') echo " <a href='#' class='btn btn-success btn-sm btn-action'>Berkas Kembali</a>";
                                                elseif ($status == 'Dalam Verifikasi') echo " <a href='#' class='btn btn-primary btn-sm btn-action'>Revisi</a>";
                                                echo "</td>";

                                                echo "</tr>";
                                                $counter++;
                                            }
                                        } else {
                                            echo "<tr><td colspan='9' class='text-center'>Tidak ada data yang ditemukan</td></tr>";
                                        }

                                        mysqli_free_result($result);
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>

    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="../vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <script src="../js/sb-admin-2.min.js"></script>
    <script>
        function confirmDelete() {
            return confirm("Apakah Anda yakin ingin menghapus berkas ini? Tindakan ini tidak dapat dibatalkan.");
        }
        $(document).ready(function() {
            $('#dataTable').DataTable({
                "pageLength": 10,
                "ordering": true,
                "searching": true,
                "language": {
                    "lengthMenu": "Show _MENU_ entries",
                    "zeroRecords": "Tidak ada data yang ditemukan",
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "infoEmpty": "Showing 0 to 0 of 0 entries",
                    "infoFiltered": "(filtered from _MAX_ total entries)",
                    "paginate": {
                        "first": "First",
                        "last": "Last",
                        "next": "Next",
                        "previous": "Previous"
                    }
                }
            });
        });
    </script>
</body>

</html>