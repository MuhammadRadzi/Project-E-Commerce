<?php
// Mengaktifkan session php
session_start();

// Menghubungkan dengan koneksi
include 'koneksi.php';

// Menangkap data yang dikirim dari form
$username = $_POST['username'];
$password = $_POST['password'];

// Mencegah SQL Injection dengan Prepared Statement
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// Cek apakah username ditemukan
if ($result->num_rows > 0) {
	$data = $result->fetch_assoc();
	
	// Verifikasi password dengan password_verify() untuk hash
	if (password_verify($password, $data['password'])) {
		// Password cocok - Buat Session
		$_SESSION['username'] = $username;
		$_SESSION['status'] = "login";
		$_SESSION['role'] = $data['role'];

		// Cek Role (Admin dan User)
		if ($data['role'] == "admin") {
			// Jika Admin, ke dashboard admin
			header("location:admin.php");
		} else {
			// Jika User biasa, ke halaman katalog biasa
			header("location:index.php");
		}
	} else {
		// Password salah
		header("location:login.php?pesan=gagal");
	}
} else {
	// Username tidak ditemukan
	header("location:login.php?pesan=gagal");
}
?>