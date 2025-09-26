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

// Pastikan hanya admin yang bisa menghapus
if ($_SESSION['role'] == 'user') {
    header("Location: ../setjen/setjen_dashboard.php");
    exit();
}

require_once "../include/koneksi.php";

// Validasi ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "ID berkas tidak valid.";
    header("Location: daftar_berkas_masuk.php");
    exit();
}

$id = intval($_GET['id']);

// Verifikasi bahwa berkas ada sebelum dihapus
$check_query = "SELECT id FROM berkas WHERE id = $id";
$check_result = mysqli_query($koneksi, $check_query);

if (mysqli_num_rows($check_result) == 0) {
    $_SESSION['error'] = "Berkas tidak ditemukan.";
    header("Location: daftar_berkas_masuk.php");
    exit();
}

// Hapus data
$delete_query = "DELETE FROM berkas WHERE id = $id";
$delete_result = mysqli_query($koneksi, $delete_query);

if ($delete_result) {
    $_SESSION['success'] = "Berkas berhasil dihapus.";
} else {
    $_SESSION['error'] = "Gagal menghapus berkas: " . mysqli_error($koneksi);
}

mysqli_close($koneksi);
header("Location: daftar_berkas_masuk.php");
exit();
