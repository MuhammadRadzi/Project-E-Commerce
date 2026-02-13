<?php
session_start();
include 'koneksi.php';

if ($_SESSION['role'] != "admin") {
	header("location:login.php");
}

$id = $_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM barang WHERE id_barang = '$id'");
$d = mysqli_fetch_array($query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Toko Komputer Online - Sedia berbagai macam perangkat hardware berkualitas.">
    <meta name="keywords" content="komputer, laptop, hardware, e-commerce">
	<title>Edit Barang - Admin</title>
	<link rel="stylesheet" href="style.css">
</head>

<body>
	<nav>
		<h1>Admin Panel - Edit</h1>
		<ul>
			<li><a href="admin.php">Kembali</a></li>
		</ul>
	</nav>

	<div class="container" style="max-width: 600px; margin-top: 2rem;">
		<div class="card" style="padding: 2rem;">
			<form action="proses_edit.php" method="post" enctype="multipart/form-data">
				<input type="hidden" name="id_barang" value="<?php echo $d['id_barang']; ?>">

				<div style="margin-bottom: 1rem;">
					<label>Nama Barang</label>
					<input type="text" name="nama_barang" value="<?php echo $d['nama_barang']; ?>" required pattern="[A-Za-z\s]{3,}" style="width: 100%; padding: 0.5rem;">
				</div>

				<div style="margin-bottom: 1rem;">
					<label>Stok</label>
					<input type="number" name="stok" min="0" title="Stok tidak boleh negatif" placeholder="Masukkan jumlah stok..." value="<?php echo $d['stok']; ?>" required style="width: 100%; padding: 0.5rem;">
				</div>

				<div style="margin-bottom: 1rem;">
					<label>Harga</label>
					<input type="number" name="harga" value="<?php echo $d['harga']; ?>" required style="width: 100%; padding: 0.5rem;">
				</div>

				<div style="margin-bottom: 1rem;">
					<label>Foto Saat Ini:</label><br>
					<img src="img/<?php echo $d['gambar']; ?>" width="100" style="margin-bottom: 10px; border-radius: 5px;">
					<br>
					<label>Ganti Foto (Kosongkan jika tidak ingin mengubah):</label>
					<input type="file" name="gambar" accept="image/*" style="width: 100%;">
				</div>

				<button type="submit" class="btn-buy" style="background: #f59e0b;">Update Data</button>
			</form>
		</div>
	</div>
</body>

</html>