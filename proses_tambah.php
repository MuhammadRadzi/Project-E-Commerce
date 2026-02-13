<?php
include 'koneksi.php';

// Validasi semua input required ada
if (!isset($_POST['nama_barang'], $_POST['jenis_barang'], $_POST['stok'], $_POST['harga'], $_POST['kondisi'])) {
	header("location:admin.php?pesan=error_db");
	exit;
}

// Menggunakan fungsi input() untuk keamanan 
$nama       = input($_POST['nama_barang']);
$jenis      = input($_POST['jenis_barang']);
$stok       = input($_POST['stok']);
$harga      = input($_POST['harga']);
$kondisi    = input($_POST['kondisi']);
$lokasi_rak = isset($_POST['lokasi_rak']) ? input($_POST['lokasi_rak']) : '';

// Logika Upload Gambar
$gambar = $_FILES['gambar']['name'];
$nama_gambar_final = 'no-image.jpg'; // Default

if ($gambar != "") {
	$ekstensi_diperbolehkan = array('png', 'jpg', 'jpeg');
	$x = explode('.', $gambar);
	$ekstensi = strtolower(end($x));
	$file_tmp = $_FILES['gambar']['tmp_name'];
	$ukuran_file = $_FILES['gambar']['size'];

	// Validasi ekstensi
	if (in_array($ekstensi, $ekstensi_diperbolehkan) === false) {
		header("location:tambah.php?pesan=ekstensi_salah");
		exit;
	}

	// Validasi ukuran (max 2MB)
	if ($ukuran_file > 2000000) {
		header("location:tambah.php?pesan=ukuran_besar");
		exit;
	}

	$nama_gambar_baru = time() . '-' . basename($gambar);

	if (move_uploaded_file($file_tmp, 'img/' . $nama_gambar_baru)) {
		$nama_gambar_final = $nama_gambar_baru;
	}
}

// Gunakan prepared statement untuk INSERT
$stmt = $conn->prepare("INSERT INTO barang (nama_barang, jenis_barang, stok, harga, kondisi, lokasi_rak, gambar) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssiisss", $nama, $jenis, $stok, $harga, $kondisi, $lokasi_rak, $nama_gambar_final);

if ($stmt->execute()) {
	header("location:admin.php?pesan=tambah_sukses");
} else {
	// Jika gagal dan sudah upload gambar, hapus gambar yang diupload
	if ($nama_gambar_final != 'no-image.jpg' && file_exists("img/" . $nama_gambar_final)) {
		unlink("img/" . $nama_gambar_final);
	}
	header("location:admin.php?pesan=error_db");
}

exit;
