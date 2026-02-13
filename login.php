<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Toko Komputer Online - Sedia berbagai macam perangkat hardware berkualitas.">
	<meta name="keywords" content="komputer, laptop, hardware, e-commerce">
	<title>Login - E-Commerce</title>
	<link rel="stylesheet" href="style.css">
	<style>
		/* CSS Khusus Login agar di tengah layar */
		body {
			display: flex;
			justify-content: center;
			align-items: center;
			min-height: 100vh;
		}

		.login-box {
			background: white;
			padding: 2rem;
			border-radius: 8px;
			box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
			width: 100%;
			max-width: 400px;
		}

		.form-group {
			margin-bottom: 1rem;
		}

		.form-group label {
			display: block;
			margin-bottom: 0.5rem;
		}

		.form-group input {
			width: 100%;
			padding: 0.75rem;
			border: 1px solid #ccc;
			border-radius: 4px;
		}

		.btn-submit {
			width: 100%;
			padding: 0.75rem;
			background: var(--primary-color);
			color: white;
			border: none;
			border-radius: 4px;
			cursor: pointer;
		}

		.btn-submit:hover {
			background: var(--secondary-color);
		}

		.alert {
			color: red;
			margin-bottom: 1rem;
			text-align: center;
		}
	</style>
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

	<div class="login-box">
		<h2 style="text-align: center; margin-bottom: 1.5rem;">Login System</h2>

		<?php
		if (isset($_GET['pesan'])) {
			if ($_GET['pesan'] == "gagal") {
				echo "<div class='alert'>Username atau Password salah!</div>";
			} else if ($_GET['pesan'] == "logout") {
				echo "<div class='alert' style='color: green;'>Anda telah berhasil logout.</div>";
			} else if ($_GET['pesan'] == "belum_login") {
				echo "<div class='alert'>Silakan login terlebih dahulu.</div>";
			}
		}
		?>

		<form action="cek_login.php" method="post">
			<div class="form-group">
				<label>Username</label>
				<input type="text" name="username" required placeholder="Masukkan username">
			</div>
			<div class="form-group">
				<label>Password</label>
				<input type="password" name="password" required placeholder="Masukkan password">
			</div>
			<button type="submit" class="btn-submit">Masuk</button>
			<p style="margin-top: 1rem; text-align: center;">
				<a href="index.php" style="text-decoration: none; font-size: 0.9rem;">&larr; Kembali ke Katalog</a>
			</p>
		</form>
	</div>

</body>

</html>