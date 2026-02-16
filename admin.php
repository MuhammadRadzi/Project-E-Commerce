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

		.btn-delete {
			background-color: #ef4444;
		}

		.header-admin {
			display: flex;
			justify-content: space-between;
			align-items: center;
			margin-bottom: 2rem;
		}

		/* Loading Overlay */
		.loading-overlay {
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background: rgba(0, 0, 0, 0.7);
			display: none;
			justify-content: center;
			align-items: center;
			z-index: 9999;
		}

		.loading-overlay.active {
			display: flex;
		}

		.loading-content {
			background: white;
			padding: 2rem;
			border-radius: 12px;
			text-align: center;
			box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
		}

		.spinner {
			width: 50px;
			height: 50px;
			border: 4px solid #e5e7eb;
			border-top: 4px solid var(--primary-color);
			border-radius: 50%;
			animation: spin 1s linear infinite;
			margin: 0 auto 1rem;
		}

		@keyframes spin {
			0% {
				transform: rotate(0deg);
			}

			100% {
				transform: rotate(360deg);
			}
		}
	</style>
</head>

<body>
	<!-- Loading Overlay -->
	<div class="loading-overlay" id="loadingOverlay">
		<div class="loading-content">
			<div class="spinner"></div>
			<p style="margin: 0; font-weight: 500;">Memproses...</p>
		</div>
	</div>

	<header>
		<nav>
			<h1>Admin Dashboard</h1>
			<ul>
				<li>Halo, <b><?php echo $_SESSION['username']; ?></b></li>
				<li><a href="index.php" target="_blank">Lihat Website</a></li>
				<li><a href="logout.php" style="color: #ffcccc;">Logout</a></li>
			</ul>
		</nav>
	</header>

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
	<main class="container">
		<section>
			<div class="header-admin">
				<h2>Data Barang Inventaris</h2>
				<a href="tambah.php" class="btn-buy" style="width: auto; background: #10b981;">+ Tambah Barang</a>
			</div>

			<div class="table-responsive">
				<table aria-label="Tabel Data Barang">
					<thead>
						<tr>
							<th scope="col">No</th>
							<th scope="col">Gambar</th>
							<th scope="col">Nama Barang</th>
							<th scope="col">Kategori</th>
							<th scope="col">Stok</th>
							<th scope="col">Harga</th>
							<th scope="col">Kondisi</th>
							<th scope="col">Aksi</th>
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

									<a href="hapus.php?id=<?php echo $d['id_barang']; ?>" class="action-btn delete-btn" style="background: #ef4444; color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none;" data-nama="<?php echo htmlspecialchars($d['nama_barang']); ?>">Hapus</a>
								</td>
							</tr>
						<?php endwhile; ?>
					</tbody>
				</table>
			</div>
		</section>
	</main>

	<script>
		// Handle delete with loading
		document.querySelectorAll('.delete-btn').forEach(function(btn) {
			btn.addEventListener('click', function(e) {
				e.preventDefault();

				const namaBarang = this.getAttribute('data-nama');
				const url = this.getAttribute('href');

				if (confirm('Apakah Anda yakin ingin menghapus barang "' + namaBarang + '"?')) {
					// Show loading overlay
					document.getElementById('loadingOverlay').classList.add('active');

					// Navigate to delete URL
					window.location.href = url;
				}
			});
		});
	</script>

</body>

</html>