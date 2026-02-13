<?php
include 'koneksi.php';

echo "<h2>Unit Testing: Database Connection</h2>";
if ($conn) {
    echo "<p style='color:green;'>[PASSED] Database terkoneksi dengan baik.</p>";
} else {
    echo "<p style='color:red;'>[FAILED] Database tidak terkoneksi.</p>";
}

echo "<h2>Unit Testing: Data Integrity</h2>";
$test_query = mysqli_query($conn, "SELECT * FROM barang LIMIT 1");
if ($test_query) {
    echo "<p style='color:green;'>[PASSED] Query Read data barang berhasil.</p>";
} else {
    echo "<p style='color:red;'>[FAILED] Query Read data barang gagal.</p>";
}
?>