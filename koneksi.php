<?php
// Konfigurasi Database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_ecommerce";

// Membuat koneksi
$conn = mysqli_connect($host, $user, $pass, $db);

// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Set fungsi format rupiah (Helper function agar rapi)
function formatRupiah($angka)
{
    return "Rp " . number_format($angka, 0, ',', '.');
}

// Fungsi untuk membuat Breadcrumb sederhana
function breadcrumb($judul_halaman)
{
    echo "
    <nav aria-label='breadcrumb' style='margin-bottom: 20px; font-size: 0.9rem;'>
        <ol style='display: flex; list-style: none; gap: 10px; padding: 0;'>
            <li><a href='index.php' style='color: var(--primary-color); text-decoration: none;'>Home</a></li>
            <li style='color: #666;'> / </li>
            <li style='color: #333; font-weight: bold;'>$judul_halaman</li>
        </ol>
    </nav>";
}

// Fungsi untuk menampilkan pesan feedback
function tampilkanPesan($jenis, $pesan)
{
    $warna = ($jenis == 'sukses') ? '#10b981' : '#ef4444';
    echo "
    <div style='background: $warna; color: white; padding: 15px; border-radius: 5px; margin-bottom: 20px; text-align: center; font-weight: bold;'>
        $pesan
    </div>";
}

// Fungsi untuk mencegah SQL Injection & XSS
function input($data) {
    global $conn;
    $data = trim($data); // Hapus spasi di awal/akhir
    $data = stripslashes($data); // Hapus backslashes
    $data = htmlspecialchars($data); // Ubah karakter khusus jadi HTML entities
    $data = mysqli_real_escape_string($conn, $data); // Filter karakter SQL
    return $data;
}

// Menutup koneksi secara otomatis saat skrip selesai (Connection Management)
register_shutdown_function(function() use ($conn) {
    if ($conn) {
        mysqli_close($conn);
    }
});