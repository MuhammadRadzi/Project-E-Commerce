<?php
include 'koneksi.php';

$id = $_GET['id'];

// Ambil info gambar untuk dihapus dari folder
$data = mysqli_query($conn, "SELECT gambar FROM barang WHERE id_barang='$id'");
$d = mysqli_fetch_array($data);

if ($d['gambar'] != 'no-image.jpg') {
	unlink("img/" . $d['gambar']); // Menghapus file di folder img
}

$query = mysqli_query($conn, "DELETE FROM barang WHERE id_barang='$id'");

if ($query) {
	echo "<script>alert('Data berhasil dihapus!'); window.location='admin.php';</script>";
} else {
	echo "Gagal menghapus.";
}

if ($result) {
	echo "<script>alert('Data berhasil dihapus!'); window.location='admin.php';</script>";
} else {
	echo "Gagal: " . mysqli_error($conn);
}

if ($result) {
    // Berhasil
    header("location:admin.php?pesan=error_db");
	} else {
    // Gagal karena database
    header("location:admin.php?pesan=hapus_sukses");
}