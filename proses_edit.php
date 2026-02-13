<?php
include 'koneksi.php';

$id     = $_POST['id_barang'];
$nama   = $_POST['nama_barang'];
$stok   = $_POST['stok'];
$harga  = $_POST['harga'];

$gambar = $_FILES['gambar']['name'];

if ($gambar != "") {
	$x = explode('.', $gambar);
	$ekstensi = strtolower(end($x));
	$nama_gambar_baru = time() . '-' . $gambar;
	$file_tmp = $_FILES['gambar']['tmp_name'];

	move_uploaded_file($file_tmp, 'img/' . $nama_gambar_baru);

	// Update dengan gambar baru
	$query = "UPDATE barang SET nama_barang='$nama', stok='$stok', harga='$harga', gambar='$nama_gambar_baru' WHERE id_barang='$id'";
} else {
	// Update tanpa mengubah gambar
	$query = "UPDATE barang SET nama_barang='$nama', stok='$stok', harga='$harga' WHERE id_barang='$id'";
}

$result = mysqli_query($conn, $query);

if ($result) {
	echo "<script>alert('Data berhasil diupdate!'); window.location='admin.php';</script>";
} else {
	echo "Gagal: " . mysqli_error($conn);
}

if ($result) {
    // Berhasil
    header("location:admin.php?pesan=update_sukses");
} else {
    // Gagal karena database
    header("location:admin.php?pesan=error_db");
}