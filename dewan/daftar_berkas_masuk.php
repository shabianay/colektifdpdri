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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Colektif</title>

    <!-- Fonts & Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet" />

    <!-- Styles -->
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
    </style>
</head>

<body id="page-top">
    <div id="wrapper">
        <?php require_once('../include/navbar_admin.php') ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php require_once('../include/topbar_admin.php') ?>
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <h1 class="h3 mb-2 text-gray-800">Daftar Berkas Masuk</h1>
                    <!-- Notifikasi -->
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= $_SESSION['success']; ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= $_SESSION['error']; ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>
                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Data Daftar Berkas Masuk</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Tanggal Masuk</th>
                                            <th class="text-center">Nomor Berkas</th>
                                            <th class="text-center">Nomor SPP</th>
                                            <th class="text-center">Unit Kerja</th>
                                            <th class="text-center">Nama Pengolah</th>
                                            <th class="text-center">Keterangan</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT * FROM berkas_dewan ORDER BY created_at DESC";
                                        $result = mysqli_query($koneksi, $query);
                                        $counter = 0;

                                        // Daftar status dan aksi yang akan dirotasi setiap 4 data
                                        $statusPattern = array(
                                            array('status' => 'selesai', 'badge' => 'badge-success'),
                                            array('status' => 'revisi', 'badge' => 'badge-danger'),
                                            array('status' => 'dalam verifikasi', 'badge' => 'badge-warning'),
                                            array('status' => 'berkas kembali', 'badge' => 'badge-info')
                                        );

                                        while ($row = mysqli_fetch_assoc($result)) {
                                            // Tentukan status dan badge berdasarkan pola setiap 4 data
                                            $patternIndex = $counter % 4;
                                            $status = $statusPattern[$patternIndex]['status'];
                                            $badgeClass = $statusPattern[$patternIndex]['badge'];
                                            $statusBadge = "<span class='badge $badgeClass'>" . ucfirst($status) . "</span>";

                                            echo "<tr>";
                                            echo "<td>" . date('d F Y, H:i:s', strtotime($row['created_at'])) . "</td>";
                                            echo "<td>" . $row['nomor_berkas'] . "</td>";
                                            echo "<td>" . $row['nomor_spp'] . "</td>";
                                            echo "<td>" . $row['unit_kerja'] . "</td>";
                                            echo "<td>" . $row['nama_pengolah'] . "</td>";
                                            echo "<td>" . $row['keterangan'] . "</td>";
                                            echo "<td class='text-center'>" . $statusBadge . "</td>";

                                            // Tombol Aksi berdasarkan status pola
                                            echo "<td class='text-center'>";
                                            echo "<a href='edit_berkas.php?id=" . $row['id'] . "' class='btn btn-warning btn-sm btn-action'><i class='fa-solid fa-pen-to-square'></i></a> ";
                                            echo "<a href='hapus_berkas.php?id=" . $row['id'] . "' class='btn btn-danger btn-sm btn-action' onclick='return confirmDelete()'><i class='fa-solid fa-trash'></i></a> ";
                                            echo "<a href='detail_berkas.php?id=" . $row['id'] . "' class='btn btn-info btn-sm btn-action'><i class='fa-solid fa-eye'></i></a> ";

                                            // Tombol tambahan sesuai status pola (TANPA ID)
                                            if ($status == 'revisi') {
                                                echo "<a href='daftar_berkas_masuk.php' class='btn btn-success btn-sm btn-action'>Berkas Kembali</a>";
                                            } elseif ($status == 'dalam verifikasi') {
                                                echo "<a href='daftar_berkas_masuk.php' class='btn btn-primary btn-sm btn-action'>Revisi</a>";
                                            }
                                            echo "</td>";
                                            echo "</tr>";
                                            $counter++;
                                        }
                                        mysqli_free_result($result);
                                        mysqli_close($koneksi);
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

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>

    <!-- Scripts -->
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
                "searching": true
            });
        });
    </script>
</body>

</html>