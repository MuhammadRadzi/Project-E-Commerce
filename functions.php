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
 */
function getBarang($awalData, $jumlahDataPerHalaman, $keyword = "") {
    global $conn;
    $query = "SELECT * FROM barang 
              WHERE nama_barang LIKE '%$keyword%' 
              OR jenis_barang LIKE '%$keyword%' 
              ORDER BY id_barang DESC 
              LIMIT $awalData, $jumlahDataPerHalaman";
    return mysqli_query($conn, $query);
}

/**
 * KATEGORI 4: Transaction Handling
 * Fungsi untuk memproses pembelian dengan Rollback mechanism
 */
function prosesPembelian($id_barang, $jumlah_beli) {
    global $conn;
    mysqli_begin_transaction($conn);

    try {
        // Lock table untuk konsistensi data integritas
        $query = mysqli_query($conn, "SELECT stok FROM barang WHERE id_barang = '$id_barang' FOR UPDATE");
        $data = mysqli_fetch_assoc($query);

        if ($data['stok'] < $jumlah_beli) {
            throw new Exception("Stok tidak mencukupi!");
        }

        $stok_baru = $data['stok'] - $jumlah_beli;
        $update = mysqli_query($conn, "UPDATE barang SET stok = '$stok_baru' WHERE id_barang = '$id_barang'");

        if (!$update) throw new Exception("Gagal update stok.");

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