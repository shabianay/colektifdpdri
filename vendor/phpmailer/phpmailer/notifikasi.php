<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './vendor/autoload.php'; // Sesuaikan dengan lokasi autoload.php dari PHPMailer
require './include/koneksi.php'; // Koneksi ke database

// Konfigurasi SMTP
$mail = new PHPMailer(true);
$mail->isSMTP();
$mail->Host       = 'smtp.example.com'; // Masukkan alamat SMTP server Anda
$mail->SMTPAuth   = true;
$mail->Username   = 'email@example.com'; // Masukkan email SMTP Anda
$mail->Password   = 'password'; // Masukkan password email SMTP Anda
$mail->SMTPSecure = 'tls'; // Ganti menjadi ssl jika Anda menggunakan SSL
$mail->Port       = 587; // Ganti port SMTP jika diperlukan

// Pengaturan email
$mail->setFrom('email@example.com', 'Nama Pengirim');
$mail->addAddress('admin@example.com', 'Nama Admin'); // Alamat email admin penerima
$mail->Subject = 'Notifikasi Hasil Skrining Membutuhkan Penanganan';
$mail->Body    = 'Ada hasil skrining yang membutuhkan penanganan khusus. Silakan cek database untuk detailnya.';

// Query untuk mengambil hasil skrining yang membutuhkan penanganan
$query = "SELECT * FROM skrining WHERE hasil = 'membutuhkan_penanganan'";
$result = mysqli_query($koneksi, $query);

if ($result) {
    // Jika terdapat hasil skrining yang membutuhkan penanganan, kirim notifikasi email
    if (mysqli_num_rows($result) > 0) {
        if ($mail->send()) {
            echo 'Notifikasi email berhasil dikirim';
        } else {
            echo 'Notifikasi email gagal dikirim: ' . $mail->ErrorInfo;
        }
    } else {
        echo 'Tidak ada hasil skrining yang membutuhkan penanganan';
    }
} else {
    echo "Error: " . mysqli_error($koneksi);
}
