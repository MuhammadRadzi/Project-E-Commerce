<?php
include 'koneksi.php';

// Menggunakan fungsi input() untuk keamanan 
$nama    = input($_POST['nama_barang']);
$jenis   = input($_POST['jenis_barang']);
$stok    = input($_POST['stok']);
$harga   = input($_POST['harga']);
$kondisi = input($_POST['kondisi']);

// Logika Upload Gambar
$gambar = $_FILES['gambar']['name'];
if ($gambar != "") {
	$ekstensi_diperbolehkan = array('png', 'jpg', 'jpeg');
	$x = explode('.', $gambar);
	$ekstensi = strtolower(end($x));
	$file_tmp = $_FILES['gambar']['tmp_name'];
	$nama_gambar_baru = time() . '-' . $gambar; // Agar nama file unik

	if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
		move_uploaded_file($file_tmp, 'img/' . $nama_gambar_baru);
		$query = "INSERT INTO barang (nama_barang, jenis_barang, stok, harga, kondisi, gambar) 
                VALUES ('$nama', '$jenis', '$stok', '$harga', '$kondisi', '$nama_gambar_baru')";
	}
} else {
	// Jika tidak ada gambar yang diupload
	$query = "INSERT INTO barang (nama_barang, jenis_barang, stok, harga, kondisi, gambar) 
            VALUES ('$nama', '$jenis', '$stok', '$harga', '$kondisi', 'no-image.jpg')";
}

$ukuran_file = $_FILES['gambar']['size'];

// if ($ukuran_file > 2000000) { // Jika lebih dari 2MB
//     header("location:tambah.php?pesan=ukuran_besar");
//     exit;
// }

$result = mysqli_query($conn, $query);

if ($result) {
	echo "<script>alert('Data berhasil ditambah!'); window.location='admin.php';</script>";
} else {
	echo "Gagal: " . mysqli_error($conn);
}

if ($result) {
    // Berhasil
    header("location:admin.php?pesan=tambah_sukses");
} else {
    // Gagal karena database
    header("location:admin.php?pesan=error_db");
}

