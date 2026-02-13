<?php
include 'koneksi.php';

$id = $_GET['id'];

// Ambil info gambar untuk dihapus dari folder
$data = mysqli_query($conn, "SELECT gambar FROM barang WHERE id_barang='$id'");
$d = mysqli_fetch_array($data);

// Hapus file gambar jika bukan placeholder
if ($d['gambar'] != 'no-image.jpg' && file_exists("img/" . $d['gambar'])) {
    unlink("img/" . $d['gambar']);
}

$query = mysqli_query($conn, "DELETE FROM barang WHERE id_barang='$id'");

if ($query) {
    header("location:admin.php?pesan=hapus_sukses");
} else {
    header("location:admin.php?pesan=error_db");
}
exit;