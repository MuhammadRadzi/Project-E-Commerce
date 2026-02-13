<?php
session_start();
include 'koneksi.php';

// --- LOGIKA PENCARIAN & PAGINATION ---
$jumlahDataPerHalaman = 6;
$keyword = isset($_GET['cari']) ? $_GET['cari'] : "";

// Query untuk menghitung jumlah data (sesuaikan dengan pencarian)
$queryHitung = "SELECT * FROM barang WHERE nama_barang LIKE '%$keyword%' OR jenis_barang LIKE '%$keyword%'";
$resultCek = mysqli_query($conn, $queryHitung);
$jumlahData = mysqli_num_rows($resultCek);
$jumlahHalaman = ceil($jumlahData / $jumlahDataPerHalaman);

$halamanAktif = (isset($_GET['halaman'])) ? $_GET['halaman'] : 1;
$awalData = ($jumlahDataPerHalaman * $halamanAktif) - $jumlahDataPerHalaman;

// Query utama dengan LIMIT dan LIKE
$query = "SELECT * FROM barang WHERE nama_barang LIKE '%$keyword%' OR jenis_barang LIKE '%$keyword%' 
        ORDER BY id_barang DESC LIMIT $awalData, $jumlahDataPerHalaman";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Toko Komputer Online - Sedia berbagai macam perangkat hardware berkualitas.">
	<meta name="keywords" content="komputer, laptop, hardware, e-commerce">
	<title>Katalog Toko Komputer</title>
	<link rel="stylesheet" href="style.css">
</head>

<body>

	<nav>
		<h1>E-Commerce Project</h1>
		<ul>
			<li><a href="index.php">Katalog</a></li>
			<li>
				<a href="keranjang.php" style="position: relative;">
					BELI
					<?php if (!empty($_SESSION['keranjang'])): ?>
						<span style="background: red; color: white; border-radius: 50%; padding: 2px 6px; font-size: 10px; position: absolute; top: -10px; right: -15px;">
							<?php echo count($_SESSION['keranjang']); ?>
						</span>
					<?php endif; ?>
				</a>
			</li>

			<?php if (isset($_SESSION['status']) && $_SESSION['status'] == "login"): ?>
				<?php if ($_SESSION['role'] == "admin"): ?>
					<li><a href="admin.php">Admin Panel</a></li>
				<?php endif; ?>
				<li><a href="logout.php" style="color: #ffcfcf;">Logout (<?php echo $_SESSION['username']; ?>)</a></li>
			<?php else: ?>
				<li><a href="login.php">Login Admin</a></li>
			<?php endif; ?>
		</ul>
	</nav>

	<main class="container">
		<h2 class="page-title">Katalog Produk</h2>

		<!-- Search Section -->
		<div class="search-container" style="margin-bottom: 2rem; display: flex; justify-content: center;">
			<form action="index.php" method="get" style="display: flex; gap: 10px; width: 100%; max-width: 500px;">
				<input type="text" name="cari" placeholder="Cari barang atau kategori..."
					value="<?php echo $keyword; ?>"
					style="flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
				<button type="submit" class="btn-buy" style="width: auto; padding: 0 20px;">Cari</button>
				<?php if ($keyword != ""): ?>
					<a href="index.php" class="btn-page" style="line-height: 40px; border: 1px solid #ef4444; color: #ef4444;">Reset</a>
				<?php endif; ?>
			</form>
		</div>

		<?php if ($keyword != ""): ?>
			<p style="margin-bottom: 1rem;">Menampilkan hasil pencarian untuk: <b>"<?php echo $keyword; ?>"</b> (<?php echo $jumlahData; ?> ditemukan)</p>
		<?php endif; ?>

		<div class="catalog">
			<?php while ($row = mysqli_fetch_assoc($result)): ?>
				<?php
				// Logika Kategori 7: Cek Stok
				$isHabis = ($row['stok'] <= 0);
				$stokLabel = $isHabis ? 'Stock Habis' : 'Stok: ' . $row['stok'];
				$badgeClass = $isHabis ? 'bg-danger' : 'bg-success';
				$cardClass = $isHabis ? 'out-of-stock' : '';

				// Cek Gambar (Kalau kosong pakai placeholder)
				$gambar = $row['gambar'];
				if ($gambar == 'no-image.jpg' || empty($gambar)) {
					$imgSrc = "https://placehold.co/300x200?text=" . urlencode($row['nama_barang']);
				} else {
					$imgSrc = "img/" . $gambar;
				}
				?>

				<article class="card <?php echo $cardClass; ?>">
					<img src="<?php echo $imgSrc; ?>" alt="<?php echo $row['nama_barang']; ?>" class="card-img-top">

					<div class="card-body">
						<span class="card-category"><?php echo $row['jenis_barang']; ?></span>
						<h3 class="card-title"><?php echo $row['nama_barang']; ?></h3>

						<div class="card-price">
							<?php echo formatRupiah($row['harga']); ?>
						</div>

						<div>
							<span class="badge <?php echo $badgeClass; ?>">
								<?php echo $stokLabel; ?>
							</span>
							<span class="badge" style="border: 1px solid #ccc;">
								<?php echo $row['kondisi']; ?>
							</span>
						</div>

						<?php if ($isHabis): ?>
							<button class="btn-buy btn-disabled" disabled>Stok Habis</button>
						<?php else: ?>
							<a href="beli.php?id=<?php echo $row['id_barang']; ?>" class="btn-buy" style="text-align: center; text-decoration: none; display: block;">
								Beli Sekarang
							</a>
						<?php endif; ?>
					</div>
				</article>

			<?php endwhile; ?>
		</div>
	</main>

	<div class="pagination-container" style="text-align: center; margin: 2rem 0;">
		<?php if ($halamanAktif > 1): ?>
			<a href="?halaman=<?php echo $halamanAktif - 1; ?>&cari=<?php echo $keyword; ?>" class="btn-page">&laquo; Prev</a>
		<?php endif; ?>

		<?php for ($i = 1; $i <= $jumlahHalaman; $i++) : ?>
			<a href="?halaman=<?php echo $i; ?>&cari=<?php echo $keyword; ?>"
				class="btn-page <?php echo ($i == $halamanAktif) ? 'active' : ''; ?>">
				<?php echo $i; ?>
			</a>
		<?php endfor; ?>

		<?php if ($halamanAktif < $jumlahHalaman): ?>
			<a href="?halaman=<?php echo $halamanAktif + 1; ?>&cari=<?php echo $keyword; ?>" class="btn-page">Next &raquo;</a>
		<?php endif; ?>
	</div>

	<footer>
		<p style="text-align: center; margin-top: 2rem; padding: 1rem; background: #ddd;">
			&copy; 2024 Toko Komputer Project
		</p>
	</footer>

</body>

</html>