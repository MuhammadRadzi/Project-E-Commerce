<?php
// Mengaktifkan session php
session_start();

// Menghubungkan dengan koneksi
include 'koneksi.php';

// Menangkap data yang dikirim dari form
$username = $_POST['username'];
$password = $_POST['password'];

// Mencegah SQL Injection dengan Prepared Statement
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
$stmt->bind_param("ss", $username, $password);
$stmt->execute();
$result = $stmt->get_result();

// Cek apakah data ditemukan
if ($result->num_rows > 0) {
	$data = $result->fetch_assoc();

	// Buat Session
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
	// Jika gagal, kembali ke login dengan pesan error
	header("location:login.php?pesan=gagal");
}

