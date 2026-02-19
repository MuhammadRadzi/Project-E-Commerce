<?php
session_start();
include 'koneksi.php';
include 'navigation.php'; // Include navigation component

// Hapus item jika ada request
if (isset($_GET['hapus'])) {
	$id_hapus = $_GET['hapus'];
	unset($_SESSION['keranjang'][$id_hapus]);
	header("location:keranjang.php");
}

// Proteksi: Harus login untuk akses keranjang
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
	header("location:login.php?pesan=belum_login");
	exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Toko Komputer Online - Sedia berbagai macam perangkat hardware berkualitas.">
	<meta name="keywords" content="komputer, laptop, hardware, e-commerce">
	<title>Keranjang Belanja - <?php echo count($_SESSION['keranjang'] ?? []); ?> Item</title>
	<link rel="stylesheet" href="style.css">
	<style>
		.empty-cart {
			text-align: center;
			padding: 3rem 1rem;
		}

		.empty-cart svg {
			width: 5rem;
			height: 5rem;
			color: #cbd5e1;
			margin-bottom: 1rem;
		}

		.cart-actions {
			display: flex;
			gap: 1rem;
			justify-content: flex-end;
			margin-top: 2rem;
		}

		@media (max-width: 768px) {
			.cart-actions {
				flex-direction: column;
			}
		}
	</style>
</head>

<body>
	<nav>
		<h1>E-Commerce Project</h1>
		<ul>
			<li><a href="index.php">Katalog</a></li>
			<li><a href="keranjang.php">Keranjang</a></li>
			<li><a href="logout.php">Logout (<?php echo $_SESSION['username']; ?>)</a></li>
		</ul>
	</nav>

	<div class="container">

		<?php
		// Enhanced page header with breadcrumb and back button
		$jumlah_item = count($_SESSION['keranjang'] ?? []);
		page_header(
			'Keranjang Belanja (' . $jumlah_item . ' Item)',
			['index.php' => 'Katalog'],
			true
		);
		?>

		<?php if (!empty($_SESSION['keranjang'])): ?>
			<div class="table-responsive">
				<table style="width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
					<thead>
						<tr style="background: linear-gradient(to right, var(--primary-color), var(--secondary-color)); color: white;">
							<th style="padding: 1rem; text-align: left;">Nama Barang</th>
							<th style="padding: 1rem; text-align: right;">Harga Satuan</th>
							<th style="padding: 1rem; text-align: center;">Jumlah</th>
							<th style="padding: 1rem; text-align: right;">Subtotal</th>
							<th style="padding: 1rem; text-align: center;">Aksi</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$total_belanja = 0;
						foreach ($_SESSION['keranjang'] as $id => $item):
							$subtotal = $item['harga'] * $item['jumlah'];
							$total_belanja += $subtotal;
						?>
							<tr style="border-bottom: 1px solid #e2e8f0;">
								<td style="padding: 1rem;">
									<span style="font-weight: 600; color: #0f172a;"><?php echo htmlspecialchars($item['nama']); ?></span>
								</td>
								<td style="padding: 1rem; text-align: right; color: #64748b;">
									<?php echo formatRupiah($item['harga']); ?>
								</td>
								<td style="padding: 1rem; text-align: center;">
									<span style="display: inline-block; padding: 0.375rem 0.75rem; background: #f1f5f9; border-radius: 4px; font-weight: 600; color: #0f172a;">
										<?php echo $item['jumlah']; ?>x
									</span>
								</td>
								<td style="padding: 1rem; text-align: right;">
									<span style="font-weight: 700; color: var(--primary-color); font-size: 1.125rem;">
										<?php echo formatRupiah($subtotal); ?>
									</span>
								</td>
								<td style="padding: 1rem; text-align: center;">
									<a href="keranjang.php?hapus=<?php echo $id; ?>"
										onclick="return confirm('Hapus item ini dari keranjang?')"
										style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.5rem 1rem; background: #fee2e2; color: #991b1b; border-radius: 4px; text-decoration: none; font-size: 0.875rem; font-weight: 500; transition: all 0.2s;">
										<svg style="width: 1rem; height: 1rem;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
											<path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
										</svg>
										Hapus
									</a>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
					<tfoot>
						<tr style="background: #f8fafc;">
							<th colspan="3" style="padding: 1.5rem; text-align: right; font-size: 1.25rem; color: #0f172a;">
								TOTAL PEMBAYARAN
							</th>
							<th colspan="2" style="padding: 1.5rem; text-align: right; font-size: 1.5rem; font-weight: 800; color: var(--primary-color);">
								<?php echo formatRupiah($total_belanja); ?>
							</th>
						</tr>
					</tfoot>
				</table>
			</div>

			<div class="cart-actions">
				<a href="index.php" class="btn-buy" style="background: #64748b; text-decoration: none; display: inline-block; text-align: center; padding: 1rem 2rem;">
					<svg style="width: 1.25rem; height: 1.25rem; display: inline-block; vertical-align: middle; margin-right: 0.5rem;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
						<path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
					</svg>
					Lanjut Belanja
				</a>
				<button onclick="checkout()" class="btn-buy" style="padding: 1rem 3rem; font-size: 1.125rem; font-weight: 600;">
					<svg style="width: 1.25rem; height: 1.25rem; display: inline-block; vertical-align: middle; margin-right: 0.5rem;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
						<path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
						<path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z" />
					</svg>
					Checkout Sekarang
				</button>
			</div>

		<?php else: ?>
			<div class="empty-cart">
				<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
				</svg>
				<h2 style="font-size: 1.5rem; color: #0f172a; margin-bottom: 0.5rem;">Keranjang Kosong</h2>
				<p style="color: #64748b; margin-bottom: 2rem;">Belum ada barang di keranjang Anda</p>
				<a href="index.php" class="btn-buy" style="text-decoration: none; display: inline-block; padding: 1rem 2rem;">
					<svg style="width: 1.25rem; height: 1.25rem; display: inline-block; vertical-align: middle; margin-right: 0.5rem;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
						<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.707-10.293a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L9.414 11H13a1 1 0 100-2H9.414l1.293-1.293z" clip-rule="evenodd" />
					</svg>
					Mulai Belanja
				</a>
			</div>
		<?php endif; ?>
	</div>

	<script>
		function checkout() {
			if (confirm('Konfirmasi checkout dengan total <?php echo formatRupiah($total_belanja ?? 0); ?>?')) {
				alert('Terima kasih! Pesanan Anda sedang diproses.');
				// TODO: Implement actual checkout logic
				// window.location.href = 'checkout.php';
			}
		}
	</script>
</body>

</html>