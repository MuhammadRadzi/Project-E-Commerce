<?php
session_start();
include 'koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:login.php?pesan=belum_login");
    exit;
}

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

// PROCESS FORM SUBMISSION
if (isset($_POST['proses_beli'])) {
    $jumlah_beli = (int)$_POST['jumlah'];

    // Validasi input
    if ($jumlah_beli <= 0) {
        echo "<script>alert('Jumlah tidak valid!'); window.location='beli.php?id=$id';</script>";
        exit;
    }

    // MULAI TRANSAKSI
    mysqli_begin_transaction($conn);

    try {
        // Cek stok terbaru dengan prepared statement dan lock
        $stmt_stok = $conn->prepare("SELECT stok FROM barang WHERE id_barang = ? FOR UPDATE");
        $stmt_stok->bind_param("i", $id);
        $stmt_stok->execute();
        $result_stok = $stmt_stok->get_result();
        $data_stok = $result_stok->fetch_assoc();

        if (!$data_stok) {
            throw new Exception("Barang tidak ditemukan!");
        }

        if ($data_stok['stok'] < $jumlah_beli) {
            throw new Exception("Maaf, stok tidak mencukupi! Tersedia: " . $data_stok['stok'] . " unit");
        }

        // Kurangi stok barang
        $stok_baru = $data_stok['stok'] - $jumlah_beli;
        $stmt_update = $conn->prepare("UPDATE barang SET stok = ? WHERE id_barang = ?");
        $stmt_update->bind_param("ii", $stok_baru, $id);
        
        if (!$stmt_update->execute()) {
            throw new Exception("Gagal memperbarui stok.");
        }

        // Cek apakah barang sudah ada di keranjang
        if (isset($_SESSION['keranjang'][$id])) {
            // Jika sudah ada, tambahkan jumlahnya
            $_SESSION['keranjang'][$id]['jumlah'] += $jumlah_beli;
        } else {
            // Jika belum ada, buat entry baru
            $_SESSION['keranjang'][$id] = [
                'nama' => $data['nama_barang'],
                'harga' => $data['harga'],
                'jumlah' => $jumlah_beli
            ];
        }

        // COMMIT - Simpan permanen
        mysqli_commit($conn);
        
        // Redirect dengan pesan sukses
        header("location:keranjang.php?pesan=berhasil_beli");
        exit;

    } catch (Exception $e) {
        // ROLLBACK - Batalkan semua perubahan
        mysqli_rollback($conn);
        echo "<script>alert('" . htmlspecialchars($e->getMessage()) . "'); window.location='beli.php?id=$id';</script>";
        exit;
    }
}

// Include navigation AFTER processing
include 'navigation.php';
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
        <h1>E-Commerce Project</h1>
        <ul>
            <li><a href="index.php">Katalog</a></li>
            <li>
                <a href="keranjang.php" style="position: relative;">
                    Keranjang
                    <?php if (!empty($_SESSION['keranjang'])): ?>
                        <span style="background: red; color: white; border-radius: 50%; padding: 2px 6px; font-size: 10px; position: absolute; top: -10px; right: -15px;">
                            <?php echo count($_SESSION['keranjang']); ?>
                        </span>
                    <?php endif; ?>
                </a>
            </li>
        </ul>
    </nav>

    <div class="container" style="max-width: 500px;">

        <?php
        // Enhanced page header with breadcrumb and back button
        page_header(
            'Konfirmasi Pembelian',
            ['index.php' => 'Katalog'],
            true
        );
        ?>

        <div class="card" style="padding: 2rem;">
            <div style="margin-bottom: 1.5rem;">
                <?php if ($data['gambar'] != 'no-image.jpg' && !empty($data['gambar'])): ?>
                    <img src="img/<?php echo htmlspecialchars($data['gambar']); ?>"
                        alt="<?php echo htmlspecialchars($data['nama_barang']); ?>"
                        style="width: 100%; height: 200px; object-fit: cover; border-radius: 8px; margin-bottom: 1rem;">
                <?php endif; ?>

                <h2 style="margin-bottom: 0.5rem;"><?php echo htmlspecialchars($data['nama_barang']); ?></h2>
                <p style="font-size: 1.5rem; color: var(--primary-color); font-weight: bold;">
                    <?php echo formatRupiah($data['harga']); ?>
                </p>

                <div style="display: inline-block; padding: 0.375rem 0.75rem; background: #d1fae5; color: #065f46; border-radius: 4px; font-size: 0.875rem; font-weight: 500; margin-top: 0.5rem;">
                    📦 Tersedia: <?php echo $data['stok']; ?> unit
                </div>
            </div>

            <hr style="margin: 1.5rem 0; border: none; border-top: 1px solid #e2e8f0;">

            <form method="POST" action="" id="purchaseForm">
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Jumlah Pembelian:</label>
                    <input type="number" name="jumlah" id="jumlahInput" value="1" min="1" max="<?php echo $data['stok']; ?>" required
                        style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 1rem;">
                    <small style="color: #64748b; margin-top: 0.25rem; display: block;">Maksimal: <?php echo $data['stok']; ?> unit</small>
                </div>

                <div style="background: #f8fafc; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span style="color: #64748b;">Harga Satuan:</span>
                        <span style="font-weight: 600;"><?php echo formatRupiah($data['harga']); ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span style="color: #64748b;">Jumlah:</span>
                        <span style="font-weight: 600;" id="displayJumlah">1</span>
                    </div>
                    <hr style="margin: 0.75rem 0; border: none; border-top: 1px dashed #cbd5e1;">
                    <div style="display: flex; justify-content: space-between;">
                        <span style="font-weight: 700; font-size: 1.125rem;">Total:</span>
                        <span style="font-weight: 700; font-size: 1.125rem; color: var(--primary-color);" id="displayTotal"><?php echo formatRupiah($data['harga']); ?></span>
                    </div>
                </div>

                <div style="display: flex; gap: 1rem;">
                    <button type="button" onclick="history.back()" class="btn-buy" style="background: #64748b; flex: 1;">Batal</button>
                    <button type="submit" name="proses_beli" value="1" class="btn-buy" style="flex: 2;" id="submitBtn">
                        <svg style="width: 1.25rem; height: 1.25rem; display: inline-block; vertical-align: middle; margin-right: 0.5rem;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" />
                        </svg>
                        Masukkan ke Keranjang
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        console.log('beli.php loaded');
        console.log('Current cart count:', <?php echo count($_SESSION['keranjang'] ?? []); ?>);
        
        // Real-time calculation
        const jumlahInput = document.getElementById('jumlahInput');
        const hargaSatuan = <?php echo $data['harga']; ?>;
        const stokMax = <?php echo $data['stok']; ?>;

        jumlahInput.addEventListener('input', function() {
            let jumlah = parseInt(this.value) || 1;
            
            if (jumlah > stokMax) {
                jumlah = stokMax;
                this.value = stokMax;
                alert('Jumlah tidak boleh melebihi stok yang tersedia!');
            }
            
            if (jumlah < 1) {
                jumlah = 1;
                this.value = 1;
            }
            
            const total = hargaSatuan * jumlah;
            
            document.getElementById('displayJumlah').textContent = jumlah;
            document.getElementById('displayTotal').textContent = formatRupiah(total);
        });

        function formatRupiah(angka) {
            return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        // Form submission
        document.getElementById('purchaseForm').addEventListener('submit', function(e) {
            console.log('Form submit triggered!');
            
            const jumlah = parseInt(jumlahInput.value);
            console.log('Quantity:', jumlah);
            
            if (jumlah < 1 || jumlah > stokMax) {
                e.preventDefault();
                alert('Jumlah tidak valid!');
                return false;
            }
            
            const btn = document.getElementById('submitBtn');
            btn.innerHTML = '<span style="display: inline-block; width: 16px; height: 16px; border: 2px solid white; border-top: 2px solid transparent; border-radius: 50%; animation: spin 0.8s linear infinite; margin-right: 0.5rem;"></span>Memproses...';
            btn.disabled = true;
            
            // Let form submit naturally
            return true;
        });
        
        // Add animation
        const style = document.createElement('style');
        style.textContent = '@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }';
        document.head.appendChild(style);
    </script>
</body>

</html>	