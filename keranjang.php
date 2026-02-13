<?php
session_start();
include 'koneksi.php';

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
	<title>Keranjang Belanja</title>
	<link rel="stylesheet" href="style.css">
</head>

<body>
	<nav>
		<h1>Keranjang Belanja</h1>
		<ul>
			<li><a href="index.php">Lanjut Belanja</a></li>
		</ul>
	</nav>

	<div class="container">
		<table>
			<thead>
				<tr>
					<th>Nama Barang</th>
					<th>Harga Satuan</th>
					<th>Jumlah</th>
					<th>Subtotal</th>
					<th>Aksi</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$total_belanja = 0;
				if (!empty($_SESSION['keranjang'])):
					foreach ($_SESSION['keranjang'] as $id => $item):
						$subtotal = $item['harga'] * $item['jumlah'];
						$total_belanja += $subtotal;
				?>
						<tr>
							<td><?php echo $item['nama']; ?></td>
							<td><?php echo formatRupiah($item['harga']); ?></td>
							<td><?php echo $item['jumlah']; ?></td>
							<td><?php echo formatRupiah($subtotal); ?></td>
							<td><a href="keranjang.php?hapus=<?php echo $id; ?>" style="color:red;">Batal</a></td>
						</tr>
					<?php endforeach;
				else: ?>
					<tr>
						<td colspan="5" style="text-align:center;">Keranjang kosong</td>
					</tr>
				<?php endif; ?>
			</tbody>
			<tfoot>
				<tr>
					<th colspan="3">TOTAL HARGA</th>
					<th colspan="2"><?php echo formatRupiah($total_belanja); ?></th>
				</tr>
			</tfoot>
		</table>

		<?php if ($total_belanja > 0): ?>
			<div style="margin-top: 2rem; text-align: right;">
				<button onclick="alert('Terima kasih! Pesanan diproses.')" class="btn-buy" style="width: 200px;">Checkout</button>
				<!-- Jika sudah dibeli, bersihkan keranjang -->
			</div>
		<?php endif; ?>
	</div>
</body>

</html>