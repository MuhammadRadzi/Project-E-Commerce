<?php
session_start();
include 'navigation.php'; // Include navigation component

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
            <li><a href="admin.php">Dashboard</a></li>
            <li><a href="logout.php" style="color: #ffcccc;">Logout</a></li>
        </ul>
    </nav>

    <div class="container" style="max-width: 600px; margin-top: 2rem;">

        <?php
        // Enhanced page header with breadcrumb and back button
        page_header(
            'Tambah Barang Baru',
            ['admin.php' => 'Dashboard'],
            true,
            'admin.php'
        );
        ?>

        <div class="card" style="padding: 2rem;">
            <form action="proses_tambah.php" method="post" enctype="multipart/form-data" id="addProductForm">
                <div style="margin-bottom: 1rem;">
                    <label>Nama Barang <span style="color: red;">*</span></label>
                    <input type="text" name="nama_barang" required style="width: 100%; padding: 0.5rem"
                        pattern="[A-Za-z\s]{3,}"
                        title="Nama barang harus berupa huruf dan minimal 3 karakter"
                        placeholder="Contoh: Laptop Asus">
                </div>

                <div style="margin-bottom: 1rem;">
                    <label>Kategori (Jenis) <span style="color: red;">*</span></label>
                    <input type="text" name="jenis_barang" required style="width: 100%; padding: 0.5rem;" placeholder="Contoh: Laptop, Mouse, Keyboard">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label>Stok <span style="color: red;">*</span></label>
                        <input type="number" name="stok" min="0" required style="width: 100%; padding: 0.5rem;"
                            title="Stok tidak boleh negatif"
                            placeholder="Jumlah stok">
                    </div>
                    <div>
                        <label>Harga <span style="color: red;">*</span></label>
                        <input type="number" name="harga" required style="width: 100%; padding: 0.5rem;" placeholder="Harga dalam Rupiah">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label>Kondisi <span style="color: red;">*</span></label>
                        <select name="kondisi" required style="width: 100%; padding: 0.5rem;">
                            <option value="">-- Pilih Kondisi --</option>
                            <option value="Baru">Baru</option>
                            <option value="Bekas">Bekas</option>
                            <option value="Rusak">Rusak</option>
                        </select>
                    </div>
                    <div>
                        <label>Lokasi Rak</label>
                        <input type="text" name="lokasi_rak" placeholder="Contoh: A-01"
                            pattern="[A-Z]-[0-9]{2}" title="Format: A-01, B-05, dll"
                            style="width: 100%; padding: 0.5rem;">
                    </div>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label>Foto Barang</label>
                    <div style="border: 2px dashed #cbd5e1; border-radius: 8px; padding: 1.5rem; text-align: center; cursor: pointer;" onclick="document.getElementById('fileInput').click()">
                        <svg style="width: 3rem; height: 3rem; margin: 0 auto 0.5rem; color: #94a3b8;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        <p style="color: #64748b; margin-bottom: 0.5rem;">Klik untuk upload gambar</p>
                        <p style="font-size: 0.875rem; color: #94a3b8;">PNG, JPG, JPEG (Max 2MB)</p>
                        <input type="file" name="gambar" accept="image/*" id="fileInput" style="display: none;">
                    </div>
                    <div id="imagePreview" style="margin-top: 1rem; display: none;">
                        <img id="previewImg" style="max-width: 100%; height: 200px; object-fit: cover; border-radius: 8px;">
                        <button type="button" onclick="clearImage()" style="margin-top: 0.5rem; padding: 0.5rem 1rem; background: #ef4444; color: white; border: none; border-radius: 4px; cursor: pointer;">Hapus Gambar</button>
                    </div>
                </div>

                <hr style="margin: 1.5rem 0;">

                <div style="display: flex; gap: 1rem;">
                    <button type="button" onclick="history.back()" class="btn-buy" style="background: #64748b; flex: 1;">Batal</button>
                    <button type="submit" class="btn-buy" style="background: #10b981; flex: 2;">
                        <svg style="width: 1.25rem; height: 1.25rem; display: inline-block; vertical-align: middle; margin-right: 0.5rem;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                        </svg>
                        Simpan Barang
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Image preview
        document.getElementById('fileInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file size (2MB)
                if (file.size > 2000000) {
                    alert('Ukuran file terlalu besar! Maksimal 2MB.');
                    this.value = '';
                    return;
                }

                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewImg').src = e.target.result;
                    document.getElementById('imagePreview').style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });

        function clearImage() {
            document.getElementById('fileInput').value = '';
            document.getElementById('imagePreview').style.display = 'none';
        }

        // Form change detection
        let formChanged = false;
        document.getElementById('addProductForm').addEventListener('change', function() {
            formChanged = true;
        });

        window.addEventListener('beforeunload', function(e) {
            if (formChanged) {
                e.preventDefault();
                e.returnValue = 'Ada perubahan yang belum disimpan. Yakin ingin keluar?';
            }
        });

        document.getElementById('addProductForm').addEventListener('submit', function() {
            formChanged = false;
        });
    </script>
</body>

</html>