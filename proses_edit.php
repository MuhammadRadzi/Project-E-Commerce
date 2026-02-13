<?php
include 'koneksi.php';

// Validasi input
if (!isset($_POST['id_barang']) || !is_numeric($_POST['id_barang'])) {
    header("location:admin.php?pesan=error_db");
    exit;
}

$id     = $_POST['id_barang'];
$nama   = input($_POST['nama_barang']);
$stok   = input($_POST['stok']);
$harga  = input($_POST['harga']);

$gambar = $_FILES['gambar']['name'];

if ($gambar != "") {
    // Validasi file upload
    $ekstensi_diperbolehkan = array('png', 'jpg', 'jpeg');
    $x = explode('.', $gambar);
    $ekstensi = strtolower(end($x));
    $nama_gambar_baru = time() . '-' . $gambar;
    $file_tmp = $_FILES['gambar']['tmp_name'];
    
    if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
        // Hapus gambar lama jika ada
        $query_old = mysqli_query($conn, "SELECT gambar FROM barang WHERE id_barang='$id'");
        $old_data = mysqli_fetch_assoc($query_old);
        if ($old_data['gambar'] != 'no-image.jpg' && file_exists("img/" . $old_data['gambar'])) {
            unlink("img/" . $old_data['gambar']);
        }
        
        move_uploaded_file($file_tmp, 'img/' . $nama_gambar_baru);
        
        // Update dengan gambar baru menggunakan prepared statement
        $stmt = $conn->prepare("UPDATE barang SET nama_barang=?, stok=?, harga=?, gambar=? WHERE id_barang=?");
        $stmt->bind_param("siisi", $nama, $stok, $harga, $nama_gambar_baru, $id);
    } else {
        header("location:admin.php?pesan=error_db");
        exit;
    }
} else {
    // Update tanpa mengubah gambar
    $stmt = $conn->prepare("UPDATE barang SET nama_barang=?, stok=?, harga=? WHERE id_barang=?");
    $stmt->bind_param("siii", $nama, $stok, $harga, $id);
}

if ($stmt->execute()) {
    header("location:admin.php?pesan=update_sukses");
} else {
    header("location:admin.php?pesan=error_db");
}

exit;
?>