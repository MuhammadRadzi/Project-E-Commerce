<?php
session_start();
if ($_SESSION['role'] != "admin") {
    header("location:login.php");
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Toko Komputer Online - Sedia berbagai macam perangkat hardware berkualitas.">
    <meta name="keywords" content="komputer, laptop, hardware, e-commerce">
    <title>Tambah Barang - Admin</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <script>
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', function() {
                const btn = this.querySelector('button[type="submit"]');
                if (btn) {
                    btn.innerHTML = "Memproses...";
                    btn.classList.add('loading-cursor');
                }
            });
        });
    </script>
    <nav>
        <h1>Admin Panel</h1>
        <ul>
            <li><a href="admin.php">Kembali ke Data Barang</a></li>
        </ul>
    </nav>

    <div class="container" style="max-width: 600px; margin-top: 2rem;">
        <div class="card" style="padding: 2rem;">
            <h2>Tambah Barang Baru</h2>
            <hr><br>
            <form action="proses_tambah.php" method="post" enctype="multipart/form-data">
                <div style="margin-bottom: 1rem;">
                    <label>Nama Barang</label>
                    <input type="text" name="nama_barang" required style="width: 100%; padding: 0.5rem"
                        pattern="[A-Za-z\s]{3,}"
                        title="Nama barang harus berupa huruf dan minimal 3 karakter"
                        placeholder="Contoh: Laptop Asus">
                </div>
                <div style="margin-bottom: 1rem;">
                    <label>Kategori (Jenis)</label>
                    <input type="text" name="jenis_barang" required style="width: 100%; padding: 0.5rem;">
                </div>
                <div style="margin-bottom: 1rem;">
                    <label>Stok</label>
                    <input type="number" name="stok" min="0" required style="width: 100%; padding: 0.5rem;"
                        title="Stok tidak boleh negatif"
                        placeholder="Masukkan jumlah stok...">
                </div>
                <div style="margin-bottom: 1rem;">
                    <label>Harga</label>
                    <input type="number" name="harga" required style="width: 100%; padding: 0.5rem;">
                </div>
                <div style="margin-bottom: 1rem;">
                    <label>Kondisi</label>
                    <select name="kondisi" required style="width: 100%; padding: 0.5rem;">
                        <option value="Baru">Baru</option>
                        <option value="Bekas">Bekas</option>
                        <option value="Rusak">Rusak</option>
                    </select>
                </div>
                <div style="margin-bottom: 1rem;">
                    <label>Foto Barang</label>
                    <input type="file" name="gambar" accept="image/*" style="width: 100%;">
                </div>
                <button type="submit" class="btn-buy" style="background: #10b981;">Simpan Barang</button>
            </form>
        </div>
    </div>
</body>

</html>