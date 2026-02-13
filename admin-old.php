<?php
session_start();
include 'koneksi.php';

// Cek apakah user sudah login DAN role-nya admin
if ($_SESSION['status'] != "login" || $_SESSION['role'] != "admin") {
	header("location:login.php?pesan=belum_login");
	exit;
}

// Ambil data barang
$query = mysqli_query($conn, "SELECT * FROM barang");
?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Toko Komputer Online - Sedia berbagai macam perangkat hardware berkualitas.">
	<meta name="keywords" content="komputer, laptop, hardware, e-commerce">
	<title>Halaman Admin</title>
	<link rel="stylesheet" href="style.css">
	<style>
		/* Tambahan CSS Tabel untuk Admin */
		body {
			display: block;
		}

		/* Reset display flex dari login */
		table {
			width: 100%;
			border-collapse: collapse;
			margin-top: 1rem;
			background: white;
		}

		th,
		td {
			padding: 12px;
			border: 1px solid #ddd;
			text-align: left;
		}

		th {
			background-color: var(--primary-color);
			color: white;
		}

		tr:nth-child(even) {
			background-color: #f2f2f2;
		}

		.action-btn {
			text-decoration: none;
			padding: 5px 10px;
			border-radius: 4px;
			color: white;
			font-size: 0.9rem;
			margin-right: 5px;
		}

		.btn-edit {
			background-color: #f59e0b;
		}

		/* Kuning/Orange */
		.btn-delete {
			background-color: #ef4444;
		}

		/* Merah */
		.header-admin {
			display: flex;
			justify-content: space-between;
			align-items: center;
			margin-bottom: 2rem;
		}
	</style>
</head>

<body>

	<nav>
		<h1>Admin Dashboard</h1>
		<ul>
			<li>Halo, <b><?php echo $_SESSION['username']; ?></b></li>
			<li><a href="index.php" target="_blank">Lihat Website</a></li>
			<li><a href="logout.php" style="color: #ffcccc;">Logout</a></li>
		</ul>
	</nav>

	<div class="container">

		<?php
		if (isset($_GET['pesan'])) {
			if ($_GET['pesan'] == "tambah_sukses") {
				tampilkanPesan('sukses', 'Barang berhasil ditambahkan ke katalog!');
			} else if ($_GET['pesan'] == "update_sukses") {
				tampilkanPesan('sukses', 'Data barang berhasil diperbarui!');
			} else if ($_GET['pesan'] == "hapus_sukses") {
				tampilkanPesan('sukses', 'Barang berhasil dihapus.');
			} else if ($_GET['pesan'] == "error_db") {
				tampilkanPesan('gagal', 'Terjadi kesalahan sistem. Silakan coba lagi.');
			}
		}
		?>

		<div class="header-admin">
			<h2>Data Barang Inventaris</h2>
			<a href="tambah.php" class="btn-buy" style="width: auto; background: #10b981;">+ Tambah Barang</a>

		</div>

		<table>
			<thead>
				<tr>
					<th>No</th>
					<th>Gambar</th>
					<th>Nama Barang</th>
					<th>Kategori</th>
					<th>Stok</th>
					<th>Harga</th>
					<th>Kondisi</th>
					<th>Aksi</th>
				</tr>
			</thead>
			<tbody>
				<?php $no = 1;
				while ($d = mysqli_fetch_array($query)): ?>
					<tr>
						<td><?php echo $no++; ?></td>
						<td>
							<?php if ($d['gambar'] != 'no-image.jpg'): ?>
								<img src="img/<?php echo $d['gambar']; ?>" width="50" height="50" style="object-fit: cover;">
							<?php else: ?>
								<span style="font-size: 0.8rem; color: #888;">No Image</span>
							<?php endif; ?>
						</td>
						<td><?php echo $d['nama_barang']; ?></td>
						<td><?php echo $d['jenis_barang']; ?></td>
						<td><?php echo $d['stok']; ?></td>
						<td><?php echo formatRupiah($d['harga']); ?></td>
						<td><?php echo $d['kondisi']; ?></td>
						<td>
							<a href="edit.php?id=<?php echo $d['id_barang']; ?>" class="action-btn" style="background: #f59e0b; color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none;">Edit</a>

							<a href="hapus.php?id=<?php echo $d['id_barang']; ?>" class="action-btn" style="background: #ef4444; color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none;" onclick="return confirm('Apakah Anda yakin ingin menghapus barang ini?')">Hapus</a>
						</td>
					</tr>
				<?php endwhile; ?>
			</tbody>
		</table>
	</div>

</body>

</html>