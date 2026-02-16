<?php
session_start();
include 'functions.php';

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

// Menggunakan fungsi dari functions.php
$result = getBarang($awalData, $jumlahDataPerHalaman, $keyword);

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
	<style>
		/* Loading Overlay */
		.loading-overlay {
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background: rgba(255, 255, 255, 0.9);
			display: flex;
			justify-content: center;
			align-items: center;
			z-index: 9999;
			opacity: 1;
			transition: opacity 0.3s;
		}

		.loading-overlay.hidden {
			opacity: 0;
			pointer-events: none;
		}

		.spinner {
			width: 50px;
			height: 50px;
			border: 4px solid #e5e7eb;
			border-top: 4px solid var(--primary-color);
			border-radius: 50%;
			animation: spin 1s linear infinite;
		}

		@keyframes spin {
			0% {
				transform: rotate(0deg);
			}

			100% {
				transform: rotate(360deg);
			}
		}

		/* Skeleton Card */
		.skeleton-card {
			background: white;
			border-radius: 12px;
			overflow: hidden;
			box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
			border: 1px solid #e2e8f0;
		}

		.skeleton-img {
			width: 100%;
			height: 200px;
			background: linear-gradient(90deg, #f3f4f6 25%, #e5e7eb 50%, #f3f4f6 75%);
			background-size: 200% 100%;
			animation: skeleton-loading 1.5s infinite;
		}

		.skeleton-text {
			height: 16px;
			margin: 10px 15px;
			background: linear-gradient(90deg, #f3f4f6 25%, #e5e7eb 50%, #f3f4f6 75%);
			background-size: 200% 100%;
			animation: skeleton-loading 1.5s infinite;
			border-radius: 4px;
		}

		.skeleton-text.short {
			width: 60%;
		}

		.skeleton-text.long {
			width: 80%;
		}
	</style>
</head>

<body>
	<!-- Loading Overlay -->
	<div class="loading-overlay" id="loadingOverlay">
		<div class="spinner"></div>
	</div>

	<header>
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
					<li><a href="logout.php">Logout (<?php echo $_SESSION['username']; ?>)</a></li>
				<?php else: ?>
					<li><a href="login.php">Login</a></li>
				<?php endif; ?>
			</ul>
		</nav>
	</header>

	<main class="container">
		<header class="page-header">
			<h2 class="page-title">Katalog Produk</h2>

			<!-- Search Section with Loading -->
			<section class="search-section" aria-label="Pencarian Produk">
				<form action="index.php" method="get" id="searchForm" style="display: flex; gap: 10px; justify-content: center; margin-bottom: 2rem;">
					<input type="search" name="cari" placeholder="Cari barang..." value="<?php echo htmlspecialchars($keyword); ?>" aria-label="Cari barang" style="padding: 10px; border-radius: 4px; border: 1px solid #ccc; width: 300px;">
					<button type="submit" class="btn-buy" id="searchBtn" style="width: auto; padding: 10px 20px;">Cari</button>
				</form>
			</section>
		</header>

		<?php if ($keyword != ""): ?>
			<p style="margin-bottom: 1rem;">Menampilkan hasil pencarian untuk: <b>"<?php echo htmlspecialchars($keyword); ?>"</b> (<?php echo $jumlahData; ?> ditemukan)</p>
		<?php endif; ?>

		<section class="catalog" id="catalogSection">
			<?php while ($row = mysqli_fetch_assoc($result)): ?>
				<?php
				// Cek Stok
				$isHabis = ($row['stok'] <= 0);
				$cardClass = $isHabis ? 'out-of-stock' : '';
				$stokLabel = $isHabis ? 'Stok Habis' : 'Stok: ' . $row['stok'];
				$badgeClass = $isHabis ? 'bg-danger' : 'bg-success';

				// Cek Gambar
				$gambar = $row['gambar'];
				if ($gambar == 'no-image.jpg' || empty($gambar)) {
					$imgSrc = "https://placehold.co/300x200?text=" . urlencode($row['nama_barang']);
				} else {
					$imgSrc = "img/" . htmlspecialchars($gambar);
				}
				?>

				<article class="card <?php echo $cardClass; ?>">
					<figure>
						<img src="<?php echo $imgSrc; ?>"
							alt="<?php echo htmlspecialchars($row['nama_barang']); ?> - <?php echo htmlspecialchars($row['jenis_barang']); ?>"
							loading="lazy" class="card-img-top">
					</figure>
					<div class="card-body">
						<header>
							<span class="card-category"><?php echo htmlspecialchars($row['jenis_barang']); ?></span>
							<h3 class="card-title"><?php echo htmlspecialchars($row['nama_barang']); ?></h3>
						</header>

						<p class="card-price">
							<?php echo formatRupiah($row['harga']); ?>
						</p>

						<div style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
							<span class="badge <?php echo $badgeClass; ?>">
								<?php echo $stokLabel; ?>
							</span>
							<span class="badge" style="border: 1px solid #ccc; background: white; color: #666;">
								<?php echo htmlspecialchars($row['kondisi']); ?>
							</span>
						</div>

						<?php if ($isHabis): ?>
							<button class="btn-buy btn-disabled" disabled>Stok Habis</button>
						<?php else: ?>
							<footer>
								<a href="beli.php?id=<?php echo $row['id_barang']; ?>" class="btn-buy" style="text-align: center; text-decoration: none; display: block;">
									Beli Sekarang
								</a>
							</footer>
						<?php endif; ?>
					</div>
				</article>
			<?php endwhile; ?>
		</section>
	</main>

	<div class="pagination-container" style="text-align: center; margin: 2rem 0;">
		<?php if ($halamanAktif > 1): ?>
			<a href="?halaman=<?php echo $halamanAktif - 1; ?>&cari=<?php echo urlencode($keyword); ?>" class="btn-page pagination-link">&laquo; Prev</a>
		<?php endif; ?>

		<?php for ($i = 1; $i <= $jumlahHalaman; $i++) : ?>
			<a href="?halaman=<?php echo $i; ?>&cari=<?php echo urlencode($keyword); ?>"
				class="btn-page pagination-link <?php echo ($i == $halamanAktif) ? 'active' : ''; ?>">
				<?php echo $i; ?>
			</a>
		<?php endfor; ?>

		<?php if ($halamanAktif < $jumlahHalaman): ?>
			<a href="?halaman=<?php echo $halamanAktif + 1; ?>&cari=<?php echo urlencode($keyword); ?>" class="btn-page pagination-link">Next &raquo;</a>
		<?php endif; ?>
	</div>

	<footer style="text-align: center; padding: 2rem; background: #ddd; margin-top: 2rem;">
		<p>&copy; 2026 Toko Komputer Project. All rights reserved.</p>
	</footer>

	<script>
		// Hide loading overlay when page fully loaded
		window.addEventListener('load', function() {
			setTimeout(function() {
				document.getElementById('loadingOverlay').classList.add('hidden');
			}, 300);
		});

		// Show loading on form submit
		document.getElementById('searchForm').addEventListener('submit', function() {
			document.getElementById('searchBtn').innerHTML = '<span style="display: inline-block; width: 16px; height: 16px; border: 2px solid white; border-top: 2px solid transparent; border-radius: 50%; animation: spin 0.8s linear infinite;"></span> Mencari...';
			document.getElementById('searchBtn').disabled = true;
		});

		// Show loading on pagination click
		document.querySelectorAll('.pagination-link').forEach(function(link) {
			link.addEventListener('click', function() {
				document.getElementById('loadingOverlay').classList.remove('hidden');
			});
		});

		// Add spin animation inline
		const style = document.createElement('style');
		style.textContent = '@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }';
		document.head.appendChild(style);
	</script>

</body>

</html>