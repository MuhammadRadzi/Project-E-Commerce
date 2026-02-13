<?php
include 'koneksi.php';

// Validasi ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("location:admin.php?pesan=error_db");
    exit;
}

$id = $_GET['id'];

// Ambil info gambar menggunakan prepared statement
$stmt = $conn->prepare("SELECT gambar FROM barang WHERE id_barang = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    
    // Hapus file gambar jika bukan placeholder
    if ($data['gambar'] != 'no-image.jpg' && file_exists("img/" . $data['gambar'])) {
        unlink("img/" . $data['gambar']);
    }
    
    // Hapus dari database menggunakan prepared statement
    $stmt_delete = $conn->prepare("DELETE FROM barang WHERE id_barang = ?");
    $stmt_delete->bind_param("i", $id);
    
    if ($stmt_delete->execute()) {
        header("location:admin.php?pesan=hapus_sukses");
    } else {
        header("location:admin.php?pesan=error_db");
    }
} else {
    // Data tidak ditemukan
    header("location:admin.php?pesan=error_db");
}

exit;
?>