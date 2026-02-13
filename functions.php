<?php
include 'koneksi.php';

/**
 * KATEGORI 4 & 6: Input Sanitization
 * Membersihkan input untuk mencegah SQL Injection dan XSS
 */
function registrasi_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = mysqli_real_escape_string($conn, $data);
    return $data;
}

/**
 * KATEGORI 3: Read Functionality
 * Mengambil data barang dengan opsi pencarian dan pagination
 * UPDATED: Menggunakan prepared statements untuk keamanan maksimal
 */
function getBarang($awalData, $jumlahDataPerHalaman, $keyword = "") {
    global $conn;
    
    // Prepared statement untuk keamanan
    $searchParam = "%$keyword%";
    $stmt = $conn->prepare("SELECT * FROM barang 
                            WHERE nama_barang LIKE ? 
                            OR jenis_barang LIKE ? 
                            ORDER BY id_barang DESC 
                            LIMIT ?, ?");
    $stmt->bind_param("ssii", $searchParam, $searchParam, $awalData, $jumlahDataPerHalaman);
    $stmt->execute();
    
    return $stmt->get_result();
}

/**
 * KATEGORI 4: Transaction Handling
 * Fungsi untuk memproses pembelian dengan Rollback mechanism
 */
function prosesPembelian($id_barang, $jumlah_beli) {
    global $conn;
    mysqli_begin_transaction($conn);

    try {
        // Lock table untuk konsistensi data integritas dengan prepared statement
        $stmt = $conn->prepare("SELECT stok FROM barang WHERE id_barang = ? FOR UPDATE");
        $stmt->bind_param("i", $id_barang);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();

        if ($data['stok'] < $jumlah_beli) {
            throw new Exception("Stok tidak mencukupi!");
        }

        $stok_baru = $data['stok'] - $jumlah_beli;
        $stmt_update = $conn->prepare("UPDATE barang SET stok = ? WHERE id_barang = ?");
        $stmt_update->bind_param("ii", $stok_baru, $id_barang);

        if (!$stmt_update->execute()) {
            throw new Exception("Gagal update stok.");
        }

        mysqli_commit($conn);
        return true;
    } catch (Exception $e) {
        mysqli_rollback($conn);
        return $e->getMessage();
    }
}

/**
 * Testing Helper
 * Fungsi sederhana untuk mengecek kesehatan database
 */
function checkSystemHealth() {
    global $conn;
    return $conn ? "Healthy" : "Down";
}
?>