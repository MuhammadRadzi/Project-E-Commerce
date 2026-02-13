<?php
session_start();
include 'koneksi.php';

// Validasi ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('Invalid request!'); window.location='index.php';</script>";
    exit;
}

$id = $_GET['id'];

// Prepared Statement - Ambil detail barang
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

// Logika Tambah ke Keranjang dengan Transaction
if (isset($_POST['proses_beli'])) {
    $jumlah_beli = $_POST['jumlah'];

    // MULAI TRANSAKSI
    mysqli_begin_transaction($conn);

    try {
        // Cek stok terbaru dengan prepared statement dan lock
        $stmt_stok = $conn->prepare("SELECT stok FROM barang WHERE id_barang = ? FOR UPDATE");
        $stmt_stok->bind_param("i", $id);
        $stmt_stok->execute();
        $result_stok = $stmt_stok->get_result();
        $data_stok = $result_stok->fetch_assoc();

        if ($data_stok['stok'] < $jumlah_beli) {
            throw new Exception("Maaf, stok tidak mencukupi!");
        }

        // Kurangi stok barang
        $stok_baru = $data_stok['stok'] - $jumlah_beli;
        $stmt_update = $conn->prepare("UPDATE barang SET stok = ? WHERE id_barang = ?");
        $stmt_update->bind_param("ii", $stok_baru, $id);
        
        if (!$stmt_update->execute()) {
            throw new Exception("Gagal memperbarui stok.");
        }

        // Tambah ke keranjang session
        $_SESSION['keranjang'][$id] = [
            'nama' => $data['nama_barang'],
            'harga' => $data['harga'],
            'jumlah' => $jumlah_beli
        ];

        // COMMIT - Simpan permanen
        mysqli_commit($conn);
        header("location:keranjang.php?pesan=berhasil_beli");
        exit;

    } catch (Exception $e) {
        // ROLLBACK - Batalkan semua perubahan
        mysqli_rollback($conn);
        echo "<script>alert('" . $e->getMessage() . "'); window.location='index.php';</script>";
        exit;
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
    <title>Beli <?php echo htmlspecialchars($data['nama_barang']); ?></title>
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
            <h2><?php echo htmlspecialchars($data['nama_barang']); ?></h2>
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