<?php
session_start();
include 'koneksi.php';

// Ambil ID dari URL
$id = $_GET['id'];

// Ambil detail barang dari database
$query = mysqli_query($conn, "SELECT * FROM barang WHERE id_barang = '$id'");
$data = mysqli_fetch_assoc($query);

// Prepared Statement
$stmt = $conn->prepare("SELECT * FROM barang WHERE id_barang = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

// Jika barang tidak ditemukan atau stok habis
if (!$data || $data['stok'] <= 0) {
    echo "<script>alert('Barang tidak tersedia!'); window.location='index.php';</script>";
    exit;
}

// Inisialisasi keranjang jika belum ada
if (!isset($_SESSION['keranjang'])) {
	$_SESSION['keranjang'] = [];
}

// Logika Tambah ke Keranjang
if (isset($_POST['proses_beli'])) {
	$jumlah = $_POST['jumlah'];

	if ($jumlah > $data['stok']) {
		echo "<script>alert('Stok tidak mencukupi!');</script>";
	} else {
		// Simpan ke session keranjang
		$_SESSION['keranjang'][$id] = [
			'nama' => $data['nama_barang'],
			'harga' => $data['harga'],
			'jumlah' => $jumlah
		];
		header("location:keranjang.php");
	}
}

if (isset($_POST['proses_beli'])) {
    $id_barang = $_GET['id'];
    $jumlah_beli = $_POST['jumlah'];

    // 1. MULAI TRANSAKSI
    mysqli_begin_transaction($conn);

    try {
        // 2. Cek stok terbaru (Lock for update agar data tidak berubah saat diproses)
        $query_stok = mysqli_query($conn, "SELECT stok FROM barang WHERE id_barang = '$id_barang' FOR UPDATE");
        $data_stok = mysqli_fetch_assoc($query_stok);

        if ($data_stok['stok'] < $jumlah_beli) {
            throw new Exception("Maaf, stok tiba-tiba habis!");
        }

        // 3. Kurangi stok barang
        $stok_baru = $data_stok['stok'] - $jumlah_beli;
        $update_stok = mysqli_query($conn, "UPDATE barang SET stok = '$stok_baru' WHERE id_barang = '$id_barang'");

        if (!$update_stok) {
            throw new Exception("Gagal memperbarui stok.");
        }

        // 4. (Opsional) Tambah ke tabel pesanan/keranjang session
        $_SESSION['keranjang'][$id_barang] = [
            'nama' => $data['nama_barang'],
            'harga' => $data['harga'],
            'jumlah' => $jumlah_beli
        ];

        // 5. JIKA SEMUA SUKSES, COMMIT (SIMPAN PERMANEN)
        mysqli_commit($conn);
        header("location:keranjang.php?pesan=berhasil_beli");

    } catch (Exception $e) {
        // 6. JIKA ADA YANG GAGAL, ROLLBACK (BATALKAN SEMUA PERUBAHAN)
        mysqli_rollback($conn);
        echo "<script>alert('" . $e->getMessage() . "'); window.location='index.php';</script>";
    }
}
?>



<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Toko Komputer Online - Sedia berbagai macam perangkat hardware berkualitas.">
    <meta name="keywords" content="komputer, laptop, hardware, e-commerce">
	<title>Beli <?php echo $data['nama_barang']; ?></title>
	<link rel="stylesheet" href="style.css">
</head>

<body>
	<nav>
		<?php breadcrumb("Konfirmasi Pembelian"); ?>
		<h1>Konfirmasi Pembelian</h1>
		<ul>
			<li><a href="index.php">Kembali ke Katalog</a></li>
		</ul>
	</nav>

	<div class="container" style="max-width: 500px;">
		<div class="card" style="padding: 2rem;">
			<h2><?php echo $data['nama_barang']; ?></h2>
			<p>Harga: <b><?php echo formatRupiah($data['harga']); ?></b></p>
			<p>Tersedia: <?php echo $data['stok']; ?> unit</p>
			<hr style="margin: 1rem 0;">

			<form method="post">
				<div style="margin-bottom: 1rem;">
					<label>Jumlah Beli:</label>
					<input type="number" name="jumlah" value="1" min="1" max="<?php echo $data['stok']; ?>" required
						style="width: 100%; padding: 0.5rem; margin-top: 0.5rem;">
				</div>
				<button type="submit" name="proses_beli" class="btn-buy">Masukkan ke Keranjang</button>
			</form>
		</div>
	</div>
</body>

</html>